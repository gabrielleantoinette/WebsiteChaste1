<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Login Routes
// Route::get('login', function () {
//     return view('login');
// })->name('login');
// Route::post('login', [LoginController::class, 'loginas']);
Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'loginadmin']);
Route::post('logout', [LoginController::class, 'logout'])->name('logout');
// Register Routes
Route::get('register', function () {
    return view('register');
});
Route::post('register', function () {
    // Logic register
});

// Logout Route
//Route::post('logout', [LoginController::class, 'logout'])->name('logout');

// Middleware untuk akses setelah login
Route::middleware(['auth'])->group(function () {
    
    // Admin & Employee Dashboard
    Route::get('/admin/dashboard', function () {
        return view('admin.dashboard');
    })->middleware('role:1'); // Admin Role (1)

    Route::get('/owner/dashboard', function () {
        return view('owner.dashboard');
    })->middleware('role:0'); // Owner Role (0)

    Route::get('/kurir/dashboard', function () {
        return view('kurir.dashboard');
    })->middleware('role:2'); // Kurir Role (2)

    Route::get('/staf-gudang/dashboard', function () {
        return view('staf-gudang.dashboard');
    })->middleware('role:3'); // Staf Gudang Role (3)

    Route::get('/staf-keuangan/dashboard', function () {
        return view('staf-keuangan.dashboard');
    })->middleware('role:4'); // Staf Keuangan Role (4)

    // Customer Dashboard
    Route::get('/customer/dashboard', function () {
        return view('customer.dashboard');
    })->middleware('role:customer');
});

// Prefix Admin untuk Management
Route::prefix('admin')->middleware('auth')->group(function () {
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
