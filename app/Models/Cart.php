<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    protected $table = 'cart';
    protected $fillable = ['user_id', 'variant_id', 'quantity'];

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}
