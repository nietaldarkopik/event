<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certificate extends Model
{
    protected $fillable = [
        'event_id',
        'participant_id',
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

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class);
    }
}
