<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'phone', 'theme', 'company_name', 'company_email', 'company_address'
    ];
}
