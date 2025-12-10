<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NegotiationTable extends Model
{
    protected $table = 'negotiation_tables';

    // allow mass‐assignment on these columns:
    protected $fillable = [
        'user_id','product_id','status','final_price',
        'cust_nego_1','seller_nego_1',
        'cust_nego_2','seller_nego_2',
        'cust_nego_3','seller_nego_3',
      ];

    /**
     * Get the customer that owns the negotiation
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'user_id');
    }

    /**
     * Get the product being negotiated
     */
    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Get original price (harga asli) - product's normal price
     * Since we don't store the negotiated size, we estimate from the negotiation offers
     */
    public function getOriginalPriceAttribute()
    {
        if (!$this->product) {
            return 0;
        }
        
        // Get all seller offers to estimate the size being negotiated
        $sellerOffers = array_filter([
            $this->seller_nego_1,
            $this->seller_nego_2,
            $this->seller_nego_3
        ]);
        
        if (!empty($sellerOffers)) {
            // Use the highest seller offer to estimate original price
            // The seller offer is usually close to the original price
            $highestSellerOffer = max($sellerOffers);
            
            // Try to match with product prices for different sizes
            $sizes = ['2x3', '3x4', '4x6', '6x8'];
            foreach ($sizes as $size) {
                $priceForSize = $this->product->getPriceForSize($size);
                // If seller offer is close to this size's price (within 10%), use it
                if (abs($priceForSize - $highestSellerOffer) / $priceForSize < 0.1) {
                    return $priceForSize;
                }
            }
            
            // If no match, use the highest seller offer as original price
            // (seller offers are usually close to original price)
            return $highestSellerOffer;
        }
        
        // Fallback to product base price (2x3)
        return $this->product->getPriceForSize('2x3');
    }

    /**
     * Get negotiated price (harga negosiasi) - final price or last seller offer
     */
    public function getNegotiatedPriceAttribute()
    {
        // If final_price is set, use it
        if ($this->final_price && $this->final_price > 0) {
            return $this->final_price;
        }
        
        // Otherwise, use the last seller offer
        if ($this->seller_nego_3) {
            return $this->seller_nego_3;
        }
        if ($this->seller_nego_2) {
            return $this->seller_nego_2;
        }
        if ($this->seller_nego_1) {
            return $this->seller_nego_1;
        }
        
        return 0;
    }
}
