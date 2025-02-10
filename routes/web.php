<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});
Route::get('login', function () {
    return view('login');
});

Route::prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    });
    Route::get('/loginadmin', function () {
        return view('admin.loginadmin');
    });
    Route::post('/login', [LoginController::class, 'loginadmin']);


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
