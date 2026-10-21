<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    protected $fillable = [
        'event_id',
        'participant_id',
        'invitation_id',
        'type', // check_in, check_out, consumption
        'scanned_at',
        'device_info',
        'ip_address',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function invitation()
    {
        return $this->belongsTo(Invitation::class);
    }
}
