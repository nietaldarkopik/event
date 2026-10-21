<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

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
        'slug',
        'title',
        'event_type',
        'category_id',
        'short_description',
        'description',
        'rules',
        'start_date',
        'end_date',
        'start_datetime',
        'end_datetime',
        'venue_name',
        'address',
        'location_link',
        'meeting_link',
        'organizer_unit',
        'contact_whatsapp',
        'wa_pertanian',
        'wa_kehutanan',
        'wa_teknik',
        'wa_ekonomi',
        'banner_image',
        'allow_certificate',
        'is_internal',
        'is_paid',
        'price',
        'status',
        'created_by',
        'invitation_template_id',
        'has_seat_number',
        'max_participants',
        'participant_quota',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'is_internal' => 'boolean',
        'is_paid' => 'boolean',
        'has_seat_number' => 'boolean',
        'max_participants' => 'integer',
    ];

    /**
     * The `events` table stores these as `start_date`/`end_date` and
     * `max_participants` (see Modules\Event\Models\Event, the model the
     * admin CRUD actually writes through). Most of the legacy code below
     * this model reads/writes the `*_datetime`/`participant_quota` names,
     * so these aliases keep both call sites working against the one column.
     */
    public function getStartDatetimeAttribute()
    {
        return $this->start_date;
    }

    public function setStartDatetimeAttribute($value)
    {
        $this->attributes['start_date'] = $value;
    }

    public function getEndDatetimeAttribute()
    {
        return $this->end_date;
    }

    public function setEndDatetimeAttribute($value)
    {
        $this->attributes['end_date'] = $value;
    }

    public function getParticipantQuotaAttribute()
    {
        return $this->max_participants;
    }

    public function setParticipantQuotaAttribute($value)
    {
        $this->attributes['max_participants'] = $value;
    }

    /**
     * The table has no single `location` column — venue name and address are
     * stored separately. Several views display `$event->location` as one string.
     */
    public function getLocationAttribute()
    {
        return trim(($this->venue_name ?? '') . ($this->address ? ', ' . $this->address : ''), ', ') ?: null;
    }

    protected static function booted()
    {
        static::saving(function (Event $event) {
            if (empty($event->slug) && ! empty($event->title)) {
                $slug = Str::slug($event->title);
                $original = $slug;
                $i = 1;
                while (static::where('slug', $slug)->where('id', '!=', $event->id ?? 0)->exists()) {
                    $slug = "{$original}-{$i}";
                    $i++;
                }
                $event->slug = $slug;
            }
        });
    }

    /** Sesi acara (mis. Wisuda sesi 1 / 2). Tabel event_sessions dipakai bersama modul Session. */
    public function sessions()
    {
        return $this->hasMany(\Modules\Session\Models\EventSession::class, 'event_id')->orderBy('start_datetime');
    }

    /**
     * Status pendaftaran publik: open | not_open | closed | full, beserta label & pesan untuk peserta.
     * Dipakai tombol di halaman event DAN validasi di server, supaya keduanya selalu sepakat.
     */
    public function registrationState(): array
    {
        $now = now();
        $start = $this->registration_start ? \Carbon\Carbon::parse($this->registration_start) : null;
        $end = $this->registration_end ? \Carbon\Carbon::parse($this->registration_end) : null;

        if (in_array(strtoupper((string) $this->status), ['REGISTRATION_CLOSED', 'FINISHED'], true)) {
            return ['state' => 'closed', 'label' => 'Ditutup', 'message' => 'Pendaftaran untuk event ini sudah ditutup.'];
        }
        if ($start && $now->lt($start)) {
            return ['state' => 'not_open', 'label' => 'Belum Dibuka', 'message' => 'Pendaftaran belum dibuka. Dibuka mulai ' . $start->translatedFormat('d M Y, H:i') . ' WIB.'];
        }
        if ($end && $now->gt($end)) {
            return ['state' => 'closed', 'label' => 'Ditutup', 'message' => 'Pendaftaran sudah ditutup pada ' . $end->translatedFormat('d M Y, H:i') . ' WIB.'];
        }
        if (! $end && $this->end_date && $now->gt($this->end_date)) {
            return ['state' => 'closed', 'label' => 'Ditutup', 'message' => 'Acara ini sudah selesai.'];
        }
        if ($this->participant_quota && $this->participants()->count() >= $this->participant_quota) {
            return ['state' => 'full', 'label' => 'Penuh', 'message' => 'Kuota pendaftaran untuk event ini sudah penuh.'];
        }

        return ['state' => 'open', 'label' => 'Tersedia', 'message' => null];
    }

    /**
     * Item agenda untuk halaman publik & undangan. Bila event punya sesi (diisi lewat wizard),
     * agenda diambil dari sesi; kalau tidak, dari baris rundown lama.
     * Tiap item: time_start, time_end (bisa null), agenda, detail, order_number, date.
     */
    public function agendaItems()
    {
        $sessions = $this->sessions()->with('speakers')->get();

        if ($sessions->isEmpty()) {
            return $this->rundowns->sortBy('order_number')->values();
        }

        return $sessions->values()->map(function ($s, $i) {
            $parts = [];
            if ($s->room) {
                $parts[] = $s->room;
            }
            if ($s->speakers->count()) {
                $parts[] = 'Pembicara: ' . $s->speakers->pluck('name')->implode(', ');
            }

            return (object) [
                'time_start' => $s->start_datetime,
                'time_end' => $s->end_datetime,
                'agenda' => $s->title,
                'detail' => implode(' · ', $parts),
                'body' => $s->description,
                'order_number' => $i + 1,
                'date' => $s->start_datetime,
            ];
        });
    }

    public function rundowns()
    {
        return $this->hasMany(EventRundown::class);
    }

    public function participants()
    {
        return $this->hasMany(Participant::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLog::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class);
    }

    public function invitationTemplate()
    {
        return $this->belongsTo(InvitationTemplate::class, 'invitation_template_id');
    }

    public function certificateCategories()
    {
        return $this->hasMany(CertificateCategory::class);
    }

    public function certificateLogos()
    {
        return $this->hasMany(CertificateLogo::class);
    }

    public function certificateSignatures()
    {
        return $this->hasMany(CertificateSignature::class);
    }

    public function retrievalCodes()
    {
        return $this->hasMany(CertificateRetrievalCode::class);
    }

    public function certificateAuditLogs()
    {
        return $this->hasMany(CertificateAuditLog::class);
    }

    public function category()
    {
        return $this->belongsTo(EventCategory::class, 'category_id');
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

    /**
     * Resolves to the uploaded banner_image (storage path) first, then falls back
     * to the legacy `banner` column which may hold either an absolute URL or a
     * relative storage path, then null. `banner` isn't in $fillable here but the
     * column exists on the shared `events` table and is written by the admin panel.
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
}
