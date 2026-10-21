<?php
namespace Modules\Participant\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Authentication\Models\User;
use Modules\Registration\Models\Registration;

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
        'user_id', 'uuid', 'name', 'full_name', 'gender', 'birth_date', 'institution', 'profession', 'whatsapp', 'phone', 'address', 'photo'
    ];

    // The `participants` table carries both the legacy full_name/phone columns
    // and the name/whatsapp columns actually populated by the live registration
    // flow (App\Models\Participant). Prefer the populated ones.
    public function getWhatsappAttribute()
    {
        return $this->attributes['whatsapp'] ?? $this->attributes['phone'] ?? null;
    }

    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->attributes['full_name'] ?? null;
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }
}