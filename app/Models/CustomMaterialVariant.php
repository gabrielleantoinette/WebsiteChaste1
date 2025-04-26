<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomMaterialVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_material_id',
        'color',
        'stock',
    ];

    public function material()
    {
        return $this->belongsTo(CustomMaterial::class, 'custom_material_id');
    }
}