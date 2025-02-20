<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable; // Ganti Model biasa ke Authenticatable

class Employee extends Authenticatable
{
    use HasFactory;
    
    protected $table = 'employees';

    protected $fillable = [
        'username',
        'password',
        'role',
    ];

    protected $hidden = [
        'password',
    ];
}

