<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HInvoice;
use App\Models\Customer;
use App\Models\Employee;

class Returns extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_id',
        'customer_id',
        'description',
        'media_path',
        'status',
    ];

    // (Opsional) Relasi ke Invoice
    public function invoice()
    {
        return $this->belongsTo(HInvoice::class, 'invoice_id');
    }

    // (Opsional) Relasi ke Customer
    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    // Relasi ke driver melalui invoice
    public function driver()
    {
        return $this->hasOneThrough(
            Employee::class,
            HInvoice::class,
            'id', // Foreign key di hinvoice
            'id', // Foreign key di employees
            'invoice_id', // Local key di returns
            'driver_id' // Local key di hinvoice
        );
    }
}

