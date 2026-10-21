<?php
namespace Modules\Certificate\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Registration\Models\Registration;

class Certificate extends Model
{
    use \App\Models\Concerns\CascadesSoftDeletes;

    const UPDATED_AT = null;
    protected $fillable = [
        'event_id', 'participant_id', 'template_id', 'category_id', 'registration_id',
        'certificate_number', 'unique_code', 'certificate_hash', 'pdf_path', 'qr_code_path',
        'is_active', 'is_revoked', 'revoked_at', 'revoked_by', 'revoke_reason',
        'issued_at', 'downloaded_at'
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }

    public function participant()
    {
        return $this->hasOneThrough(
            \Modules\Participant\Models\Participant::class,
            \Modules\Registration\Models\Registration::class,
            'id',
            'id',
            'registration_id',
            'participant_id'
        );
    }

    public function event()
    {
        return $this->hasOneThrough(
            \Modules\Event\Models\Event::class,
            \Modules\Registration\Models\Registration::class,
            'id',
            'id',
            'registration_id',
            'event_id'
        );
    }
}