<?php

use App\Http\Controllers\CustomerController;
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

Route::get('/produk', function () {
    return view('produk');
})->name('produk');
Route::get('/produk/{id}', function ($id) {
    return view('detail');
})->name('produk.detail');
Route::get('/custom-terpal', function () {
    return view('custom');
})->name('custom.terpal');
Route::get('/keranjang', function () {
    return view('keranjang');
})->name('keranjang');



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
        Route::get('/detail/{id}/variants/create', [ProductController::class, 'createVariant']);
        Route::post('/detail/{id}/variants/create', [ProductController::class, 'createVariantAction']);
    });

    Route::prefix('employees')->group(function () {
        Route::get('/', [EmployeeController::class, 'view']);
        Route::get('/create', [EmployeeController::class, 'create']);
        Route::post('/create', [EmployeeController::class, 'createEmployeeAction']);
        Route::get('/detail/{id}', [EmployeeController::class, 'detail']);
        Route::post('/detail/{id}', [EmployeeController::class, 'updateEmployeeAction']);
    });

    Route::prefix('customers')->group(function () {
        Route::get('/', [CustomerController::class, 'view']);
        Route::get('/create', [CustomerController::class, 'create']);
        Route::post('/create', [CustomerController::class, 'createCustomerAction']);
        Route::get('/detail/{id}', [CustomerController::class, 'detail']);
        Route::post('/detail/{id}', [CustomerController::class, 'updateCustomerAction']);
    });
});
