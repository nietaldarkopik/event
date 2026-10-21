<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /** Slug dipakai sebagai nilai pilihan kategori (template undangan, tipe event); selalu terisi & unik. */
    protected static function booted()
    {
        static::saving(function ($cat) {
            if (empty($cat->slug) && ! empty($cat->name)) {
                $base = \Illuminate\Support\Str::slug($cat->name, '_') ?: 'kategori';
                $slug = $base;
                $i = 1;
                while (\DB::table('event_categories')->where('slug', $slug)->where('id', '!=', $cat->id ?? 0)->exists()) {
                    $slug = $base . '_' . $i++;
                }
                $cat->slug = $slug;
            }
        });
    }

    public function events()
    {
        return $this->hasMany(Event::class);
    }
}
