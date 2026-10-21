<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Invitation; // Import model Invitation

class EventInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invitation; // Deklarasikan properti publik untuk Invitation

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Invitation $invitation) // Terima objek Invitation
    {
        $this->invitation = $invitation;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Undangan Event: ' . $this->invitation->event->title) // Atur subjek email
                    ->view('emails.event_invitation'); // Gunakan view 'emails.event_invitation'
    }
}
