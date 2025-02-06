<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
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


    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'view']);

        Route::get('/create', [ProductController::class, 'create']);
        Route::post('/create', [ProductController::class, 'createAction']);

        Route::get('/detail/{id}', [ProductController::class, 'detail']);
        Route::post('/detail/{id}', [ProductController::class, 'detailAction']);
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'view']);

        Route::get('/create', [EmployeeController::class, 'create']);
        Route::post('/create', [EmployeeController::class, 'createAction']);

        Route::get('/detail/{id}', [EmployeeController::class, 'detail']);
        Route::post('/detail/{id}', [EmployeeController::class, 'detailAction']);
    });
});
