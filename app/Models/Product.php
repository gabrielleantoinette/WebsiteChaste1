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
        'size_prices',
        'min_price',
        'min_price_per_size',
        'min_buying_stock',
        'size',
        'live',
        'category_id',
    ];

    protected $casts = [
        'size_prices' => 'array',
        'min_price_per_size' => 'array',
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
            // Cek apakah path sudah relatif ke public (images/products/...)
            if (str_starts_with($this->image, 'images/products/')) {
                $publicPath = $this->image;
                if (file_exists(public_path($publicPath))) {
                    return $publicPath;
                }
            }
            
            // Cek apakah file ada di storage/app/public
            if (Storage::disk('public')->exists($this->image)) {
                // Jika symlink tidak tersedia, gunakan route
                $linkPath = public_path('storage');
                if (!file_exists($linkPath) || !is_link($linkPath)) {
                    // Gunakan route-based serving
                    return '/public/storage/' . ltrim($this->image, '/');
                }
                return 'storage/' . ltrim($this->image, '/');
            }

            // Coba cari di public/images/products berdasarkan filename
            $fileName = basename($this->image);
            $publicPath = 'images/products/' . $fileName;
            if (file_exists(public_path($publicPath))) {
                return $publicPath;
            }
        }

        return 'images/gulungan-terpal.png';
    }

    /**
     * Get price for specific size
     * If size_prices is set, use it. Otherwise, calculate from base price (2x3)
     */
    public function getPriceForSize(string $size): float
    {
        $sizePrices = $this->size_prices ?? [];
        
        // Jika ada harga khusus untuk ukuran ini, gunakan itu
        if (isset($sizePrices[$size]) && $sizePrices[$size] > 0) {
            return (float) $sizePrices[$size];
        }
        
        // Jika tidak ada, hitung otomatis dari harga dasar (2x3)
        $pricePerM2 = $this->price / 6; // 2x3 = 6 m2
        
        $sizeMap = [
            '2x3' => 6,   // 2 * 3 = 6 m2
            '3x4' => 12,  // 3 * 4 = 12 m2
            '4x6' => 24,  // 4 * 6 = 24 m2
            '6x8' => 48,  // 6 * 8 = 48 m2
        ];
        
        $area = $sizeMap[$size] ?? 6;
        return $pricePerM2 * $area;
    }

    /**
     * Get minimum price for negotiation for specific size
     * If min_price_per_size is set, use it. Otherwise, calculate from min_price or default to 65% of price
     */
    public function getMinPriceForSize(string $size): float
    {
        $minPricePerSize = $this->min_price_per_size ?? [];
        
        // Jika ada min_price khusus untuk ukuran ini, gunakan itu
        if (isset($minPricePerSize[$size]) && $minPricePerSize[$size] > 0) {
            return (float) $minPricePerSize[$size];
        }
        
        // Jika tidak ada, hitung dari min_price global atau default
        $baseMinPrice = $this->min_price ?? ($this->price * 0.65);
        $priceForSize = $this->getPriceForSize($size);
        
        // Hitung proporsi min_price terhadap harga dasar (2x3)
        $basePrice = $this->price; // harga 2x3
        $ratio = $basePrice > 0 ? $baseMinPrice / $basePrice : 0.65;
        
        return $priceForSize * $ratio;
    }
}
