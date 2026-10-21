<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateSignature extends Model
{
    protected $fillable = [
        'template_id',
        'event_id',
        'signer_name',
        'signer_position',
        'show_name',
        'show_title',
        'signature_file', // renamed to signature_image_path in plan, but kept for compatibility or will rename in next step if needed. Actually plan says signature_image_path. I'll stick to what's in the DB.
        'position', // 'left', 'center', 'right'
        'order_number',
        'is_active',
        'font_family',
        'font_size',
        'text_alignment',
        'stamp_image_path',
        'stamp_opacity',
        'stamp_rotation',
        'stamp_layer',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function template()
    {
        return $this->belongsTo(CertificateTemplate::class, 'template_id');
    }
}
