<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = ['name', 'color', 'unit', 'default_price', 'stock'];

    public function orderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
