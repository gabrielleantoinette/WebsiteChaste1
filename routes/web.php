<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\LoggedIn;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'loginadmin']);

Route::get('register', function () {
    return view('register');
});
Route::post('register', function () {
    // Logic register
});

// Prefix Admin untuk Management
Route::prefix('admin')->middleware([LoggedIn::class])->group(function () {
    Route::get('/', function () {
        return view('admin.dashboard');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'view']);
        Route::get('/create', [ProductController::class, 'create']);
        Route::post('/create', [ProductController::class, 'createProductAction']);
        Route::get('/detail/{id}', [ProductController::class, 'detail']);
        Route::post('/detail/{id}', [ProductController::class, 'updateProductAction']);
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'view']);
        Route::get('/create', [EmployeeController::class, 'create']);
        Route::post('/create', [EmployeeController::class, 'createEmployeeAction']);
        Route::get('/detail/{id}', [EmployeeController::class, 'detail']);
        Route::post('/detail/{id}', [EmployeeController::class, 'updateEmployeeAction']);
    });
});
