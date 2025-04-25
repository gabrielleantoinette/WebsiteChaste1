<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DInvoice extends Model
{
    //
    protected $table = 'dinvoice';
    protected $fillable = ['hinvoice_id', 'product_id', 'variant_id', 'price', 'quantity', 'subtotal'];

    public function invoice()
    {
        return $this->belongsTo(HInvoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}
