<?php

namespace App\Services;

use App\Models\WaLog;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private static $secret;

    /** Get base URL from config */
    private static function baseUrl(): string
    {
        return config('services.wa_service.url', 'http://127.0.0.1:3300');
    }

    /** Check if WA notifications are enabled */
    private static function isEnabled(): bool
    {
        return (bool) config('services.wa_service.enabled', true);
    }

    /**
     * Send a plain text WhatsApp message.
     *
     * @param  object $participant  Must have ->whatsapp and ->id
     * @param  string $type        Message type label (for logging)
     * @param  string $message     Plain text body
     * @return bool
     */
    public static function send($participant, string $type, string $message): bool
    {
        if (!self::isEnabled()) {
            Log::info('[WA] Notifications disabled. Skipping send.', ['phone' => $participant->whatsapp]);
            return false;
        }

        $log = self::createLog($participant, $type);

        try {
            if (self::useBlaster()) {
                return self::sendViaBlaster($participant, $log, $message);
            }

            $response = Http::timeout(10)
                ->withToken(self::secret())
                ->post(self::baseUrl() . '/reply', array_filter([
                    'to'      => $participant->whatsapp,
                    'message' => $message,
                    'account' => config('services.wa_blaster.account') ?: null,
                ]));

            if ($response->successful() && ($response->json('ok') === true)) {
                $log->update(['status' => 'sent', 'response_message' => 'ok']);
                return true;
            }

            $errorMsg = $response->json('error') ?? $response->body();
            $log->update(['status' => 'failed', 'response_message' => $errorMsg]);
            Log::warning('[WA] Send failed', ['phone' => $participant->whatsapp, 'error' => $errorMsg]);
            return false;

        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'response_message' => 'Exception: ' . $e->getMessage()]);
            Log::error('[WA] Exception during send', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Kirim teks lewat Blaster API v1. Pesan masuk antrean (202), jadi statusnya 'pending'
     * sampai blaster mengirimnya; id pesan disimpan di response_message untuk pelacakan.
     */
    private static function sendViaBlaster($participant, WaLog $log, string $message, ?string $imageUrl = null, ?string $caption = null): bool
    {
        $payload = array_filter([
            'to'            => $participant->whatsapp,
            'message'       => $message,
            'account'       => self::pickBlasterAccount($imageUrl && $caption !== $message ? 2 : 1),
            'image_url'     => $imageUrl,
            'image_caption' => $caption,
        ]);

        // Kena batas kecepatan (429 + Retry-After): tunggu sebentar lalu coba lagi, maksimal 3 kali.
        for ($attempt = 0; ; $attempt++) {
            $response = Http::timeout(15)
                ->withToken(config('services.wa_blaster.key'))
                ->acceptJson()
                ->post(rtrim(config('services.wa_blaster.url'), '/') . '/messages', $payload);

            $wait = (int) $response->header('Retry-After');
            if ($response->status() === 429 && $wait > 0 && $attempt < 3) {
                sleep(min($wait, 15));
                continue;
            }
            break;
        }

        if ($response->status() === 202 && $response->json('ok') === true) {
            $log->update([
                'status'           => 'pending',
                'response_message' => 'queued id=' . $response->json('id') . ' posisi=' . $response->json('position'),
            ]);
            return true;
        }

        $errorMsg = $response->json('error') ?? $response->body();
        if ($response->status() === 429 && $response->header('Retry-After')) {
            $errorMsg .= ' (coba lagi dalam ' . $response->header('Retry-After') . ' detik)';
        }
        $log->update(['status' => 'failed', 'response_message' => 'HTTP ' . $response->status() . ': ' . $errorMsg]);
        Log::warning('[WA] Blaster send failed', ['phone' => $participant->whatsapp, 'status' => $response->status(), 'error' => $errorMsg]);
        return false;
    }

    /**
     * Perbarui log berstatus 'pending' yang berasal dari antrean Blaster (response_message "queued id=...")
     * dengan menanyakan status pesannya ke Blaster: sent -> 'sent', failed/cancelled -> 'failed'.
     * Gagal menghubungi Blaster tidak mengubah apa pun (tetap 'pending').
     */
    public static function syncBlasterStatuses($logs): void
    {
        if (!self::useBlaster()) {
            return;
        }
        $base = rtrim(config('services.wa_blaster.url'), '/');

        foreach ($logs as $log) {
            $tracked = $log->status === 'pending' && preg_match('/^queued id=(\w+)/', (string) $log->response_message, $m);
            // Yang sudah 'sent' tetap dipantau sampai terbaca (maks. 3 hari), untuk info sampai/dibaca.
            if (!$tracked && !($log->status === 'sent' && $log->created_at && $log->created_at->gt(now()->subDays(3))
                && preg_match('/^ok \(id=(\w+)\)(?!.*Dibaca)/', (string) $log->response_message, $m))) {
                continue;
            }
            try {
                $r = Http::timeout(5)->withToken(config('services.wa_blaster.key'))->acceptJson()->get($base . '/messages/' . $m[1]);
            } catch (\Throwable $e) {
                Log::warning('[WA] Blaster status sync failed', ['error' => $e->getMessage()]);
                return; // Blaster tidak terjangkau: jangan tunggu timeout untuk tiap baris
            }
            if ($r->status() === 404) {
                $log->update(['status' => 'failed', 'response_message' => 'Pesan tidak ditemukan di antrean Blaster (id=' . $m[1] . ')']);
                continue;
            }
            $status = $r->json('status');
            if ($status === 'sent') {
                $info = $r->json('delivery') === 'read' ? ' · Dibaca ' . date('d/m H:i', intdiv((int) $r->json('readAt'), 1000))
                    : ($r->json('delivery') === 'delivered' ? ' · Sampai di HP ' . date('H:i', intdiv((int) $r->json('deliveredAt'), 1000)) : '');
                $msg = 'ok (id=' . $m[1] . ')' . $info;
                if ($log->status !== 'sent' || $log->response_message !== $msg) {
                    $log->update(['status' => 'sent', 'response_message' => $msg, 'sent_at' => $log->sent_at ?: now()]);
                }
            } elseif (in_array($status, ['failed', 'cancelled'], true)) {
                $log->update(['status' => 'failed', 'response_message' => ($r->json('error') ?: $status) . ' (id=' . $m[1] . ')']);
            }
        }
    }

    /** Id akun Blaster yang dipakai mengirim (WA_BLASTER_ACCOUNTS, atau WA_BLASTER_ACCOUNT). Kosong = default Blaster. */
    private static function blasterAccountIds(): array
    {
        $list = array_values(array_filter(array_map('trim', explode(',', (string) config('services.wa_blaster.accounts')))));
        if ($list) {
            return $list;
        }
        $one = config('services.wa_blaster.account');

        return $one ? [$one] : [];
    }

    /**
     * Pilih nomor pengirim. Satu nomor: langsung dipakai. Beberapa nomor: pilih yang tersambung dengan
     * sisa kuota hari ini (kuota - terpakai - antre) terbesar, supaya beban terbagi sesuai kapasitas
     * masing-masing dan pesan tidak menumpuk di nomor yang penuh/mati. $weight = jumlah chat pesan ini.
     */
    private static function pickBlasterAccount(int $weight = 1): ?string
    {
        $ids = self::blasterAccountIds();
        if (count($ids) <= 1) {
            return $ids[0] ?? null;
        }

        $remaining = Cache::get('wa_blaster_remaining');
        if (!is_array($remaining)) {
            $remaining = [];
            try {
                $r = Http::timeout(5)->withToken(config('services.wa_blaster.key'))->acceptJson()
                    ->get(rtrim(config('services.wa_blaster.url'), '/') . '/accounts');
                foreach ((array) $r->json('accounts') as $a) {
                    if (in_array($a['id'], $ids, true) && !empty($a['connected'])) {
                        $q = $a['quota'] ?? [];
                        $remaining[$a['id']] = (int) ($q['limit'] ?? 0) - (int) ($q['used'] ?? 0) - (int) ($q['queued'] ?? 0);
                    }
                }
            } catch (\Exception $e) {
                Log::warning('[WA] Blaster accounts check failed (pick account)', ['error' => $e->getMessage()]);
            }
        }

        if (!$remaining) {
            return $ids[0]; // tak ada yang tersambung / Blaster tak terjangkau: biarkan Blaster yang menolak
        }
        arsort($remaining);
        $pick = array_key_first($remaining);
        $remaining[$pick] -= $weight; // perkiraan lokal sampai cache diperbarui
        Cache::put('wa_blaster_remaining', $remaining, 20);

        return $pick;
    }

    /** Info nomor pengirim dari Blaster API (status koneksi + sisa kuota), null bila tidak ditemukan. */
    private static function blasterAccount(): ?array
    {
        try {
            $r = Http::timeout(8)->withToken(config('services.wa_blaster.key'))->acceptJson()
                ->get(rtrim(config('services.wa_blaster.url'), '/') . '/accounts');
            if (!$r->successful()) {
                return null;
            }
            $ids = self::blasterAccountIds();
            $found = null;
            foreach ((array) $r->json('accounts') as $a) {
                if (!$ids || in_array($a['id'], $ids, true)) {
                    if (!empty($a['connected'])) {
                        return $a; // cukup satu nomor yang tersambung
                    }
                    $found ??= $a;
                }
            }
            return $found;
        } catch (\Exception $e) {
            Log::warning('[WA] Blaster accounts check failed', ['error' => $e->getMessage()]);
        }
        return null;
    }

    private static function useBlaster(): bool
    {
        return !empty(config('services.wa_blaster.key'));
    }

    /**
     * Send a WhatsApp message with an image attachment (for QR barcode).
     *
     * @param  object      $participant  Must have ->whatsapp and ->id
     * @param  string      $type        Message type label (for logging)
     * @param  string      $message     Text message (sent after image)
     * @param  string|null $imagePath   Absolute server path to the image file (preferred)
     * @param  string|null $imageUrl    Public URL of image (fallback)
     * @param  string|null $caption     Caption under the image
     * @return bool
     */
    public static function sendWithImage(
        $participant,
        string $type,
        string $message,
        ?string $imagePath = null,
        ?string $imageUrl  = null,
        ?string $caption   = null
    ): bool {
        if (!self::isEnabled()) {
            Log::info('[WA] Notifications disabled. Skipping sendWithImage.', ['phone' => $participant->whatsapp]);
            return false;
        }

        if (!$imagePath && !$imageUrl) {
            // Fallback: send as text only
            return self::send($participant, $type, $message);
        }

        // Satu chat saja: isi pesan lengkap jadi caption gambar (batas caption WhatsApp 1024 karakter).
        // Bila terlalu panjang, tetap dua chat: gambar dengan caption singkat, lalu teks.
        if (mb_strlen($message) <= 1024) {
            $caption = $message;
        }

        $log = self::createLog($participant, $type, $imageUrl ?? $imagePath);

        // Lewat Blaster: gambar + teks jadi satu item antrean (gambar dulu, jeda singkat, lalu teks),
        // sehingga ikut jeda/kuota dan statusnya terlacak. Butuh URL gambar publik (https).
        if (self::useBlaster() && $imageUrl && str_starts_with($imageUrl, 'https://')) {
            try {
                return self::sendViaBlaster($participant, $log, $message, $imageUrl, $caption ?? $message);
            } catch (\Exception $e) {
                $log->update(['status' => 'failed', 'response_message' => 'Exception: ' . $e->getMessage()]);
                Log::error('[WA] Exception during sendWithImage (blaster)', ['error' => $e->getMessage()]);
                return false;
            }
        }

        $payload = [
            'to'      => $participant->whatsapp,
            'message' => $message,
            'caption' => $caption ?? $message,
        ];

        // API v1 belum mendukung gambar, jadi QR tetap lewat gateway /reply, dari nomor pengirim yang sama.
        if ($account = config('services.wa_blaster.account')) {
            $payload['account'] = $account;
        }

        if ($imagePath) {
            $payload['image_path'] = $imagePath;
        } else {
            $payload['image_url'] = $imageUrl;
        }

        try {
            $response = Http::timeout(30)   // Larger timeout for image loading
                ->withToken(self::secret())
                ->post(self::baseUrl() . '/reply', $payload);

            if ($response->successful() && ($response->json('ok') === true)) {
                $log->update(['status' => 'sent', 'response_message' => 'ok']);

                // Gateway hanya mengirim `caption` bersama gambar; `message` diabaikan. Bila caption
                // berbeda (biasanya singkat), isi pesan lengkap harus dikirim sebagai teks menyusul.
                if ($caption !== null && trim($caption) !== trim($message)) {
                    self::send($participant, $type . '_text', $message);
                }

                return true;
            }

            $errorMsg = $response->json('error') ?? $response->body();
            $log->update(['status' => 'failed', 'response_message' => $errorMsg]);
            Log::warning('[WA] SendWithImage failed', ['phone' => $participant->whatsapp, 'error' => $errorMsg]);
            return false;

        } catch (\Exception $e) {
            $log->update(['status' => 'failed', 'response_message' => 'Exception: ' . $e->getMessage()]);
            Log::error('[WA] Exception during sendWithImage', ['error' => $e->getMessage()]);
            return false;
        }
    }

    /**
     * Check if the local WA service is up and connected.
     */
    public static function isReady(): bool
    {
        if (self::useBlaster()) {
            return (self::blasterAccount()['connected'] ?? false) === true;
        }

        try {
            $response = Http::timeout(5)->get(self::baseUrl() . '/health');
            return $response->successful() && ($response->json('connected') === true);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Get WA service status details. 'ready' is kept for backward
     * compatibility with existing views; it mirrors the gateway's 'connected'.
     */
    public static function status(): array
    {
        if (self::useBlaster()) {
            $acc = self::blasterAccount();
            if ($acc === null) {
                return ['ready' => false, 'connected' => false, 'error' => 'Nomor pengirim tidak ditemukan / API key tidak valid'];
            }
            return ['ready' => (bool) $acc['connected'], 'connected' => (bool) $acc['connected'], 'number' => $acc['number'] ?? null, 'quota' => $acc['quota'] ?? null];
        }

        try {
            $response = Http::timeout(5)->get(self::baseUrl() . '/health');
            if ($response->successful()) {
                $connected = $response->json('connected') === true;
                return ['ready' => $connected, 'connected' => $connected];
            }
            return ['ready' => false, 'error' => 'HTTP ' . $response->status()];
        } catch (\Exception $e) {
            return ['ready' => false, 'error' => $e->getMessage()];
        }
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private static function createLog($participant, string $type, ?string $imageUrl = null): WaLog
    {
        $fillable = [
            'participant_id'   => $participant->id,
            'phone'            => $participant->whatsapp,
            'message_type'     => $type,
            'status'           => 'pending',
            'sent_at'          => now(),
        ];

        // Only set image_url if column exists (backward-compatible)
        if (\Schema::hasColumn('wa_logs', 'image_url')) {
            $fillable['image_url'] = $imageUrl;
        }

        return WaLog::create($fillable);
    }

    private static function secret(): string
    {
        if (empty(self::$secret)) {
            self::$secret = config('services.wa_service.secret', '');
        }
        return self::$secret;
    }
}
