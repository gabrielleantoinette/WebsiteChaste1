<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DInvoice extends Model
{
    //
    protected $table = 'dinvoice';
    protected $fillable = ['invoice_id', 'product_id', 'price', 'quantity', 'subtotal'];

    public function invoice()
    {
        return $this->belongsTo(HInvoice::class);
    }
}
