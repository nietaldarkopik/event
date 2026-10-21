<?php
namespace Modules\Event\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Organization\Models\Organization;
use Modules\Category\Models\EventCategory;
use Modules\Authentication\Models\User;
use Modules\Session\Models\EventSession;
use Modules\Ticket\Models\TicketType;
use Modules\Sponsor\Models\Sponsor;
use Modules\Gallery\Models\Gallery;
use Modules\Registration\Models\Registration;

class Event extends Model
{
    use \App\Models\Concerns\CascadesSoftDeletes;

    /** Turunan yang ikut di-soft-delete/restore bersama model ini. */
    protected array $softCascade = [
        [\App\Models\Participant::class, 'event_id'],
        [\App\Models\Invitation::class, 'event_id'],
        [\App\Models\Registration::class, 'event_id'],
        [\App\Models\Certificate::class, 'event_id'],
        [\App\Models\Payment::class, 'event_id'],
    ];

    protected $fillable = [
        'uuid', 'organization_id', 'category_id', 'created_by', 'title', 'slug',
        'short_description', 'description', 'contact_whatsapp', 'banner', 'banner_image', 'venue_name', 'address',
        'city', 'province', 'latitude', 'longitude', 'registration_start',
        'registration_end', 'start_date', 'end_date', 'max_participants',
        'certificate_enabled', 'attendance_required', 'visibility', 'status'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'registration_start' => 'datetime',
        'registration_end' => 'datetime',
        'certificate_enabled' => 'boolean',
        'attendance_required' => 'boolean',
    ];

    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function sessions()
    {
        return $this->hasMany(EventSession::class);
    }

    public function ticketTypes()
    {
        return $this->hasMany(TicketType::class);
    }

    public function sponsors()
    {
        return $this->hasMany(Sponsor::class);
    }

    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    public function announcements()
    {
        return $this->hasMany(Announcement::class);
    }

    public function committees()
    {
        return $this->hasMany(EventCommittee::class);
    }

    /**
     * Pendaftar sebenarnya: peserta yang mendaftar lewat halaman publik / admin disimpan di tabel
     * `participants` (bukan `registrations`, yang dipakai modul registrasi terpisah dan masih kosong).
     */
    public function participantRecords()
    {
        return $this->hasMany(\App\Models\Participant::class, 'event_id');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function rundowns()
    {
        return $this->hasMany(EventSession::class, 'event_id');
    }

    public function participants()
    {
        return $this->hasManyThrough(
            \Modules\Participant\Models\Participant::class,
            \Modules\Registration\Models\Registration::class,
            'event_id',
            'id',
            'id',
            'participant_id'
        );
    }

    public function attendanceLogs()
    {
        return $this->hasManyThrough(
            \Modules\Attendance\Models\AttendanceLog::class,
            \Modules\Registration\Models\Registration::class,
            'event_id',
            'registration_id',
            'id',
            'id'
        );
    }

    public function payments()
    {
        return $this->hasManyThrough(
            \Modules\Payment\Models\Payment::class,
            \Modules\Registration\Models\Registration::class,
            'event_id',
            'registration_id',
            'id',
            'id'
        );
    }

    public function certificates()
    {
        return $this->hasManyThrough(
            \Modules\Certificate\Models\Certificate::class,
            \Modules\Registration\Models\Registration::class,
            'event_id',
            'registration_id',
            'id',
            'id'
        );
    }

    public function certificateCategories()
    {
        return $this->hasMany(\App\Models\CertificateCategory::class, 'event_id');
    }

    public function certificateLogos()
    {
        return $this->hasMany(\App\Models\CertificateLogo::class, 'event_id');
    }

    public function certificateSignatures()
    {
        return $this->hasMany(\App\Models\CertificateSignature::class, 'event_id');
    }

    public function retrievalCodes()
    {
        return $this->hasMany(\App\Models\CertificateRetrievalCode::class, 'event_id');
    }

    public function certificateAuditLogs()
    {
        return $this->hasMany(\App\Models\CertificateAuditLog::class, 'event_id');
    }

    public function invitationTemplate()
    {
        return $this->belongsTo(\App\Models\InvitationTemplate::class, 'invitation_template_id');
    }

    public function getStartDatetimeAttribute()
    {
        return $this->start_date;
    }

    public function getEndDatetimeAttribute()
    {
        return $this->end_date;
    }

    public function getEventTypeAttribute()
    {
        return $this->category ? strtolower($this->category->name) : 'seminar';
    }

    /**
     * There's no single `location` column — venue name and address are
     * stored separately. Some admin views display `$event->location` as one string.
     */
    public function getLocationAttribute()
    {
        return trim(($this->venue_name ?? '') . ($this->address ? ', ' . $this->address : ''), ', ') ?: null;
    }

    /**
     * Resolves to the uploaded banner_image (storage path) first, then falls back
     * to the legacy `banner` field which may hold either an absolute URL or a
     * relative storage path, then null.
     */
    public function getBannerUrlAttribute()
    {
        if ($this->banner_image) {
            return asset('storage/' . $this->banner_image);
        }

        if ($this->banner) {
            return preg_match('#^https?://#i', $this->banner)
                ? $this->banner
                : asset('storage/' . $this->banner);
        }

        return null;
    }

    public static function getEventTypes()
    {
        return [
            'rapat' => 'Rapat Internal',
            'seminar' => 'Seminar',
            'workshop' => 'Workshop',
            'wisuda' => 'Wisuda',
            'kuliah_umum' => 'Kuliah Umum',
            'lomba' => 'Lomba / Kompetisi',
            'pelatihan' => 'Pelatihan',
            'pengabdian' => 'Pengabdian Masyarakat',
        ];
    }
}