<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InvitationTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'is_active',
        'description',
        'config',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'config' => 'array',
    ];

    /**
     * Get default config structure for a new template
     */
    public static function getDefaultConfig()
    {
        return [
            'layout' => [
                'header_style' => 'card',
                'spacing' => 'normal',
            ],
            'colors' => [
                'primary' => '#166534',
                'secondary' => '#b8923a',
                'bg' => '#ece6d6',
                'card_bg' => '#fffdf8',
                'text' => '#3a3629',
                'title_color' => '#14532d',
            ],
            'typography' => [
                'font' => 'Cormorant Garamond',
                'title_size' => '2rem',
                'body_size' => '1rem',
                'line_height' => '1.6',
                'font_weight' => 'normal',
            ],
            'buttons' => [
                'shape' => 'rounded',
                'size' => 'medium',
                'shadow' => true,
            ],
            'sections' => [
                'show_participant_info' => true,
                'show_barcode' => true,
                'show_barcode_masuk' => true,
                'show_barcode_pulang' => false,
                'show_barcode_konsumsi' => false,
                'show_rundown' => true,
                'show_rules' => true,
            ],
            'advanced' => [
                'custom_css' => '',
                'border_radius' => '15px',
                'animation' => 'fade',
            ],
        ];
    }

    public function events()
    {
        return $this->hasMany(Event::class, 'invitation_template_id');
    }
}
