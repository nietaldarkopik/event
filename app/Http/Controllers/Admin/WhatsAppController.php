<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WaLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class WhatsAppController extends Controller
{
    public function index()
    {
        $waStatus = WhatsAppService::status();
        $stats = [
            'total' => WaLog::count(),
            'sent'  => WaLog::where('status', 'sent')->count(),
            'failed'=> WaLog::where('status', 'failed')->count(),
            'pending'=> WaLog::where('status', 'pending')->count(),
        ];
        $logs = WaLog::with('participant')->latest()->paginate(25);
        return view('admin.whatsapp.index', compact('logs', 'waStatus', 'stats'));
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
