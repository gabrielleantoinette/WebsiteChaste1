<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name', 'email', 'password', 'active', 'phone', 'address', 'city', 'province', 'postal_code', 'birth_date', 'gender', 'profile_picture'
    ];
}
