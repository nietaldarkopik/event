<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Participant extends Model
{
    protected $fillable = [
        'event_id',
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
        'status', // Tambahkan ini
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
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

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
