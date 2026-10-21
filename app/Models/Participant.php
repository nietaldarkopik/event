<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    use \App\Models\Concerns\CascadesSoftDeletes;

    /** Turunan yang ikut di-soft-delete/restore bersama model ini. */
    protected array $softCascade = [
        [\App\Models\Invitation::class, 'participant_id'],
        [\App\Models\Registration::class, 'participant_id'],
        [\App\Models\Certificate::class, 'participant_id'],
        [\App\Models\Payment::class, 'participant_id'],
    ];

    protected $fillable = [
        'event_id',
        'session_id',
        'name',
        'whatsapp',
        'email',
        'type', // mahasiswa, dosen, tendik, umum
        'nim',
        'program_studi',
        'fakultas',
        'jabatan',
        'unit',
        'unit_id',
        'seat_number',
        'certificate_category_id',
        'status',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    /** Sesi acara yang diikuti peserta (null bila event tanpa sesi / belum ditetapkan). */
    public function session()
    {
        return $this->belongsTo(\Modules\Session\Models\EventSession::class, 'session_id');
    }

    // Named unitRelation (not unit) because the `unit` column shadows the
    // relationship name in Eloquent's attribute resolution.
    public function unitRelation()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function certificateCategory()
    {
        return $this->belongsTo(CertificateCategory::class, 'certificate_category_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function retrievalCode()
    {
        return $this->hasOne(CertificateRetrievalCode::class);
    }

    public function certificateAuditLogs()
    {
        return $this->hasMany(CertificateAuditLog::class);
    }

    public function invitation()
    {
        return $this->hasOne(Invitation::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    // payments belong to a registration, not directly to a participant
    // (the `payments` table only has a `registration_id` column).
    public function payments()
    {
        return $this->hasManyThrough(
            Payment::class,
            Registration::class,
            'participant_id',
            'registration_id'
        );
    }
}
