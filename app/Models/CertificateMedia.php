<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificateMedia extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'file_path',
        'thumbnail_path',
        'is_global',
    ];

    /**
     * Get the URL for the file.
     */
    public function getUrlAttribute()
    {
        return asset('storage/' . $this->file_path);
    }
}
