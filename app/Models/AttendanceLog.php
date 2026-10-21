<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceLog extends Model
{
    // The `attendance_logs` table only has `created_at` (append-only log,
    // no `updated_at` column), so Eloquent's default timestamp handling
    // would fail on insert/update.
    public $timestamps = false;

    protected $fillable = [
        'event_id',
        'participant_id',
        'invitation_id',
        'type', // check_in, check_out, consumption
        'scanned_at',
        'device_info',
        'ip_address',
        'session_id',
        'device',          // metode: admin_scan | manual | self
        'scanner_user_id',
        'status',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    // Relasi ke data yang bisa di-soft-delete memakai withTrashed() agar riwayat
    // presensi tetap menampilkan nama peserta/event yang sudah dihapus.
    public function event()
    {
        return $this->belongsTo(Event::class)->withTrashed();
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class)->withTrashed();
    }

    public function invitation()
    {
        return $this->belongsTo(Invitation::class)->withTrashed();
    }
}
