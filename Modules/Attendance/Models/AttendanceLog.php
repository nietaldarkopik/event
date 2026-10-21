<?php
namespace Modules\Attendance\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Registration\Models\Registration;
use Modules\Session\Models\EventSession;
use Modules\Authentication\Models\User;

class AttendanceLog extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'registration_id', 'session_id', 'scanner_user_id', 'device', 'scan_time', 'status'
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class)->withTrashed();
    }

    public function session()
    {
        return $this->belongsTo(EventSession::class, 'session_id');
    }

    public function scanner()
    {
        return $this->belongsTo(User::class, 'scanner_user_id');
    }
}