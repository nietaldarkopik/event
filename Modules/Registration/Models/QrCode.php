<?php
namespace Modules\Registration\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $table = 'qr_codes';
    const UPDATED_AT = null;
    protected $fillable = [
        'registration_id', 'code', 'expired_at', 'is_used'
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}