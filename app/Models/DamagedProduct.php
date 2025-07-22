<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DamagedProduct extends Model
{
    protected $table = 'damaged_products';
    protected $fillable = [
        'product_id',
        'variant_id',
        'return_id',
        'quantity',
        'damage_description',
        'damage_media_path',
        'status',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
    public function retur()
    {
        return $this->belongsTo(Returns::class, 'return_id');
    }
} 
 