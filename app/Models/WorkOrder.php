<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkOrder extends Model
{
    protected $fillable = [
        'code',
        'order_date',
        'due_date',
        'description',
        'status',
        'created_by',
        'assigned_to',
        'started_at',
        'completed_at',
        'notes'
    ];

    protected $casts = [
        'order_date' => 'date',
        'due_date' => 'date',
        'started_at' => 'datetime',
        'completed_at' => 'datetime'
    ];

    public function items()
    {
        return $this->hasMany(WorkOrderItem::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(Employee::class, 'assigned_to');
    }

    public function getStatusLabelAttribute()
    {
        return [
            'dibuat' => 'Dibuat',
            'dikerjakan' => 'Dikerjakan',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan'
        ][$this->status] ?? $this->status;
    }

    public function getStatusColorAttribute()
    {
        return [
            'dibuat' => 'bg-blue-100 text-blue-800',
            'dikerjakan' => 'bg-yellow-100 text-yellow-800',
            'selesai' => 'bg-green-100 text-green-800',
            'dibatalkan' => 'bg-red-100 text-red-800'
        ][$this->status] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTotalItemsAttribute()
    {
        return $this->items->count();
    }

    public function getCompletedItemsAttribute()
    {
        return $this->items->where('status', 'completed')->count();
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->total_items == 0) return 0;
        return round(($this->completed_items / $this->total_items) * 100);
    }
}
