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
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CustomMaterialController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\HutangController;
use App\Http\Controllers\NegotiationController;
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
Route::get('/api/custom-materials/{id}/colors', [CustomMaterialController::class, 'getColors']);

Route::middleware([LoggedIn::class])->group(function () {
    Route::get('/produk', [CustomerController::class, 'produk'])->name('produk');
    // Route::get('/produk', [CustomerController::class, 'viewProducts'])->name('produk');
    Route::get('/produk/{id}', [CustomerController::class, 'detailProduct'])->name('produk.detail');
    Route::post('/produk/{id}', [CartController::class, 'addItem'])->name('produk.add');
    Route::get('/custom-terpal', [CustomMaterialController::class, 'customTerpal'])->name('custom.terpal');


    Route::get('/keranjang', [CartController::class, 'view'])->name('keranjang');
    Route::post('/keranjang/add', [CartController::class, 'addItem'])->name('keranjang.add');
    Route::get('/keranjang/delete/{id}', [CartController::class, 'deleteItem'])->name('keranjang.delete');
    Route::post('/keranjang/custom/add', [CartController::class, 'addCustomItem'])->name('keranjang.custom.add');
    Route::get('/custom-terpal', [CustomMaterialController::class, 'showCustomPage'])->name('custom.terpal');

    Route::get('/transaksi', [CustomerController::class, 'viewTransaction'])->name('transaksi');
    Route::get('/transaksi/detail/{id}', [CustomerController::class, 'detailTransaction'])->name('transaksi.detail');
    Route::post('/transaksi/detail/{id}/diterima', [CustomerController::class, 'transaksiDiterima'])->name('transaksi.diterima');
    Route::get('/transaksi/status/{status}', [CustomerController::class, 'filterTransaksiByStatus'])->name('transaksi.status');
    Route::get('/pesanan', function () {
        return view('pesanan');
    })->name('pesanan');
    Route::get('/transaksi/menunggu-pembayaran', [CustomerController::class, 'showMenungguPembayaran'])->name('transaksi.menunggupembayaran');
    Route::get('/transaksi/dikemas', [CustomerController::class, 'showDikemas'])->name('transaksi.dikemas');
    Route::get('/transaksi/dikirim', [CustomerController::class, 'showDikirim'])->name('transaksi.dikirim');
    Route::get('/transaksi/beri-penilaian', [CustomerController::class, 'showBeriPenilaian'])->name('transaksi.beripenilaian');



    Route::get('/produk/{product}/negosiasi', [NegotiationController::class, 'show'])
     ->name('produk.negosiasi');
    Route::post('/produk/{product}/negosiasi', [NegotiationController::class, 'tawar'])
     ->name('produk.negosiasi.tawar');

    Route::get('/profile', [CustomerController::class, 'viewProfile'])->name('profile');

    Route::post('/checkout/invoice', [InvoiceController::class, 'storeFromCheckout'])->name('checkout.invoice');

    Route::get('/order-success', function () {
        return view('order_success');
    })->name('order.success');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');

    Route::get('/invoice/view/{id}', [InvoiceController::class, 'viewInvoice'])->name('invoice.view');
    Route::get('/invoice/download/{id}', [InvoiceController::class, 'downloadInvoice'])->name('invoice.download');
});


// Prefix Admin untuk Management
Route::prefix('admin')->middleware([LoggedIn::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index']);

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

    Route::prefix('custom-materials')->group(function () {
        Route::get('/', [CustomMaterialController::class, 'view'])->name('custom-materials.view');
        Route::get('/create', [CustomMaterialController::class, 'create'])->name('custom-materials.create');
        Route::post('/create', [CustomMaterialController::class, 'store'])->name('custom-materials.store');
        // Route::post('/store', [CustomMaterialController::class, 'store'])->name('admin.custom-materials.store');
        Route::get('/edit/{id}', [CustomMaterialController::class, 'edit'])->name('custom-materials.edit');
        Route::post('/edit/{id}', [CustomMaterialController::class, 'update'])->name('custom-materials.update');
        Route::post('{id}/variants', [CustomMaterialController::class, 'createVariantAction'])->name('custom-materials.variants.store');
        Route::post('/delete/{id}', [CustomMaterialController::class, 'destroy'])->name('custom-materials.destroy');
    });

    Route::prefix('categories')->group(function () {
        Route::get('/', [CategoryController::class, 'view'])->name('admin.categories.view');
        Route::post('/create', [CategoryController::class, 'store'])->name('admin.categories.store');

        Route::get('/detail/{id}', [CategoryController::class, 'detail'])->name('admin.categories.detail');
        Route::post('/detail/{id}', [CategoryController::class, 'update'])->name('admin.categories.update');
        Route::post('/detail/{id}/remove-product', [CategoryController::class, 'removeProductFromCategory'])->name('admin.categories.remove.product');

        Route::get('/detail/{id}/add-product', [CategoryController::class, 'addProductView'])->name('admin.categories.add.product');
        Route::post('/detail/{id}/add-product', [CategoryController::class, 'addProductToCategory'])->name('admin.categories.add.product.action');
        Route::delete('/{id}', [CategoryController::class, 'destroy'])->name('admin.categories.destroy');
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
        Route::post('/detail/{id}', [EmployeeController::class, 'updateEmployeeAction'])->name('employees.updateEmployeeAction');
    });

    Route::prefix('invoices')->group(function () {
        Route::get('/', [InvoiceController::class, 'view']);
        Route::get('/create-customer', [InvoiceController::class, 'createCustomer']);
        Route::post('/create-customer', [InvoiceController::class, 'createCustomerAction']);
        Route::get('/create-product', [InvoiceController::class, 'createProduct']);
        Route::post('/create-product', [InvoiceController::class, 'createProductAction']);
        Route::get('/create-confirmation', [InvoiceController::class, 'createConfirmation']);
        Route::post('/create-confirmation', [InvoiceController::class, 'createConfirmationAction']);
        Route::get('/export-pdf', [InvoiceController::class, 'exportPDF'])->name('invoices.export.pdf');
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

    Route::prefix('keuangan')->group(function () {
        Route::get('/', [KeuanganController::class, 'view'])->name('keuangan.view');
        Route::get('/detail/{id}', [KeuanganController::class, 'detail'])->name('keuangan.detail');
        Route::get('/create', [KeuanganController::class, 'create'])->name('keuangan.create');
        Route::post('/create', [KeuanganController::class, 'store'])->name('keuangan.store');
        Route::get('/keuangan/hutang', [HutangController::class, 'index'])->name('keuangan.hutang.index');
        Route::get('/keuangan/hutang/{id}', [HutangController::class, 'show'])->name('keuangan.hutang.show');
        Route::get('/hutang/create', [HutangController::class, 'create'])->name('keuangan.hutang.create');
        Route::post('/hutang/store', [HutangController::class, 'store'])->name('keuangan.hutang.store');               
        Route::get('/export-pdf', [KeuanganController::class, 'exportPDF'])->name('keuangan.export.pdf');
        Route::get('/hutang/export-pdf', [HutangController::class, 'exportPDF'])->name('keuangan.hutang.export.pdf');
    });
});
