<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Product extends Model
{
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'image',
        'price',
        'min_price',
        'min_buying_stock',
        'size',
        'live',
        'category_id',
    ];

    protected $appends = [
        'image_url',
    ];

    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function category()
    {
        return $this->belongsTo(Categories::class);
    }

    public function getImageUrlAttribute(): string
    {
        if ($this->image) {
            if (Storage::disk('public')->exists($this->image)) {
                return 'storage/' . ltrim($this->image, '/');
            }

            $fileName = basename($this->image);
            $publicPath = 'images/products/' . $fileName;

            if (file_exists(public_path($publicPath))) {
                return $publicPath;
            }
        }

        return 'images/gulungan-terpal.png';
    }
}
