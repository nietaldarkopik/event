<?php

namespace App\Jobs;

use App\Models\Invitation;
use App\Models\Participant;
use App\Services\InvitationNotifier;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

/**
 * Kirim WhatsApp undangan (QR + info event) di latar belakang. Dipakai import CSV supaya
 * request web tidak kena batas waktu PHP saat peserta banyak. Tidak diulang otomatis
 * (tries = 1) agar peserta tidak menerima pesan ganda; kegagalan tercatat di log WA.
 */
class SendInvitationWhatsAppJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 120;

    public function __construct(
        public int $participantId,
        public int $invitationId,
        public string $waType,
        public ?string $template = null
    ) {
    }

    public function handle(): void
    {
        $participant = Participant::with('event')->find($this->participantId);
        $invitation = Invitation::find($this->invitationId);
        if (!$participant || !$invitation) {
            return; // dihapus sebelum sempat dikirim
        }

        InvitationNotifier::sendWhatsApp($participant, $invitation, $this->waType, $this->template);
    }
}
