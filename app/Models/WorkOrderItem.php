<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrderItem extends Model
{
    protected $fillable = [
        'work_order_id',
        'size_material',
        'color',
        'quantity',
        'remarks',
        'status',
        'completed_quantity',
        'notes'
    ];

    public function workOrder()
    {
        return $this->belongsTo(WorkOrder::class);
    }

    public function getStatusLabelAttribute()
    {
        return [
            'pending' => 'Menunggu',
            'in_progress' => 'Sedang Dikerjakan',
            'completed' => 'Selesai'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'pending' => 'bg-gray-100 text-gray-800',
            'in_progress' => 'bg-yellow-100 text-yellow-800',
            'completed' => 'bg-green-100 text-green-800'
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->quantity == 0) return 0;
        return round(($this->completed_quantity / $this->quantity) * 100);
    }

    public function getRemainingQuantityAttribute()
    {
        return $this->quantity - $this->completed_quantity;
    }
}
