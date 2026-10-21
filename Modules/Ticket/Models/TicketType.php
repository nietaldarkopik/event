<?php
namespace Modules\Ticket\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Event\Models\Event;
use Modules\Registration\Models\Registration;

class TicketType extends Model
{
    protected $fillable = [
        'event_id', 'name', 'price', 'quota', 'sale_start', 'sale_end', 'description'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}