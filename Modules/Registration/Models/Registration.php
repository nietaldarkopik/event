<?php
namespace Modules\Registration\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Event\Models\Event;
use Modules\Participant\Models\Participant;
use Modules\Ticket\Models\TicketType;
use Modules\Payment\Models\Payment;
use Modules\Attendance\Models\AttendanceLog;
use Modules\Certificate\Models\Certificate;
use Modules\Registration\Models\QrCode;

class Registration extends Model
{
    use \App\Models\Concerns\CascadesSoftDeletes;

    /** Turunan yang ikut di-soft-delete/restore bersama model ini. */
    protected array $softCascade = [
        [\App\Models\Payment::class, 'registration_id'],
        [\App\Models\Certificate::class, 'registration_id'],
    ];

    protected $fillable = [
        'uuid', 'event_id', 'participant_id', 'ticket_type_id', 'registration_number',
        'status', 'registered_at', 'checked_in_at', 'notes'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    public function ticketType()
    {
        return $this->belongsTo(TicketType::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function qrCode()
    {
        return $this->hasOne(QrCode::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function certificate()
    {
        return $this->hasOne(Certificate::class);
    }
}