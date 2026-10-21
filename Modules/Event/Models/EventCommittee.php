<?php
namespace Modules\Event\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Authentication\Models\User;

class EventCommittee extends Model
{
    const UPDATED_AT = null;
    protected $fillable = [
        'event_id', 'user_id', 'name', 'unit', 'position', 'section', 'sort_order'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /** Nama tampil: nama yang diketik, atau nama akun bila terhubung ke pengguna. */
    public function getDisplayNameAttribute()
    {
        return $this->name ?: optional($this->user)->name;
    }
}
