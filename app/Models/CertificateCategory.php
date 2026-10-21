<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'name',
        'slug',
        'template_id',
        'certificate_prefix',
        'auto_map_to_role',
        'mapped_role',
        'display_order',
        'is_active',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'category_id');
    }
}
