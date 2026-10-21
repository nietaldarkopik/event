<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventRundown extends Model
{
    protected $fillable = [
        'event_id',
        'time_start',
        'time_end',
        'agenda',
        'order_number',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
