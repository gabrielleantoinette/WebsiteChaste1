<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PaymentModel extends Model
{
    //
    protected $table = 'payment';
    protected $fillable = [
        'invoice_id',
        'midtrans_id',
        'method',
        'type',
        'status',
        'amount',
        'snap_token',
    ];
}
