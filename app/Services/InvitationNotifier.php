<?php

namespace App\Services;

use App\Http\Controllers\Admin\ParticipantController;
use App\Models\Invitation;
use App\Models\Participant;

/**
 * Notifikasi undangan ke peserta (WhatsApp + QR). Dipakai bersama oleh input admin,
 * import, approve, kirim ulang, dan pendaftaran mandiri di halaman publik, supaya
 * semua jalur mengirim pesan yang sama.
 */
class InvitationNotifier
{
    public static function sendWhatsApp(Participant $participant, Invitation $invitation, string $waType, ?string $template = null): void
    {
        $qr = QrCodeImageService::generateAndSave($invitation->unique_code);
        $message = self::buildMessage($participant, $invitation, $template);
        $event = $participant->event;

        // Pakai URL publik, bukan path lokal: gateway WA berjalan sebagai user lain.
        WhatsAppService::sendWithImage(
            $participant,
            $waType,
            $message,
            null,
            $qr['url'],
            "QR Code Presensi — {$event->title} | Kode: {$invitation->unique_code}"
        );
    }

    /** Isi template pesan (opsional hasil edit admin); default memakai DEFAULT_WA_TEMPLATE. */
    public static function buildMessage(Participant $participant, Invitation $invitation, ?string $template = null): string
    {
        $event = $participant->event;
        $eventLink = $event->slug ? route('events.show', $event->slug) : url('/');
        $eventDate = $event->event_date ?? $event->date ?? $event->start_date ?? null;
        $location = $event->location ?? $event->venue ?? null;

        return strtr($template ?: ParticipantController::DEFAULT_WA_TEMPLATE, [
            '{nama}' => $participant->name,
            '{event}' => $event->title,
            '{tanggal}' => $eventDate ? \Carbon\Carbon::parse($eventDate)->translatedFormat('l, d F Y') : '-',
            '{lokasi}' => $location ?: '-',
            '{kode}' => $invitation->unique_code,
            '{link}' => $eventLink,
            '{presensi}' => AttendanceService::selfHintText($event, $invitation->unique_code),
        ]);
    }
}
