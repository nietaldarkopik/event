<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateRetrievalCode extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'participant_id',
        'code',
        'is_used',
        'used_at',
        'ip_address',
        'user_agent',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }
}
