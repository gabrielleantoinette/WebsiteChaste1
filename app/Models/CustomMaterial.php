<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'price',
        'color',
        'stock',
    ];

    public function variants()
    {
        return $this->hasMany(CustomMaterialVariant::class);
    }
}