<?php
namespace Modules\Session\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Event\Models\Event;
use Modules\Speaker\Models\Speaker;
use Modules\Attendance\Models\AttendanceLog;

class EventSession extends Model
{
    protected $fillable = [
        'event_id', 'title', 'description', 'room', 'start_datetime', 'end_datetime', 'max_participants'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function speakers()
    {
        return $this->belongsToMany(Speaker::class, 'session_speakers', 'session_id', 'speaker_id');
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class, 'session_id');
    }
}