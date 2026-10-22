<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WhatsAppController extends Controller
{
    /** Kueri log menurut filter status + pencarian (nama peserta / nomor). */
    private function filtered(Request $request)
    {
        $q = trim((string) $request->input('q', ''));

        return WaLog::query()
            ->when(in_array($request->input('status'), ['sent', 'failed', 'pending'], true), fn ($query) => $query->where('status', $request->input('status')))
            ->when($q !== '', function ($query) use ($q) {
                $like = '%' . str_replace(['%', '_'], ['\\%', '\\_'], $q) . '%';
                $query->where(fn ($w) => $w->where('phone', 'like', $like)
                    ->orWhereHas('participant', fn ($p) => $p->where('name', 'like', $like)));
            });
    }

    public function index(Request $request)
    {
        $waStatus = WhatsAppService::status();
        // Pesan lewat Blaster masuk antrean ('pending'); selesaikan statusnya sebelum angka ringkasan dihitung.
        WhatsAppService::syncBlasterStatuses(WaLog::where('status', 'pending')->latest()->limit(50)->get());
        $stats = [
            'total' => WaLog::count(),
            'sent'  => WaLog::where('status', 'sent')->count(),
            'failed'=> WaLog::where('status', 'failed')->count(),
            'pending'=> WaLog::where('status', 'pending')->count(),
        ];
        $perPage = in_array((int) $request->input('per_page'), [25, 50, 100, 200], true) ? (int) $request->input('per_page') : 25;
        $logs = $this->filtered($request)->with('participant')->latest()->paginate($perPage)->withQueryString();
        WhatsAppService::syncBlasterStatuses($logs->getCollection()); // info sampai/dibaca untuk yang sudah terkirim
        return view('admin.whatsapp.index', compact('logs', 'waStatus', 'stats'));
    }

    /**
     * Kirim ulang undangan (QR + info event) untuk log gagal/pending yang dipilih, lewat Blaster.
     * Berjalan di latar belakang (Laravel queue); satu peserta hanya sekali per aksi. Hanya jenis
     * pesan undangan/QR yang bisa direkonstruksi; jenis lain dilewati dan dilaporkan.
     * Catatan: isi pesan memakai template bawaan, bukan template khusus yang dipakai saat impor.
     */
    public function bulkResend(Request $request)
    {
        $request->validate(['ids' => 'nullable|array|max:2000', 'ids.*' => 'integer', 'all_filtered' => 'nullable|boolean']);

        $query = $request->boolean('all_filtered') ? $this->filtered($request) : WaLog::whereIn('id', (array) $request->input('ids', []));
        $logs = $query->whereIn('status', ['failed', 'pending'])->orderBy('id')->limit(2000)->get();
        $back = $request->only('status', 'q', 'per_page');

        if ($logs->isEmpty()) {
            return redirect()->route('admin.whatsapp.logs', $back)->with('error', 'Tidak ada log berstatus gagal/pending yang dipilih.');
        }

        $participants = \App\Models\Participant::with('invitation')
            ->whereIn('id', $logs->pluck('participant_id')->filter()->unique())->get()->keyBy('id');
        $resendable = ['invitation', 'invitation_qr', 'registration_approved_qr', 'payment_approved_qr'];

        $queued = 0;
        $skipped = ['type' => 0, 'participant' => 0];
        $done = [];
        $stamp = ' · dikirim ulang (antrean) ' . now()->format('d/m H:i');

        foreach ($logs as $log) {
            $base = preg_replace('/(_text|_resend)+$/', '', (string) $log->message_type);
            $p = $participants->get($log->participant_id);

            if (!in_array($base, $resendable, true)) {
                $skipped['type']++;
                continue;
            }
            if (!$p || !$p->invitation || empty($p->whatsapp) || $p->status !== 'approved') {
                $skipped['participant']++;
                continue;
            }
            if (!isset($done[$p->id])) {
                \App\Jobs\SendInvitationWhatsAppJob::dispatch($p->id, $p->invitation->id, 'invitation_resend');
                $done[$p->id] = true;
                $queued++;
            }
            if (!str_contains((string) $log->response_message, 'dikirim ulang')) {
                $log->update(['response_message' => \Illuminate\Support\Str::limit((string) $log->response_message, 400, '') . $stamp]);
            }
        }

        $msg = "{$queued} peserta dimasukkan ke antrean kirim ulang WhatsApp (dikirim bertahap lewat Blaster; pantau di halaman ini).";
        if ($skipped['type'] || $skipped['participant']) {
            $msg .= " Dilewati: {$skipped['type']} log jenis pesan yang tidak bisa dikirim ulang otomatis, {$skipped['participant']} log tanpa peserta/undangan aktif/status disetujui.";
        }

        return redirect()->route('admin.whatsapp.logs', $back)->with($queued ? 'success' : 'error', $msg);
    }

    public function resend($id)
    {
        $log = WaLog::with('participant')->findOrFail($id);

        if (!$log->participant) {
            return redirect()->back()->with('error', 'Data participant tidak ditemukan untuk log ini.');
        }

        $message = $log->response_message ? null : null;
        $success = false;

        if ($log->image_url || $log->image_url) {
            $imageUrl  = $log->image_url;
            $imagePath = null;
            if ($imageUrl && str_starts_with($imageUrl, '/storage/')) {
                $diskPath = public_path(ltrim($imageUrl, '/'));
                if (file_exists($diskPath)) {
                    $imagePath = $diskPath;
                    $imageUrl  = null;
                }
            }
            $caption = $log->message_type === 'registration_approved_qr'
                ? "Notif ulang: QR Code Kehadiran untuk {$log->participant->name}"
                : null;
            $success = WhatsAppService::sendWithImage(
                participant: $log->participant,
                type: $log->message_type.'_resend',
                message: "[RESEND] Pesan WA sebelumnya (status {$log->status}).",
                imagePath: $imagePath,
                imageUrl: $imageUrl,
                caption: $caption
            );
        } else {
            $success = WhatsAppService::send(
                participant: $log->participant,
                type: $log->message_type.'_resend',
                message: "[RESEND] Pesan WA sebelumnya (status: {$log->status})."
            );
        }

        if ($success) {
            return redirect()->route('admin.whatsapp.logs')
                ->with('success', "Pesan WA ID #{$log->id} BERHASIL dikirim ulang ke {$log->phone}.");
        }

        $lastErr = optional(WaLog::latest()->first())->response_message ?? 'Lihat log terbaru di daftar paling atas untuk detail error.';
        return redirect()->route('admin.whatsapp.logs')
            ->with('error', "Gagal kirim ulang WA ID #{$log->id}. Error terakhir: {$lastErr}");
    }
}
