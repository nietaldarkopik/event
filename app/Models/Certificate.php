<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    use \App\Models\Concerns\CascadesSoftDeletes;

    protected $fillable = [
        'event_id',
        'participant_id',
        'committee_id',
        'recipient_name',
        'recipient_unit',
        'template_id',
        'category_id',
        'certificate_number',
        'unique_code',
        'certificate_hash',
        'qr_code_path',
        'pdf_path',
        'generated_at',
        'issued_at',
        'downloaded_at',
        'is_active',
        'is_revoked',
        'revoked_at',
        'revoked_by',
        'revoke_reason',
    ];

    protected $casts = [
        'generated_at' => 'datetime',
        'issued_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'revoked_at' => 'datetime',
        'is_active' => 'boolean',
        'is_revoked' => 'boolean',
    ];

    public function category()
    {
        return $this->belongsTo(CertificateCategory::class, 'category_id');
    }

    public function revokedBy()
    {
        return $this->belongsTo(User::class, 'revoked_by');
    }

    public function auditLogs()
    {
        return $this->hasMany(CertificateAuditLog::class);
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class);
    }

    /** Penerima berupa panitia (event_committees), bila bukan peserta. */
    public function committee()
    {
        return $this->belongsTo(\Modules\Event\Models\EventCommittee::class, 'committee_id');
    }

    public function isCommittee(): bool
    {
        return ! is_null($this->committee_id);
    }

    /** Nama penerima: salinan saat terbit, atau dari data peserta/panitia untuk sertifikat lama. */
    public function getDisplayNameAttribute()
    {
        return $this->recipient_name
            ?: optional($this->participant)->name
            ?: optional($this->committee)->display_name
            ?: '-';
    }

    public function getDisplayUnitAttribute()
    {
        if ($this->recipient_unit) {
            return $this->recipient_unit;
        }
        $p = $this->participant;

        return $p ? ($p->unit ?: $p->program_studi ?: $p->fakultas) : optional($this->committee)->unit;
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }
}
