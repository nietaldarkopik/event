<?php
namespace Modules\Event\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'event_id', 'title', 'content', 'published_at'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}