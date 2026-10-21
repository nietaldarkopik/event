<?php
namespace Modules\Organization\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Event\Models\Event;

class Organization extends Model
{
    protected $fillable = [
        'uuid', 'name', 'logo', 'email', 'phone', 'website', 'address'
    ];

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}