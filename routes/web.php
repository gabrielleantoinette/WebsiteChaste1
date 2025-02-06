<?php

use App\Http\Controllers\LoginController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('/admin/login', function () {
    return view('admin.login');
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/login', function () {
        return view('admin.login');
    });
    Route::post('/login', [LoginController::class, 'login']);
});
