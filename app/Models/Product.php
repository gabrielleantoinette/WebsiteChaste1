<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'image',
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }
}
