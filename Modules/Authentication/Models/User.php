<?php
namespace Modules\Authentication\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable, HasRoles;

    protected $guard_name = 'web';

    protected $fillable = [
        'uuid', 'role_id', 'name', 'email', 'phone', 'password', 'avatar', 'status', 'email_verified_at', 'google_id', 'unwim_id'
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Accessor for role attribute.
     */
    public function getRoleAttribute()
    {
        return $this->roles->first()?->name ?? null;
    }

    /**
     * Accessor for is_active attribute.
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'ACTIVE';
    }

    /**
     * Mutator for is_active attribute.
     */
    public function setIsActiveAttribute($value)
    {
        $this->attributes['status'] = $value ? 'ACTIVE' : 'INACTIVE';
    }
}