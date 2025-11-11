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
use App\Http\Controllers\SettingController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\WorkOrderController;
use App\Http\Controllers\ReviewController;
use App\Http\Middleware\LoggedIn;
use App\Http\Middleware\GudangRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/public/storage/{path}', function (string $path) {
    if (str_contains($path, '..')) {
        abort(403);
    }

    $cleanPath = ltrim($path, '/');
    $fullPath = storage_path('app/public/' . $cleanPath);

    if (!File::exists($fullPath) || File::isDirectory($fullPath)) {
        abort(404);
    }

    $mimeType = File::mimeType($fullPath) ?: 'application/octet-stream';

    return response()->file($fullPath, [
        'Content-Type' => $mimeType,
        'Cache-Control' => 'public, max-age=31536000',
    ]);
})->where('path', '.*');

Route::get('logout', [LoginController::class, 'logout'])->name('logout');

Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('login', [LoginController::class, 'loginadmin']);

Route::get('register', [LoginController::class, 'showRegisterForm'])->name('register');
Route::post('register', [LoginController::class, 'register']);
Route::get('/api/custom-materials/{id}/colors', [CustomMaterialController::class, 'getColors']);

Route::middleware([LoggedIn::class])->group(function () {
    Route::get('/produk', [CustomerController::class, 'produk'])->name('produk');
    Route::get('/produk/{id}', [CustomerController::class, 'detailProduct'])->name('produk.detail');
    Route::post('/produk/{id}', [CartController::class, 'addItem'])->name('produk.add');
    Route::get('/custom-terpal', [CustomMaterialController::class, 'customTerpal'])->name('custom.terpal');


    Route::get('/keranjang', [CartController::class, 'view'])->name('keranjang');
    Route::post('/keranjang/add', [CartController::class, 'addItemFromCart'])->name('keranjang.add');
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

    Route::post('/review/submit', [ReviewController::class, 'submitReview'])->name('review.submit');
    Route::get('/review/product/{productId}', [ReviewController::class, 'getProductReviews'])->name('review.product');
    Route::get('/review/check/{orderId}/{productId}', [ReviewController::class, 'checkUserReview'])->name('review.check');

    Route::get('/retur/{id}', [CustomerController::class, 'showReturForm'])->name('retur.create');
    Route::post('/retur/{id}', [CustomerController::class, 'submitRetur'])->name('retur.store');

    Route::get('/produk/{product}/negosiasi', [NegotiationController::class, 'show'])
        ->name('produk.negosiasi');
    Route::post('/produk/{product}/negosiasi', [NegotiationController::class, 'tawar'])
        ->name('produk.negosiasi.tawar');
    // routes/web.php
    Route::post(
        '/produk/{product}/negosiasi/reset',
        [NegotiationController::class, 'reset']
    )->name('produk.negosiasi.reset')
        ->middleware(LoggedIn::class);


    Route::get('/profile', [CustomerController::class, 'viewProfile'])->name('profile');
    Route::post('/profile', [CustomerController::class, 'updateCustomerAction'])->name('profile.update');
    Route::get('/profile/hutang', [CustomerController::class, 'detailHutang'])->name('profile.hutang');
    Route::post('/profile/hutang/upload', [CustomerController::class, 'uploadPelunasanHutang'])->name('profile.hutang.upload');

    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
    Route::post('/checkout/invoice', [InvoiceController::class, 'storeFromCheckout'])->name('checkout.invoice'); // ini tidak dipakai lagi
    // Route::get('/checkout/invoice', [InvoiceController::class, 'storeFromCheckoutGet'])->name('checkout.invoice.get');
    Route::get('/checkout/midtrans-payment', [InvoiceController::class, 'midtransPayment'])->name('checkout.midtrans.payment');
    Route::get('/checkout/midtrans-result', [InvoiceController::class, 'midtransPaymentAction'])->name('checkout.midtrans.result');

    Route::get('/order-success', function () {
        return view('order_success');
    })->name('order.success');

    Route::get('/invoice/view/{id}', [InvoiceController::class, 'viewInvoice'])->name('invoice.view');
    Route::get('/invoice/download/{id}', [InvoiceController::class, 'downloadInvoice'])->name('invoice.download');
});


// Prefix Admin untuk Management
Route::prefix('admin')->middleware([LoggedIn::class])->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('admin.dashboard');
    
    // Dashboard Gudang
    Route::get('/dashboard-gudang', [GudangController::class, 'dashboardGudang'])->name('gudang.dashboard');
    Route::get('/dashboard-driver', [DriverController::class, 'dashboardDriver'])->name('driver.dashboard');

    Route::get('/admin/keuangan', function() {
        return redirect()->route('keuangan.dashboard');
    });

    Route::prefix('products')->group(function () {
        Route::get('/', [ProductController::class, 'view'])
            ->name('admin.products.view');
        Route::get('/create', [ProductController::class, 'create']);
        Route::post('/create', [ProductController::class, 'createProductAction'])
            ->name('admin.products.store');
        Route::get('/detail/{id}', [ProductController::class, 'detail']);
        Route::post('/detail/{id}', [ProductController::class, 'updateProductAction']);
        Route::get('/detail/{id}/variants/create', [ProductController::class, 'createVariant']);
        Route::post('/detail/{id}/variants/create', [ProductController::class, 'createVariantAction']);
        Route::match(['post', 'put'], '/detail/{productId}/variants/{variantId}/update', [ProductController::class, 'updateVariantAction']);
        Route::get('/test-variant/{productId}/{variantId}', function($productId, $variantId) {
            return response()->json(['success' => true, 'message' => 'Test route working', 'productId' => $productId, 'variantId' => $variantId]);
        });
        Route::match(['post', 'delete'], '/detail/{productId}/variants/{variantId}/delete', [ProductController::class, 'deleteVariantAction']);

        Route::post('/detail/{id}/min-price', [ProductController::class, 'updateMinPriceAction']);
        Route::post('/detail/{id}/min-buying-stock', [ProductController::class, 'updateMinBuyingStockAction']);
    });

    Route::prefix('settings')->group(function () {
        Route::get('/', [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/', [SettingController::class, 'update'])->name('admin.settings.update');
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
        Route::post('/upload-bukti/{id}', [InvoiceController::class, 'uploadBukti'])->name('admin.invoices.upload.bukti');
    });

    Route::prefix('gudang-transaksi')->group(function () {
        Route::get('/', [GudangController::class, 'viewTransaksiGudang']);
        Route::get('/detail/{id}', [GudangController::class, 'detailTransaksiGudang']);
        Route::post('/assign-gudang/{id}', [GudangController::class, 'assignGudang']);
    });

    // Work Orders Routes
    Route::prefix('work-orders')->group(function () {
        // Admin routes
        Route::get('/', [WorkOrderController::class, 'index'])->name('admin.work-orders.index');
        Route::get('/create', [WorkOrderController::class, 'create'])->name('admin.work-orders.create');
        Route::post('/', [WorkOrderController::class, 'store'])->name('admin.work-orders.store');
        Route::post('/test', [WorkOrderController::class, 'testStore'])->name('admin.work-orders.test');
        Route::get('/{id}', [WorkOrderController::class, 'show'])->name('admin.work-orders.show');
        Route::get('/{id}/edit', [WorkOrderController::class, 'edit'])->name('admin.work-orders.edit');
        Route::put('/{id}', [WorkOrderController::class, 'update'])->name('admin.work-orders.update');
        
        // Gudang routes
        
        Route::post('/{id}/status', [WorkOrderController::class, 'updateWorkOrderStatus'])->name('gudang.work-orders.update-status');
    });
    


    Route::prefix('assign-driver')->group(function () {
        Route::get('/', [OwnerController::class, 'viewAssignDriver']);
        Route::post('/assign/{id}', [OwnerController::class, 'assignDriver']);
    });

    Route::get('/transactions', [OwnerController::class, 'transactionsIndex'])->name('owner.transactions.index');

                    // Laporan Transaksi Owner
                Route::prefix('laporan-transaksi')->group(function () {
                    Route::get('/download', [OwnerController::class, 'downloadLaporanTransaksi'])->name('owner.laporan.download');
                });

    // Laporan Payment Gateway
    Route::prefix('laporan-payment-gateway')->group(function () {
        Route::get('/download', [OwnerController::class, 'downloadLaporanPaymentGateway'])->name('owner.laporan.payment-gateway');
    });

    // Laporan Negosiasi
    Route::prefix('laporan-negosiasi')->group(function () {
        Route::get('/download', [OwnerController::class, 'downloadLaporanNegosiasi'])->name('owner.laporan.negosiasi');
    });

    Route::prefix('driver-transaksi')->group(function () {
        Route::get('/', [DriverController::class, 'viewTransaksiDriver']);
        Route::get('/detail/{id}', [DriverController::class, 'detailTransaksiDriver'])->name('driver-transaksi.detail');
        Route::post('/finish/{id}', [DriverController::class, 'finishTransaksi']);
    });



    Route::prefix('driver-retur')->group(function () {
        Route::get('/', [DriverController::class, 'viewReturDriver'])->name('driver-retur.index');
        Route::get('/detail/{id}', [DriverController::class, 'detailRetur'])->name('driver-retur.detail');
        Route::post('/pickup/{id}', [DriverController::class, 'pickupRetur'])->name('driver-retur.pickup');
    });

    Route::prefix('keuangan')->group(function () {
        Route::get('/', [KeuanganController::class, 'view'])->name('keuangan.view');
        Route::get('/detail/{id}', [KeuanganController::class, 'detail'])->name('keuangan.detail');
        Route::post('/konfirmasi/{id}', [KeuanganController::class, 'konfirmasi'])->name('keuangan.konfirmasi');
        Route::get('/create', [KeuanganController::class, 'create'])->name('keuangan.create');
        Route::post('/create', [KeuanganController::class, 'store'])->name('keuangan.store');
        Route::get('/keuangan/hutang', [HutangController::class, 'index'])->name('keuangan.hutang.index');
        Route::get('/keuangan/hutang/{id}', [HutangController::class, 'show'])->name('keuangan.hutang.show');
        Route::get('/hutang/create', [HutangController::class, 'create'])->name('keuangan.hutang.create');
        Route::post('/hutang/store', [HutangController::class, 'store'])->name('keuangan.hutang.store');
        Route::post('/keuangan/hutang/{id}/payment', [HutangController::class, 'storePayment'])->name('keuangan.hutang.payment.store');
        Route::get('/export-pdf', [KeuanganController::class, 'exportPDF'])->name('keuangan.export.pdf');
        Route::get('/hutang/export-pdf', [HutangController::class, 'exportPDF'])->name('keuangan.hutang.export.pdf');
    });
    Route::prefix('laporan')->group(function () {
        Route::get('/penjualan/export-pdf', [LaporanController::class, 'penjualanPDF'])->name('laporan.penjualan.pdf');
        Route::get('/customer/export-pdf', [LaporanController::class, 'customerPDF'])->name('laporan.customer.pdf');
        Route::get('/retur/export-pdf', [LaporanController::class, 'returPDF'])->name('laporan.retur.pdf');
        Route::get('/ratarata/export-pdf', [LaporanController::class, 'rataRataPDF'])->name('laporan.ratarata.pdf');
    });
    Route::get('/admin/keuangan/dashboard', [\App\Http\Controllers\KeuanganController::class, 'dashboardKeuangan'])->name('keuangan.dashboard');
    
    // Admin Raw Material Stock Management
    Route::get('/admin/raw-material-stock', [App\Http\Controllers\AdminController::class, 'viewRawMaterialStock'])->name('admin.raw-material-stock');
Route::post('/admin/raw-material-stock/create', [App\Http\Controllers\AdminController::class, 'createRawMaterial'])->name('admin.raw-material-stock.create');
Route::put('/admin/raw-material-stock/{id}/update', [App\Http\Controllers\AdminController::class, 'updateRawMaterialStock'])->name('admin.raw-material-stock.update');
Route::post('/admin/raw-material-stock/{id}/add', [App\Http\Controllers\AdminController::class, 'addRawMaterialStock'])->name('admin.raw-material-stock.add');
Route::post('/admin/raw-material-stock/{id}/reduce', [App\Http\Controllers\AdminController::class, 'reduceRawMaterialStock'])->name('admin.raw-material-stock.reduce');
    
    Route::get('/retur', [App\Http\Controllers\ReturController::class, 'index'])->name('admin.retur.index');
    Route::get('/retur/damaged-products', [App\Http\Controllers\ReturController::class, 'damagedProducts'])->name('admin.retur.damaged-products');
    Route::get('/retur/{id}', [App\Http\Controllers\ReturController::class, 'show'])->name('admin.retur.detail');
    Route::post('/retur/{id}/process', [App\Http\Controllers\ReturController::class, 'process'])->name('admin.retur.process');
});

// Gudang Work Orders Routes (bisa diakses oleh gudang)
Route::prefix('gudang/work-orders')->middleware([LoggedIn::class])->group(function () {
    Route::get('/', [WorkOrderController::class, 'index'])->name('gudang.work-orders.index');
    Route::get('/{id}', [WorkOrderController::class, 'show'])->name('gudang.work-orders.show');
});

// Barang Rusak Gudang
Route::get('/gudang/barang-rusak', [App\Http\Controllers\GudangController::class, 'viewBarangRusak'])->name('gudang.barang-rusak');
Route::post('/gudang/barang-rusak/{id}/perbaiki', [App\Http\Controllers\GudangController::class, 'perbaikiBarangRusak'])->name('gudang.barang-rusak.perbaiki');

// Stok Barang Gudang
Route::get('/gudang/stok-barang', [App\Http\Controllers\GudangController::class, 'viewStokBarang'])->name('gudang.stok-barang')->middleware([LoggedIn::class, 'gudang.role']);
Route::get('/gudang/laporan-stok', [App\Http\Controllers\GudangController::class, 'laporanStokHarian'])->name('gudang.laporan-stok')->middleware([LoggedIn::class, 'gudang.role']);
Route::get('/gudang/laporan-stok/export-pdf', [App\Http\Controllers\GudangController::class, 'exportLaporanStokPDF'])->name('gudang.laporan-stok.pdf')->middleware([LoggedIn::class, 'gudang.role']);

// Laporan Retur untuk Gudang
Route::get('/gudang/laporan-retur/export-pdf', [LaporanController::class, 'returPDF'])->name('gudang.laporan.retur.pdf')->middleware([LoggedIn::class]);

// Upload Foto Bukti Kualitas Barang
Route::post('/gudang/upload-quality-proof/{id}', [App\Http\Controllers\GudangController::class, 'uploadQualityProof'])->name('gudang.upload-quality-proof');

// Notification routes (AJAX only)
Route::prefix('notifications')->middleware([LoggedIn::class])->group(function () {
    Route::get('/latest', [\App\Http\Controllers\NotificationController::class, 'latest'])->name('notifications.latest');
    Route::post('/{id}/mark-read', [App\Http\Controllers\NotificationController::class, 'markAsRead'])->name('notifications.mark-read');
    Route::post('/mark-all-read', [App\Http\Controllers\NotificationController::class, 'markAllAsRead'])->name('notifications.mark-all-read');
    Route::get('/unread-count', [App\Http\Controllers\NotificationController::class, 'getUnreadCount'])->name('notifications.unread-count');
});
