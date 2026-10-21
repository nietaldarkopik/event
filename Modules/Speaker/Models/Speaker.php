<?php
namespace Modules\Speaker\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Session\Models\EventSession;

class Speaker extends Model
{
    protected $fillable = [
        'uuid', 'name', 'company', 'title', 'photo', 'bio', 'linkedin', 'instagram', 'email', 'phone'
    ];

    public function sessions()
    {
        return $this->belongsToMany(EventSession::class, 'session_speakers', 'speaker_id', 'session_id');
    }
}