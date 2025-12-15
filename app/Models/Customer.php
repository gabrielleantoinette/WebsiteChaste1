<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'password', 'active', 'phone', 'address', 'city', 'province', 'postal_code', 'birth_date', 'gender', 'profile_picture', 'two_factor_enabled', 'two_factor_code', 'two_factor_expires_at'
    ];
}
