<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Ganti Model biasa ke Authenticatable

class Employee extends Authenticatable
{
    use HasFactory;

    protected $table = 'employees';

    protected $fillable = [
        'name',
        'email',
        'password',
        'active',
        'role',
        'phone',
        'ktp',
        'car_plate',
        'car_type',
        'profile_picture',
    ];

    protected $hidden = [
        'password',
    ];
}
