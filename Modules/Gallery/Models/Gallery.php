<?php
namespace Modules\Gallery\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Event\Models\Event;

class Gallery extends Model
{
    protected $table = 'galleries';
    const UPDATED_AT = null;
    protected $fillable = [
        'event_id', 'title', 'image', 'sort_order'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}