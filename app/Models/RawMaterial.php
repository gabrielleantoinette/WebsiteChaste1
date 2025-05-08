<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RawMaterial extends Model
{
    protected $fillable = ['name', 'unit', 'default_price'];

    public function orderItems()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }
}
