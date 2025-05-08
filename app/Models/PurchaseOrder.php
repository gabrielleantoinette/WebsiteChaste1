<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = ['supplier_id', 'code', 'order_date', 'due_date', 'total', 'status'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function payments()
    {
        return $this->hasMany(DebtPayment::class);
    }

    public function getStatusLabelAttribute()
    {
        return [
            'belum_dibayar' => 'Belum Dibayar',
            'sebagian_dibayar' => 'Sebagian Dibayar',
            'lunas' => 'Lunas',
        ][$this->status];
    }
}
