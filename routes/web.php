<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\GudangController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\ProductController;
use App\Http\Middleware\LoggedIn;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'loginadmin']);

Route::get('register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('register', [LoginController::class, 'register']);

Route::middleware([LoggedIn::class])->group(function () {
    Route::get('/produk', [CustomerController::class, 'viewProducts'])->name('produk');
    Route::get('/produk/{id}', [CustomerController::class, 'detailProduct'])->name('produk.detail');
    Route::post('/produk/{id}', [CartController::class, 'addItem'])->name('produk.add');
    Route::get('/custom-terpal', function () {
        return view('custom');
    })->name('custom.terpal');

    Route::get('/keranjang', [CartController::class, 'view'])->name('keranjang');
    Route::post('/keranjang/add', [CartController::class, 'addItem'])->name('keranjang.add');
    Route::post('/keranjang/delete/{id}', [CartController::class, 'deleteItem'])->name('keranjang.delete');
    Route::get('/checkout', function () {
        return view('checkout');
    })->name('checkout');

    Route::get('/transaksi', [CustomerController::class, 'viewTransaction'])->name('transaksi');
    Route::get('/transaksi/detail/{id}', [CustomerController::class, 'detailTransaction'])->name('transaksi.detail');
    Route::get('/pesanan', function () {
        return view('pesanan');
    })->name('pesanan');


    Route::get('/produk/{id}/negosiasi', function () {
        return view('negosiasi');
    })->name('produk.negosiasi');

    Route::get('/profile', [CustomerController::class, 'viewProfile'])->name('profile');
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
        Route::get('/detail/{id}/variants/create', [ProductController::class, 'createVariant']);
        Route::post('/detail/{id}/variants/create', [ProductController::class, 'createVariantAction']);

        Route::post('/detail/{id}/min-price', [ProductController::class, 'updateMinPriceAction']);
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

    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'view']);
        Route::get('/create-customer', [InvoiceController::class, 'createCustomer']);
        Route::post('/create-customer', [InvoiceController::class, 'createCustomerAction']);
        Route::get('/create-product', [InvoiceController::class, 'createProduct']);
        Route::post('/create-product', [InvoiceController::class, 'createProductAction']);
        Route::get('/create-confirmation', [InvoiceController::class, 'createConfirmation']);
        Route::post('/create-confirmation', [InvoiceController::class, 'createConfirmationAction']);

        Route::get('/detail/{id}', [InvoiceController::class, 'detail']);
    });

    Route::prefix('gudang-transaksi')->group(function () {
        Route::get('/', [GudangController::class, 'viewTransaksiGudang']);
        Route::get('/detail/{id}', [GudangController::class, 'detailTransaksiGudang']);
        Route::post('/assign-gudang/{id}', [GudangController::class, 'assignGudang']);
    });

    Route::prefix('assign-driver')->group(function () {
        Route::get('/', [OwnerController::class, 'viewAssignDriver']);
        Route::post('/assign/{id}', [OwnerController::class, 'assignDriver']);
    });

    Route::prefix('driver-transaksi')->group(function () {
        Route::get('/', [DriverController::class, 'viewTransaksiDriver']);
        Route::post('/finish/{id}', [DriverController::class, 'finishTransaksi']);
    });
});
