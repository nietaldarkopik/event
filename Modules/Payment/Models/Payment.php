<?php
namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Modules\Registration\Models\Registration;

class Payment extends Model
{
    use \App\Models\Concerns\CascadesSoftDeletes;

    protected $fillable = [
        'registration_id', 'invoice_number', 'payment_method', 'payment_gateway',
        'transaction_id', 'amount', 'status', 'paid_at'
    ];

    public function registration()
    {
        return $this->belongsTo(Registration::class);
    }
}