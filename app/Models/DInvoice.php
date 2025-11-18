<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DInvoice extends Model
{
    //
    protected $table = 'dinvoice';
    protected $fillable = ['hinvoice_id', 'product_id', 'variant_id', 'selected_size', 'price', 'quantity', 'subtotal', 'kebutuhan_custom', 'warna_custom', 'bahan_custom', 'ukuran_custom', 'jumlah_ring_custom', 'pakai_tali_custom', 'catatan_custom'];

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
