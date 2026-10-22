<?php

namespace App\Services;

use App\Models\AttendanceLog;
use App\Models\Event;
use App\Models\Invitation;
use App\Models\Participant;
use Carbon\Carbon;

/**
 * Aturan presensi bersama untuk tiga metode:
 *  - admin_scan : admin memindai / mengetik kode yang ditunjukkan peserta
 *  - manual     : admin mencari peserta lalu menandai hadir
 *  - self       : peserta presensi mandiri lewat QR di lokasi acara
 * Metode dicatat di kolom attendance_logs.device.
 */
class AttendanceService
{
    /** Presensi mandiri dibuka sekian menit sebelum acara mulai. */
    const SELF_OPEN_BEFORE_MIN = 60;
    /** Bila event tidak punya jam selesai, presensi mandiri ditutup sekian jam setelah mulai. */
    const SELF_FALLBACK_HOURS = 6;
    /** Satu kode QR layar berlaku 1 menit; yang sebelumnya masih diterima beberapa menit agar sempat membuka halaman. */
    const TOKEN_SLOT_SECONDS = 60;
    const TOKEN_GRACE_SLOTS = 3;
    /** Setelah QR lokasi terbukti valid, peserta punya waktu segini untuk menyelesaikan form. */
    const PROOF_MINUTES = 15;

    const METHOD_LABELS = [
        'admin_scan' => 'Scan Admin',
        'manual' => 'Manual Admin',
        'self' => 'Mandiri',
    ];

    /** Tipe "pulang" tergantung jenis event (wisuda memakai consumption). */
    public static function secondaryType(Event $event): string
    {
        return $event->event_type == 'wisuda' ? 'consumption' : 'check_out';
    }

    /**
     * Catat presensi. Melempar \RuntimeException (pesan aman ditampilkan) bila tidak boleh.
     *
     * @param  string $method  admin_scan | manual | self
     */
    public static function record(Event $event, Participant $participant, ?Invitation $invitation, string $type, string $method, ?int $userId = null, ?string $ip = null, ?string $userAgent = null): AttendanceLog
    {
        $existing = AttendanceLog::where('event_id', $event->id)
            ->where('participant_id', $participant->id)
            ->get();

        $names = ['check_in' => 'Absensi Datang', 'check_out' => 'Absensi Pulang', 'consumption' => 'Absensi Pulang'];

        if ($existing->where('type', $type)->count() > 0) {
            throw new \RuntimeException("Peserta sudah melakukan {$names[$type]}.");
        }

        if ($type != 'check_in' && $existing->where('type', 'check_in')->count() == 0) {
            throw new \RuntimeException('Peserta belum melakukan Absensi Datang. Lakukan presensi datang terlebih dahulu.');
        }

        if ($type == 'consumption' && $event->event_type != 'wisuda') {
            throw new \RuntimeException('Event ini tidak memiliki fitur Absensi Pulang jenis tersebut.');
        }
        if ($type == 'check_out' && $event->event_type == 'wisuda') {
            throw new \RuntimeException('Untuk Wisuda, gunakan Absensi Pulang yang tersedia.');
        }

        return AttendanceLog::create([
            'event_id' => $event->id,
            'participant_id' => $participant->id,
            'invitation_id' => $invitation ? $invitation->id : null,
            'type' => $type,
            'scanned_at' => now(),
            'device_info' => $userAgent ? mb_substr($userAgent, 0, 255) : null,
            'ip_address' => $ip,
            'session_id' => $participant->session_id,
            'device' => $method,
            'scanner_user_id' => $userId,
            'status' => 'SUCCESS',
        ]);
    }

    // ── Presensi mandiri: jendela waktu ─────────────────────────────────────

    /**
     * Jendela presensi mandiri. Dengan $session: jendela sesi itu. Tanpa $session pada event
     * bersesi: dari sesi paling awal sampai sesi paling akhir. Tanpa sesi: jam event.
     *
     * @return array{0:Carbon,1:Carbon}|null [buka, tutup]
     */
    public static function selfWindow(Event $event, $session = null): ?array
    {
        if ($session && $session->start_datetime) {
            $start = Carbon::parse($session->start_datetime);
            $end = $session->end_datetime ? Carbon::parse($session->end_datetime) : $start->copy()->addHours(self::SELF_FALLBACK_HOURS);

            return [self::openAt($event, $start->copy()->subMinutes(self::SELF_OPEN_BEFORE_MIN)), $end];
        }

        if (!$session && $event->session_mode !== 'none' && $event->session_mode !== null) {
            $sessions = $event->sessions()->whereNotNull('start_datetime')->get();
            if ($sessions->count()) {
                $first = Carbon::parse($sessions->min('start_datetime'));
                $last = Carbon::parse($sessions->max(fn ($x) => $x->end_datetime ?: Carbon::parse($x->start_datetime)->addHours(self::SELF_FALLBACK_HOURS)));

                return [self::openAt($event, $first->copy()->subMinutes(self::SELF_OPEN_BEFORE_MIN)), $last];
            }
        }

        if (!$event->start_date) {
            return null;
        }
        $start = Carbon::parse($event->start_date);
        $end = $event->end_date ? Carbon::parse($event->end_date) : $start->copy()->addHours(self::SELF_FALLBACK_HOURS);

        return [$start->copy()->subMinutes(self::SELF_OPEN_BEFORE_MIN), $end];
    }

    /** Jam buka presensi mandiri: yang lebih awal antara hasil hitungan (sesi - 60 menit) dan jam mulai event yang diatur admin. */
    private static function openAt(Event $event, Carbon $computed): Carbon
    {
        if ($event->start_date) {
            $eventStart = Carbon::parse($event->start_date);
            if ($eventStart->lt($computed)) {
                return $eventStart;
            }
        }

        return $computed;
    }

    /** Pesan alasan bila presensi mandiri belum/tidak bisa dipakai, null bila boleh. */
    public static function selfClosedReason(Event $event, $session = null): ?string
    {
        if ($event->status === 'DRAFT') {
            return 'Presensi untuk event ini belum dibuka.';
        }
        $w = self::selfWindow($event, $session);
        if (!$w) {
            return 'Jadwal event belum diatur, presensi mandiri belum tersedia.';
        }
        $who = $session ? ($session->title . ' ') : '';
        if (now()->lt($w[0])) {
            return 'Presensi ' . $who . 'belum dibuka. Dibuka mulai ' . $w[0]->translatedFormat('d M Y, H:i') . ' WIB.';
        }
        if (now()->gt($w[1])) {
            return 'Presensi ' . $who . 'sudah ditutup (selesai ' . $w[1]->translatedFormat('d M Y, H:i') . ' WIB). Hubungi panitia bila perlu presensi susulan.';
        }

        return null;
    }

    /** "Sesi 1 · 08.00–12.00 WIB" untuk peserta bersesi, '' bila tidak ada. */
    public static function sessionLabel($session): string
    {
        if (!$session) {
            return '';
        }
        $label = $session->title;
        if ($session->start_datetime) {
            $label .= ' · ' . Carbon::parse($session->start_datetime)->format('H.i')
                . ($session->end_datetime ? '–' . Carbon::parse($session->end_datetime)->format('H.i') : '') . ' WIB';
        }

        return $label;
    }

    // ── Presensi mandiri: kode QR layar (berganti tiap menit) ───────────────

    private static function slotToken(Event $event, int $slot): string
    {
        return substr(hash_hmac('sha256', 'attendance|' . $event->id . '|' . $slot, config('app.key')), 0, 12);
    }

    public static function currentToken(Event $event): string
    {
        return self::slotToken($event, (int) floor(time() / self::TOKEN_SLOT_SECONDS));
    }

    public static function tokenValid(Event $event, ?string $token): bool
    {
        if (!$token) {
            return false;
        }
        $now = (int) floor(time() / self::TOKEN_SLOT_SECONDS);
        for ($i = 0; $i < self::TOKEN_GRACE_SLOTS; $i++) {
            if (hash_equals(self::slotToken($event, $now - $i), $token)) {
                return true;
            }
        }

        return false;
    }

    public static function selfUrl(Event $event, ?string $token = null): string
    {
        $url = route('attendance.self', ['slug' => $event->slug]);

        return $token ? $url . '?t=' . $token : $url;
    }

    // ── Pencarian peserta untuk presensi mandiri ────────────────────────────

    /** Cari undangan aktif berdasarkan kode undangan atau nomor WhatsApp. */
    public static function findInvitation(Event $event, string $input): array
    {
        $input = trim($input);
        if (preg_match('/[?&]code=([^&]+)/', $input, $m)) {
            $input = urldecode($m[1]);
        }

        $base = Invitation::with('participant')->where('event_id', $event->id)->where('is_active', true);

        $byCode = (clone $base)->whereRaw('UPPER(unique_code) = ?', [strtoupper($input)])->first();
        if ($byCode) {
            return [$byCode, null];
        }

        $digits = preg_replace('/\D+/', '', $input);
        if (strlen($digits) >= 9) {
            $norm = self::normalizePhone($digits);
            $matches = $base->get()->filter(function ($inv) use ($norm) {
                return $inv->participant && self::normalizePhone(preg_replace('/\D+/', '', (string) $inv->participant->whatsapp)) === $norm;
            });
            if ($matches->count() === 1) {
                return [$matches->first(), null];
            }
            if ($matches->count() > 1) {
                return [null, 'Nomor ini terdaftar lebih dari sekali. Gunakan kode undangan dari WhatsApp/email Anda.'];
            }
        }

        return [null, 'Kode undangan atau nomor WhatsApp tidak ditemukan untuk event ini. Pastikan peserta Anda sudah disetujui panitia.'];
    }

    private static function normalizePhone(string $digits): string
    {
        if (strpos($digits, '0') === 0) {
            return '62' . substr($digits, 1);
        }
        if (strpos($digits, '8') === 0) {
            return '62' . $digits;
        }

        return $digits;
    }

    // ── Teks panduan (dipakai di WA, halaman publik, layar presensi) ────────

    public static function selfHintText(Event $event, ?string $code = null): string
    {
        $session = null;
        if ($code) {
            $inv = Invitation::with('participant.session')->whereRaw('UPPER(unique_code) = ?', [strtoupper($code)])->first();
            $session = $inv && $inv->participant ? $inv->participant->session : null;
        }

        $w = self::selfWindow($event, $session);
        $when = $w ? 'Presensi mandiri dibuka mulai ' . $w[0]->translatedFormat('H:i') . ' WIB sampai ' . ($session ? 'sesi Anda selesai' : 'acara selesai') . ' (boleh saat acara sudah berjalan).' : '';

        return ($session ? "🕒 *Sesi Anda:* " . self::sessionLabel($session) . "\n\n" : '')
            . "✅ *Cara presensi:*\n"
            . ($code
                ? "1️⃣ Buka tautan undangan di bawah, klik kotak *ABSENSI DATANG* (\"Klik Tampilkan\") untuk memunculkan QR Code, lalu tunjukkan ke petugas, *atau*\n"
                : "1️⃣ Tunjukkan QR Code undangan Anda ke petugas, *atau*\n")
            . "2️⃣ Presensi mandiri di lokasi: scan QR yang ditampilkan panitia di layar/meja registrasi, lalu cari nama Anda (atau masukkan kode registrasi) dan klik *Saya hadir*.\n"
            . ($when ? "🕒 {$when}\n" : '')
            . "Simpan kode undangan Anda dan jangan dibagikan ke orang lain.\n\n"
            . ($code ? "🎫 *Undangan digital, detail acara & kode Anda:*\n" . route('invitation.show', ['code' => $code]) . "\n\n" : '')
            . "ℹ️ *Panduan presensi lengkap:*\n" . route('attendance.self', ['slug' => $event->slug]);
    }
}
