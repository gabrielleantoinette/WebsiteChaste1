<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HInvoice extends Model
{
    //
    protected $table = 'hinvoice';
    protected $fillable = ['code', 'customer_id', 'employee_id', 'driver_id', 'accountant_id', 'grand_total', 'status', 'is_paid', 'is_dp', 'dp_amount', 'paid_amount', 'due_date', 'receive_date', 'address', 'gudang_id', 'is_online'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function driver()
    {
        return $this->belongsTo(Employee::class, 'driver_id');
    }

    public function accountant()
    {
        return $this->belongsTo(Employee::class, 'accountant_id');
    }

    public function gudang()
    {
        return $this->belongsTo(Employee::class, 'gudang_id');
    }

    public function details()
    {
        return $this->hasMany(DInvoice::class, 'hinvoice_id');
    }
}
