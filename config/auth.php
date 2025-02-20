<?php

return [

'defaults' => [
    'guard' => env('AUTH_GUARD', 'web'),
    'passwords' => 'employees',
],

'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'employees', // Ganti dari 'users' ke 'employees'
    ],
],

'providers' => [
    'employees' => [ // Tambahkan provider ini
        'driver' => 'eloquent',
        'model' => App\Models\Employee::class,
    ],
],

'passwords' => [
    'employees' => [ // Ganti dari 'users' ke 'employees'
        'provider' => 'employees',
        'table' => 'password_reset_tokens',
        'expire' => 60,
        'throttle' => 60,
    ],
],


];
