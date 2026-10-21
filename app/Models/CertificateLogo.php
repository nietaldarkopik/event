<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateLogo extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'logo_type',
        'image_path',
        'display_order',
        'is_primary',
        'width',
        'height',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}
