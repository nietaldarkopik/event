<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WaLog extends Model
{
    protected $fillable = [
        'participant_id',
        'phone',
        'message_type', // invitation, reminder, certificate_link
        'status', // pending, sent, failed
        'response_message',
        'sent_at',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
