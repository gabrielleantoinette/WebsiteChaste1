<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notification extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'title',
        'message',
        'recipient_type',
        'recipient_id',
        'recipient_role',
        'data_type',
        'data_id',
        'is_read',
        'read_at',
        'action_url',
        'icon',
        'priority'
    ];

    protected $casts = [
        'is_read' => 'boolean',
        'read_at' => 'datetime',
    ];

    // Relationship dengan Employee
    public function employee()
    {
        return $this->belongsTo(Employee::class, 'recipient_id')->where('recipient_type', 'employee');
    }

    // Relationship dengan Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'recipient_id')->where('recipient_type', 'customer');
    }

    // Scope untuk notifikasi yang belum dibaca
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    // Scope untuk notifikasi berdasarkan role
    public function scopeForRole($query, $role)
    {
        return $query->where('recipient_role', $role);
    }

    // Scope untuk notifikasi berdasarkan recipient
    public function scopeForRecipient($query, $type, $id)
    {
        return $query->where('recipient_type', $type)->where('recipient_id', $id);
    }

    // Method untuk menandai notifikasi sebagai dibaca
    public function markAsRead()
    {
        $this->update([
            'is_read' => true,
            'read_at' => now()
        ]);
    }

    // Method untuk menandai notifikasi sebagai belum dibaca
    public function markAsUnread()
    {
        $this->update([
            'is_read' => false,
            'read_at' => null
        ]);
    }

    // Method untuk mendapatkan data terkait
    public function getRelatedData()
    {
        if (!$this->data_type || !$this->data_id) {
            return null;
        }

        $modelClass = 'App\\Models\\' . ucfirst($this->data_type);
        
        if (class_exists($modelClass)) {
            return $modelClass::find($this->data_id);
        }

        return null;
    }
}
