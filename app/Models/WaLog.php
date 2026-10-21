<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Modules\Participant\Models\Participant;

class WaLog extends Model
{
    protected $fillable = [
        'participant_id',
        'phone',
        'message_type',
        'status',
        'response_message',
        'image_url',
        'sent_at',
    ];

    protected $casts = [
        'sent_at' => 'datetime',
    ];

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }
}
