<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HInvoice extends Model
{
    //
    protected $table = 'hinvoice';
    protected $fillable = ['code', 'customer_id', 'employee_id', 'driver_id', 'accountant_id', 'grand_total', 'status', 'is_paid', 'is_dp', 'dp_amount', 'paid_amount', 'due_date', 'receive_date', 'address', 'gudang_id', 'is_online','delivery_proof_photo',
    'delivery_signature', 'quality_proof_photo', 'shipping_cost', 'shipping_courier', 'shipping_service', 'tracking_number', 'cancelled_at', 'cancellation_reason'];

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
        return $this->belongsTo(Employee::class, 'driver_id')->withDefault(null);
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

    public function payments()
    {
        return $this->hasMany(\App\Models\PaymentModel::class, 'invoice_id');
    }

    public function returns()
    {
        return $this->hasMany(Returns::class, 'invoice_id');
    }
}
