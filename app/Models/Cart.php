<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $fillable = ['user_id', 'variant_id', 'quantity', 'selected_size', 'kebutuhan_custom', 'bahan_custom', 'ukuran_custom', 'warna_custom', 'jumlah_ring_custom', 'pakai_tali_custom', 'catatan_custom', 'harga_custom'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
