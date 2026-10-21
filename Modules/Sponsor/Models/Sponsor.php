<?php
namespace Modules\Sponsor\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Event\Models\Event;

class Sponsor extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'event_id', 'name', 'logo', 'website', 'level'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}