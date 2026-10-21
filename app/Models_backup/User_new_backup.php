<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles;

    protected $guard_name = 'web';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'role_id',
        'status',
        'google_id',
        'unwim_id',
        'avatar',
        'phone',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

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

    /**
     * Override morph class so Spatie permissions lookup matches auth model morph class
     */
    public function getMorphClass()
    {
        return \Modules\Authentication\Models\User::class;
    }
}
