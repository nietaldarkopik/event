<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateTemplate extends Model
{
    protected $fillable = [
        'name',
        'event_type', // seminar, workshop, wisuda
        'orientation', // landscape, portrait
        'layout', // center, left
        'background_file',
        'font_title',
        'font_body',
        'text_color',
        'layout_json',
        'is_locked',
        'is_default',
        'created_by',
    ];

    protected $casts = [
        'layout_json' => 'array',
        'is_locked' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function categories()
    {
        return $this->hasMany(CertificateCategory::class, 'template_id');
    }

    public function signatures()
    {
        return $this->hasMany(CertificateSignature::class, 'template_id');
    }

    public function certificates()
    {
        return $this->hasMany(Certificate::class, 'template_id');
    }
}
