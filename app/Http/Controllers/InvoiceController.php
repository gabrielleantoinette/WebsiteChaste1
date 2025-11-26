<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\DInvoice;
use App\Models\Employee;
use App\Models\HInvoice;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;
use App\Models\Cart;
use App\Models\OrderModel;
use App\Models\PaymentModel;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;
use Midtrans\Config;
use Midtrans\Snap;
use Illuminate\Support\Facades\Storage;
use stdClass;

class InvoiceController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
        // Set midtrans configuration
        Config::$serverKey = 'SB-Mid-server-GkW-oS9nOpd2CktkXZve26qV';
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function view(Request $request)
    {
        $query = HInvoice::with(['customer', 'employee', 'driver', 'gudang', 'accountant']);
        
        if ($request->filled('status')) {
            $statusMap = [
                'menunggu_pembayaran' => 'Menunggu Pembayaran',
                'dikemas' => 'Dikemas',
                'dikirim' => 'Dikirim',
                'selesai' => 'Selesai',
            ];
            $statusValue = $statusMap[$request->status] ?? $request->status;
            $query->where('status', $statusValue);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('code', 'like', "%{$search}%")
                  ->orWhereHas('customer', function($customerQuery) use ($search) {
                      $customerQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }
        
        $totalInvoices = HInvoice::count();
        $completedInvoices = HInvoice::where('status', 'Selesai')->count();
        $pendingInvoices = HInvoice::whereIn('status', ['Menunggu Pembayaran', 'Menunggu Konfirmasi Pembayaran'])->count();
        $totalSales = HInvoice::sum('grand_total');
        
        $invoices = $query->orderBy('created_at', 'desc')->orderBy('id', 'desc')->paginate(10);
        
        return view('admin.invoices.view', compact('invoices', 'totalInvoices', 'completedInvoices', 'pendingInvoices', 'totalSales'));
    }

    public function createCustomer()
    {
        $customers = Customer::all();
        return view('admin.invoices.create-customer', compact('customers'));
    }

    public function createCustomerAction(Request $request)
    {
        $customer = Customer::find($request->customer_id);

        $invoice_session = new stdClass();
        $invoice_session->customer = $customer;
        $invoice_session->products = [];
        Session::put('invoice_session', $invoice_session);

        return redirect('/admin/invoices/create-product');
    }

    public function createProduct()
    {
        $products = Product::all();

        $invoice_session = Session::get('invoice_session');
        return view('admin.invoices.create-product', compact('products', 'invoice_session'));
    }

    public function createProductAction(Request $request)
    {
        $invoice_session = Session::get('invoice_session');

        $productQuantity = $request->quantity;
        $productId = $request->product_id;
        $variantId = $request->variant_id;
        $lists = [];

        for ($i = 0; $i < count($productQuantity); $i++) {
            if ($productQuantity[$i] > 0) {
                $product = Product::find($productId[$i]);
                $product->quantity = $productQuantity[$i];
                $product->variant_id = $variantId[$i];
                $product->variant = ProductVariant::find($variantId[$i]);
                $lists[] = $product;
            }
        }

        $invoice_session->products = $lists;
        Session::put('invoice_session', $invoice_session);
        return redirect('/admin/invoices/create-confirmation');
    }

    public function createConfirmation()
    {
        $invoice_session = Session::get('invoice_session');
        $customer = $invoice_session->customer;
        $products = $invoice_session->products;
        return view('admin.invoices.create-confirmation', compact('customer', 'products'));
    }

    public function createConfirmationAction(Request $request)
    {
        $due_date = $request->due_date;
        $receive_date = $request->receive_date;
        $address = $request->address;

        $invoice_session = Session::get('invoice_session');
        $customer = $invoice_session->customer;
        $products = $invoice_session->products;

        $current_user = Session::get('user');

        // start transaction
        // insert to hinvoice
        // insert to dinvoice
        // update product quantity
        // commit transaction
        DB::beginTransaction();
        try {
            $grand_total = 0;
            foreach ($products as $product) {
                $grand_total += $product->price * $product->quantity;
            }

            $hinvoice = HInvoice::create([
                'code' => $this->createInvoiceCode(),
                'customer_id' => $customer->id,
                'employee_id' => $current_user->id,
                'due_date' => $due_date,
                'receive_date' => $receive_date,
                'grand_total' => $grand_total,
                'status' => 'dikemas',
                'address' => $address,
            ]);

            foreach ($products as $product) {
                DInvoice::create([
                    'hinvoice_id' => $hinvoice->id,
                    'product_id' => $product->id,
                    'variant_id' => $product->variant_id,
                    'price' => $product->price,
                    'quantity' => $product->quantity,
                    'subtotal' => $product->price * $product->quantity,
                ]);
            }

            DB::commit();
            Session::forget('invoice_session');
        } catch (\Exception $e) {
            DB::rollBack();
            var_dump($e);
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect('/admin/invoices');
    }

    public function detail($id)
    {
        $invoice = HInvoice::with(['details.product', 'details.variant', 'customer', 'employee', 'driver', 'gudang'])->findOrFail($id);
        return view('admin.invoices.detail', compact('invoice'));
    }

    // create invoice code
    public function createInvoiceCode()
    {
        $months = ['JAN', 'FEB', 'MAR', 'APR', 'MEI', 'JUN', 'JUL', 'AGU', 'SEP', 'OKT', 'NOV', 'DES'];
        $year = date('Y');
        $month = date('m');

        $counter = 1000;
        $invoice_this_month_count = HInvoice::where('created_at', 'like', '%' . $year . '-' . $month . '%')->count();
        $counter += $invoice_this_month_count + 1;
        // get last 3 digit of counter
        $counter = substr($counter, -3);

        $invoice_code = 'CS' . $counter . '/' . $months[$month - 1] . $year;
        var_dump($invoice_code);
        return $invoice_code;
    }

    public function exportPDF()
    {
        $invoices = HInvoice::with(['customer', 'employee'])->get();

        $pdf = PDF::loadView('exports.invoices-pdf', compact('invoices'));
        return $pdf->download('laporan-invoice.pdf');
    }

    public function storeFromCheckout(Request $request)
    {
        $customerId = session()->get('customer_id');
        if (!$customerId) {
            $user = Session::get('user');
            $customerId = $user['id'] ?? null;
        }
        
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }
        
        $paymentMethod = $request->input('payment_method');
        $shippingMethod = $request->input('shipping_method');
        $shippingCost = $request->input('shipping_cost') ?? 0;
        $shippingCourier = $request->input('shipping_courier') ?? '';
        $shippingService = $request->input('shipping_service') ?? '';
        $alamat = $request->input('address');
        $invoiceCode = 'INV-' . date('Ymd') . '-' . Str::random(5);
        $isFirstOrder = HInvoice::where('customer_id', $customerId)->count() == 0;
        
        $isAlamatSurabaya = str_contains(strtolower($alamat ?? ''), 'surabaya');
        if ($isAlamatSurabaya && $shippingMethod === 'expedition') {
            return redirect()->back()->with('error', 'Untuk alamat pengiriman di Surabaya, silakan pilih Kurir Perusahaan. Ekspedisi tidak tersedia untuk Surabaya.');
        }
        
        if ($paymentMethod == 'cod') {
            $customer = Customer::find($customerId);
            $isFromSurabaya = false;
            if ($customer) {
                $isFromSurabaya = (strtolower($customer->city ?? '') === 'surabaya' || 
                                  strtolower($customer->province ?? '') === 'jawa timur' && 
                                  str_contains(strtolower($customer->city ?? ''), 'surabaya'));
            }
            
            if (!$isFromSurabaya && !$isAlamatSurabaya) {
                return redirect()->back()->with('error', 'COD hanya tersedia untuk pengiriman di Surabaya. Silakan pilih metode pembayaran lain.');
            }
        }
        
        if ($paymentMethod == 'transfer') {
            $request->validate([
                'bukti_transfer' => 'required|image|max:2048',
            ]);
        }

        // cari apakah ini pesanan pertama customer?
        $isFirstOrder = HInvoice::where('customer_id', $customerId)->count() == 0;

        if ($paymentMethod == 'hutang') {
            if ($isFirstOrder) {
                return redirect()->back()->with('error', 'Anda harus minimal 1x transaksi lunas sebelum boleh berhutang.');
            }
            
            $totalHutangSaatIni = HInvoice::where('customer_id', $customerId)
                ->whereHas('payments', function($q) {
                    $q->where('method', 'hutang')->where('is_paid', 0);
                })
                ->sum('grand_total');
            
            $limitHutang = 10000000; // 10 juta
            if (($totalHutangSaatIni + $grandTotal) > $limitHutang) {
                return redirect()->back()->with('error', 'Total hutang Anda akan melebihi limit Rp 10.000.000. Silakan lunasi hutang terlebih dahulu atau pilih metode pembayaran lain.');
            }
        }

        if (!empty($cartIds)) {
            $stockValidation = $this->validateStockAvailability($cartIds);
            if (!$stockValidation['valid']) {
                return redirect()->back()->with('error', $stockValidation['message']);
            }
        }

        $subtotalProduk = 0;
        $cartIds = [];
        if ($request->has('cart_ids')) {
            $cartIds = $request->cart_ids;
            $carts = DB::table('cart')->whereIn('id', $cartIds)->get();

            foreach ($carts as $cart) {
                if ($cart->variant_id) {
                    $productModel = \App\Models\Product::join('product_variants', 'products.id', '=', 'product_variants.product_id')
                        ->where('product_variants.id', $cart->variant_id)
                        ->select('products.*')
                        ->first();
                    
                    if ($productModel) {
                        $selectedSize = $cart->selected_size ?? '2x3';
                        $price = $cart->harga_custom ?? $productModel->getPriceForSize($selectedSize);
                        $subtotalProduk += ($price ?? 0) * $cart->quantity;
                    } else {
                        // Fallback jika product tidak ditemukan
                        $price = $cart->harga_custom ?? 0;
                        $subtotalProduk += $price * $cart->quantity;
                    }
                } elseif ($cart->kebutuhan_custom) {
                    $subtotalProduk += ($cart->harga_custom ?? 0) * $cart->quantity;
                }
            }
        }

        $grandTotal = $subtotalProduk + $shippingCost;

        $statusInvoice = 'Dikemas';
        if ($paymentMethod == 'transfer') {
            $statusInvoice = 'Menunggu Konfirmasi Pembayaran';
        } elseif ($isFirstOrder) {
            $statusInvoice = 'Menunggu Pembayaran';
        }
        $transferProofPath = null;
        if ($paymentMethod == 'transfer' && $request->hasFile('bukti_transfer')) {
            $file = $request->file('bukti_transfer');
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('YmdHis');
            $randomString = \Str::random(10);
            $uniqueFileName = $timestamp . '_' . $randomString . '_' . \Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            
            $transferProofPath = $file->storeAs('bukti_transfer', $uniqueFileName, 'public');
            
            \Log::info('Transfer proof uploaded', [
                'original_name' => $originalName,
                'unique_file_name' => $uniqueFileName,
                'stored_path' => $transferProofPath,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'customer_id' => $customerId,
                'timestamp' => $timestamp
            ]);
            
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($transferProofPath)) {
                \Log::error('Transfer proof file not saved correctly', [
                    'path' => $transferProofPath,
                    'customer_id' => $customerId
                ]);
            } else {
                $storedFileSize = \Illuminate\Support\Facades\Storage::disk('public')->size($transferProofPath);
                \Log::info('Transfer proof file verified', [
                    'path' => $transferProofPath,
                    'original_size' => $file->getSize(),
                    'stored_size' => $storedFileSize,
                    'match' => $file->getSize() === $storedFileSize
                ]);
            }
        }
        // ⬇️ Ubah ini supaya insert sekaligus ambil ID
        $newInvoiceId = DB::table('hinvoice')->insertGetId([
            'code' => $invoiceCode,
            'customer_id' => $customerId,
            'employee_id' => 1,
            'address' => $alamat,
            'is_online' => 1, // Transaksi dari checkout online
            'status' => $statusInvoice,
            'is_dp' => $isFirstOrder ? true : false,
            'dp_amount' => $isFirstOrder ? $grandTotal / 2 : 0,
            'grand_total' => $grandTotal,
            'shipping_cost' => $shippingCost ?? 0,
            'shipping_courier' => $shippingCourier,
            'shipping_service' => $shippingService,
            'due_date' => now()->addDays(30), // <-- otomatis 30 hari dari sekarang
            'created_at' => now(),
            'updated_at' => now(),
            'transfer_proof' => $transferProofPath,
        ]);
        
        if ($transferProofPath) {
            $storedPath = DB::table('hinvoice')->where('id', $newInvoiceId)->value('transfer_proof');
            $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($transferProofPath);
            
            \Log::info('Invoice created with transfer proof', [
                'invoice_id' => $newInvoiceId,
                'invoice_code' => $invoiceCode,
                'transfer_proof_path' => $transferProofPath,
                'stored_path_in_db' => $storedPath,
                'path_match' => $transferProofPath === $storedPath,
                'file_exists' => $fileExists,
                'customer_id' => $customerId
            ]);
            
            if ($transferProofPath !== $storedPath) {
                \Log::error('Transfer proof path mismatch!', [
                    'invoice_id' => $newInvoiceId,
                    'expected_path' => $transferProofPath,
                    'stored_path' => $storedPath
                ]);
            }
            
            if (!$fileExists) {
                \Log::warning('Transfer proof file not found after invoice creation', [
                    'invoice_id' => $newInvoiceId,
                    'path' => $transferProofPath
                ]);
            }
        }

        // Midtrans
        // Hanya buat midtrans kalau payment_method adalah ewallet.
        if ($paymentMethod == 'midtrans') {
            $customer = Customer::find($customerId);
            
            $itemDetails = [];
            
            if (!empty($cartIds)) {
                $carts = DB::table('cart')->whereIn('id', $cartIds)->get();
                foreach ($carts as $cart) {
                    if ($cart->variant_id) {
                        $productModel = \App\Models\Product::join('product_variants', 'products.id', '=', 'product_variants.product_id')
                            ->where('product_variants.id', $cart->variant_id)
                            ->select('products.*')
                            ->first();
                        
                        if ($productModel) {
                            $selectedSize = $cart->selected_size ?? '2x3';
                            $price = $cart->harga_custom ?? $productModel->getPriceForSize($selectedSize);
                            $itemDetails[] = [
                                'id'       => 'product-' . $productModel->id . '-' . $cart->variant_id,
                                'price'    => $price * $cart->quantity,
                                'quantity' => 1,
                                'name'     => $productModel->name . ' (x' . $cart->quantity . ')'
                            ];
                        }
                    } elseif ($cart->kebutuhan_custom) {
                        $itemDetails[] = [
                            'id'       => 'custom-' . $cart->id,
                            'price'    => ($cart->harga_custom ?? 0) * $cart->quantity,
                            'quantity' => 1,
                            'name'     => 'Produk Custom (x' . $cart->quantity . ')'
                        ];
                    }
                }
            }
            
            if ($shippingCost > 0) {
                $shippingName = 'Ongkos Kirim';
                if ($shippingCourier) {
                    $shippingName .= ' - ' . ucfirst($shippingCourier);
                }
                if ($shippingService) {
                    $shippingName .= ' (' . $shippingService . ')';
                }
                
                $itemDetails[] = [
                    'id'       => 'shipping-' . $newInvoiceId,
                    'price'    => $shippingCost,
                    'quantity' => 1,
                    'name'     => $shippingName
                ];
            }
            
            $itemsTotal = array_sum(array_column($itemDetails, 'price'));
            if (abs($itemsTotal - $grandTotal) > 1) { // Allow 1 rupiah difference for rounding
                if (!empty($itemDetails)) {
                    $diff = $grandTotal - $itemsTotal;
                    $itemDetails[count($itemDetails) - 1]['price'] += $diff;
                }
            }
            
            $newPayment = new PaymentModel();
            $newPayment->invoice_id = $newInvoiceId;
            $newPayment->method = 'midtrans';
            $newPayment->type = $isFirstOrder ? 'dp' : 'full';
            $newPayment->is_paid = false;
            $newPayment->amount = $grandTotal;
            $newPayment->save();
            
            $finishRedirectUrl = url('/checkout/midtrans-result?paymentId=' . $newPayment->id . '&status=success');
            $unfinishRedirectUrl = url('/checkout/midtrans-result?paymentId=' . $newPayment->id . '&status=pending');
            $errorRedirectUrl = url('/checkout/midtrans-result?paymentId=' . $newPayment->id . '&status=error');
            
            $payload = [
                'transaction_details' => [
                    'order_id'      => now() . '-' . $newInvoiceId,
                    'gross_amount'  => $isFirstOrder ? $grandTotal / 2 : $grandTotal,
                ],
                'customer_details' => [
                    'first_name'    => $customer->name,
                    'email'         => $customer->email,
                ],
                'item_details' => $itemDetails,
                'finish_redirect_url' => $finishRedirectUrl,
                'unfinish_redirect_url' => $unfinishRedirectUrl,
                'error_redirect_url' => $errorRedirectUrl
            ];
            $snapToken = Snap::getSnapToken($payload);
            
            $newPayment->snap_token = $snapToken;
            $newPayment->save();

            if (!empty($cartIds)) {
                DB::table('dinvoice')->where('hinvoice_id', $newInvoiceId)->delete();
                
                $carts = DB::table('cart')
                    ->whereIn('id', $cartIds)
                    ->select('*')
                    ->get();
                
                foreach ($carts as $cart) {
                    if ($cart->variant_id) {
                        // Produk biasa
                        $productModel = \App\Models\Product::join('product_variants', 'products.id', '=', 'product_variants.product_id')
                            ->where('product_variants.id', $cart->variant_id)
                            ->select('products.*')
                            ->first();
                        
                        if ($productModel) {
                            $selectedSize = $cart->selected_size ?? '2x3';
                            $price = $cart->harga_custom ?? $productModel->getPriceForSize($selectedSize);
                            DB::table('dinvoice')->insert([
                                'hinvoice_id' => $newInvoiceId,
                                'product_id' => $productModel->id,
                                'variant_id' => $cart->variant_id,
                                'selected_size' => $selectedSize,
                                'price' => $price,
                                'quantity' => $cart->quantity,
                                'subtotal' => $price * $cart->quantity,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                    } elseif ($cart->kebutuhan_custom) {
                        // Produk custom
                        DB::table('dinvoice')->insert([
                            'hinvoice_id' => $newInvoiceId,
                            'product_id' => null,
                            'variant_id' => null,
                            'price' => $cart->harga_custom,
                            'quantity' => $cart->quantity,
                            'subtotal' => $cart->harga_custom * $cart->quantity,
                            'kebutuhan_custom' => $cart->kebutuhan_custom,
                            'warna_custom' => $cart->warna_custom,
                            'bahan_custom' => $cart->bahan_custom ?? null,
                            'ukuran_custom' => $cart->ukuran_custom ?? null,
                            'jumlah_ring_custom' => $cart->jumlah_ring_custom ?? null,
                            'pakai_tali_custom' => $cart->pakai_tali_custom ?? null,
                            'catatan_custom' => $cart->catatan_custom ?? null,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            
            session()->put('cart_ids_to_delete', $cartIds);
            return redirect()->route('checkout.midtrans.payment', ['snapToken' => $snapToken, 'paymentId' => $newPayment->id]);
        }

        if (!empty($cartIds)) {
            // Hapus dinvoice records yang sudah ada untuk invoice ini (jika ada)
            DB::table('dinvoice')->where('hinvoice_id', $newInvoiceId)->delete();
            
            $carts = DB::table('cart')->whereIn('id', $cartIds)->get();
            
            foreach ($carts as $cart) {
                if ($cart->variant_id) {
                    // Produk biasa
                    $productModel = \App\Models\Product::join('product_variants', 'products.id', '=', 'product_variants.product_id')
                        ->where('product_variants.id', $cart->variant_id)
                        ->select('products.*')
                        ->first();
                    
                    if ($productModel) {
                        $selectedSize = $cart->selected_size ?? '2x3';
                        $price = $cart->harga_custom ?? $productModel->getPriceForSize($selectedSize);
                        DB::table('dinvoice')->insert([
                            'hinvoice_id' => $newInvoiceId,
                            'product_id' => $productModel->id,
                            'variant_id' => $cart->variant_id,
                            'selected_size' => $selectedSize,
                            'price' => $price,
                            'quantity' => $cart->quantity,
                            'subtotal' => $price * $cart->quantity,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                } elseif ($cart->kebutuhan_custom) {
                    // Produk custom - simpan dengan product_id = 0 atau null
                    DB::table('dinvoice')->insert([
                        'hinvoice_id' => $newInvoiceId,
                        'product_id' => null,
                        'variant_id' => null,
                        'price' => $cart->harga_custom,
                        'quantity' => $cart->quantity,
                        'subtotal' => $cart->harga_custom * $cart->quantity,
                        'kebutuhan_custom' => $cart->kebutuhan_custom,
                        'warna_custom' => $cart->warna_custom,
                        'bahan_custom' => $cart->bahan_custom ?? null,
                        'ukuran_custom' => $cart->ukuran_custom ?? null,
                        'jumlah_ring_custom' => $cart->jumlah_ring_custom ?? null,
                        'pakai_tali_custom' => $cart->pakai_tali_custom ?? null,
                        'catatan_custom' => $cart->catatan_custom ?? null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        if (!empty($cartIds)) {
            $this->reduceStockFromCart($cartIds);
        }

        if (in_array($paymentMethod, ['cod', 'hutang', 'transfer_bank'])) {
            PaymentModel::create([
                'invoice_id' => $newInvoiceId,
                'method' => $paymentMethod,
                'type' => 'full',
                'is_paid' => 0,
                'amount' => $grandTotal,
            ]);
        }

        if (!empty($cartIds)) {
            DB::table('cart')->whereIn('id', $cartIds)->delete();
        }

        $customer = Customer::find($customerId);
        $notificationService = app(NotificationService::class);
        $notificationService->notifyNewOrder($newInvoiceId, [
            'customer_name' => $customer->name,
            'invoice_code' => $invoiceCode,
            'total_amount' => $grandTotal
        ]);

        $notificationService->notifyCustomerAction([
            'message' => "Customer {$customer->name} telah membuat pesanan baru dengan kode {$invoiceCode}",
            'action_id' => $newInvoiceId,
            'action_url' => "/admin/invoices/detail/{$newInvoiceId}",
            'priority' => 'high'
        ]);

        $customerNotificationMessage = '';
        $customerNotificationTitle = '';
        
        if ($paymentMethod == 'midtrans') {
            $customerNotificationTitle = 'Pesanan Berhasil Dibuat';
            $customerNotificationMessage = "Pesanan Anda dengan kode {$invoiceCode} telah berhasil dibuat. Silakan lakukan pembayaran melalui Midtrans.";
        } elseif ($paymentMethod == 'transfer') {
            $customerNotificationTitle = 'Pesanan Berhasil Dibuat';
            $customerNotificationMessage = "Pesanan Anda dengan kode {$invoiceCode} telah berhasil dibuat. Silakan lakukan transfer dan upload bukti pembayaran.";
        } elseif ($paymentMethod == 'cod') {
            $customerNotificationTitle = 'Pesanan Berhasil Dibuat';
            $customerNotificationMessage = "Pesanan Anda dengan kode {$invoiceCode} telah berhasil dibuat. Pembayaran dilakukan saat pengiriman (COD).";
        } elseif ($paymentMethod == 'hutang') {
            $customerNotificationTitle = 'Pesanan Berhasil Dibuat';
            $customerNotificationMessage = "Pesanan Anda dengan kode {$invoiceCode} telah berhasil dibuat dengan pembayaran hutang.";
        }

        if ($customerNotificationMessage) {
            $notificationService->sendToCustomer(
                'order_created',
                $customerNotificationTitle,
                $customerNotificationMessage,
                $customerId,
                [
                    'data_type' => 'order',
                    'data_id' => $newInvoiceId,
                    'action_url' => "/orders/{$newInvoiceId}",
                    'priority' => 'normal'
                ]
            );
        }

        session()->put('last_invoice_id', $newInvoiceId);
        return redirect()->route('order.success');
    }

    public function midtransPayment(Request $request)
    {
        $snapToken = $request->query('snapToken');
        $paymentId = $request->query('paymentId');

        return view('midtrans_payment', compact('snapToken', 'paymentId'));
    }

    public function midtransPaymentAction(Request $request)
    {
        try {
            \Log::info('Midtrans callback received', [
                'url' => $request->fullUrl(),
                'query' => $request->all(),
                'method' => $request->method()
            ]);
            
            $paymentStatus = $request->query('status') 
                ?? $request->query('amp;status') 
                ?? $request->input('status') 
                ?? $request->get('status');
            
            $paymentId = $request->query('paymentId') 
                ?? $request->input('paymentId') 
                ?? $request->get('paymentId');
            
            \Log::info('Midtrans callback: Parsed parameters', [
                'paymentStatus' => $paymentStatus,
                'paymentId' => $paymentId,
                'all_query' => $request->all()
            ]);
            
            if (!$paymentId) {
                \Log::error('Midtrans callback: paymentId tidak ditemukan', $request->all());
                return redirect()->route('produk')->with('error', 'Payment ID tidak ditemukan.');
            }
            
            $payment = PaymentModel::find($paymentId);
            if (!$payment) {
                \Log::error('Midtrans callback: Payment tidak ditemukan', ['paymentId' => $paymentId]);
                return redirect()->route('produk')->with('error', 'Data pembayaran tidak ditemukan.');
            }
            
            \Log::info('Midtrans callback: Payment found', ['paymentId' => $paymentId, 'invoice_id' => $payment->invoice_id]);
            
            $invoice = HInvoice::find($payment->invoice_id);
            if (!$invoice) {
                \Log::error('Midtrans callback: Invoice tidak ditemukan', ['invoice_id' => $payment->invoice_id]);
                return redirect()->route('produk')->with('error', 'Invoice tidak ditemukan.');
            }
            
            $customer = Customer::find($invoice->customer_id);
            if (!$customer) {
                \Log::error('Midtrans callback: Customer tidak ditemukan', ['customer_id' => $invoice->customer_id]);
                return redirect()->route('produk')->with('error', 'Data customer tidak ditemukan.');
            }
            
            $payment->is_paid = $paymentStatus == 'success' ? true : false;
            $payment->save();
            
            \Log::info('Midtrans callback: Payment status updated', [
                'paymentId' => $paymentId,
                'is_paid' => $payment->is_paid,
                'status' => $paymentStatus
            ]);

        // Set session last_invoice_id agar halaman sukses tidak error
        session()->put('last_invoice_id', $payment->invoice_id);

        if ($paymentStatus == 'success') {
            
            $cartIds = [];
            
            // Coba ambil dari session dulu
            if (session()->has('cart_ids_to_delete')) {
                $cartIds = session('cart_ids_to_delete');
                session()->forget('cart_ids_to_delete');
            }
            
            if (empty($cartIds)) {
                \Log::info('Cart IDs tidak ditemukan di session, menghapus cart berdasarkan dinvoice', [
                    'invoice_id' => $invoice->id,
                    'customer_id' => $invoice->customer_id
                ]);
                
                $dinvoices = DB::table('dinvoice')->where('hinvoice_id', $invoice->id)->get();
                
                foreach ($dinvoices as $dinvoice) {
                    if ($dinvoice->variant_id) {
                        // Match cart berdasarkan customer_id, variant_id, quantity, dan selected_size
                        $matchingCarts = DB::table('cart')
                            ->where('user_id', $invoice->customer_id)
                            ->where('variant_id', $dinvoice->variant_id)
                            ->where('quantity', $dinvoice->quantity)
                            ->where(function($query) use ($dinvoice) {
                                if ($dinvoice->selected_size) {
                                    $query->where('selected_size', $dinvoice->selected_size);
                                } else {
                                    $query->whereNull('selected_size');
                                }
                            })
                            ->get();
                        
                        foreach ($matchingCarts as $cart) {
                            $cartIds[] = $cart->id;
                        }
                    } elseif ($dinvoice->kebutuhan_custom) {
                        // Match cart custom berdasarkan customer_id, kebutuhan_custom, quantity
                        $matchingCarts = DB::table('cart')
                            ->where('user_id', $invoice->customer_id)
                            ->where('kebutuhan_custom', $dinvoice->kebutuhan_custom)
                            ->where('quantity', $dinvoice->quantity)
                            ->get();
                        
                        foreach ($matchingCarts as $cart) {
                            $cartIds[] = $cart->id;
                        }
                    }
                }
                
                $cartIds = array_unique($cartIds);
            }
            
            if (!empty($cartIds)) {
                $stockValidation = $this->validateStockAvailability($cartIds);
                if ($stockValidation['valid']) {
                    $this->reduceStockFromCart($cartIds);
                    DB::table('cart')->whereIn('id', $cartIds)->delete();
                    \Log::info('Cart berhasil dihapus setelah payment sukses', [
                        'cart_ids' => $cartIds,
                        'invoice_id' => $invoice->id
                    ]);
                } else {
                    \Log::error("Stok tidak cukup setelah pembayaran midtrans: " . $stockValidation['message']);
                }
            } else {
                \Log::warning('Tidak ada cart yang ditemukan untuk dihapus', [
                    'invoice_id' => $invoice->id,
                    'customer_id' => $invoice->customer_id
                ]);
            }

            $invoice->status = 'Dikemas'; // Langsung ubah ke status Dikemas agar masuk ke gudang
            $invoice->is_paid = 1;
            $invoice->paid_amount = $payment->amount;
            $invoice->save();
            
            \Log::info('Midtrans callback: Invoice updated', [
                'invoice_id' => $invoice->id,
                'invoice_code' => $invoice->code,
                'status' => $invoice->status,
                'is_paid' => $invoice->is_paid,
                'paid_amount' => $invoice->paid_amount
            ]);
            
            $notificationService = app(NotificationService::class);
            
            $notificationService->sendToCustomer(
                'payment_success',
                'Pembayaran Berhasil',
                "Pembayaran untuk pesanan {$invoice->code} telah berhasil dilakukan. Pesanan Anda akan segera diproses.",
                $customer->id,
                [
                    'data_type' => 'order',
                    'data_id' => $invoice->id,
                    'action_url' => "/orders/{$invoice->id}",
                    'priority' => 'high'
                ]
            );

            $notificationService->notifyPayment($payment->id, [
                'amount' => $payment->amount,
                'customer_name' => $customer->name,
                'invoice_code' => $invoice->code
            ]);
            
            $notificationService->notifyOrderCreated($invoice->id, $customer->id, [
                'total_amount' => $payment->amount,
                'invoice_code' => $invoice->code,
                'customer_name' => $customer->name
            ]);
            
            $notificationService->sendToRole(
                'order_created',
                'Pesanan Baru Siap Diproses',
                "Pesanan baru {$invoice->code} dari {$customer->name} sebesar Rp " . number_format($payment->amount) . " siap untuk diproses",
                'gudang',
                [
                    'data_type' => 'order',
                    'data_id' => $invoice->id,
                    'action_url' => "/admin/gudang-transaksi/detail/{$invoice->id}",
                    'priority' => 'high',
                    'icon' => 'fas fa-box'
                ]
            );
            
            $notificationService->notifyWarehouseAction([
                'message' => "Pesanan baru {$invoice->code} dari {$customer->name} sebesar Rp " . number_format($payment->amount) . " siap untuk diproses",
                'action_id' => $invoice->id,
                'action_url' => "/admin/gudang-transaksi/detail/{$invoice->id}",
                'priority' => 'high'
            ]);
            
            $notificationService->sendToRole(
                'order_created',
                'Pesanan Baru',
                "Pesanan baru {$invoice->code} dari {$customer->name} sebesar Rp " . number_format($payment->amount) . " telah dibuat",
                'admin',
                [
                    'data_type' => 'order',
                    'data_id' => $invoice->id,
                    'action_url' => "/admin/invoices/{$invoice->id}",
                    'priority' => 'high',
                    'icon' => 'fas fa-shopping-cart'
                ]
            );
            
            $existingDInvoice = DB::table('dinvoice')->where('hinvoice_id', $invoice->id)->first();
            if (!$existingDInvoice) {
                \Log::warning("No dinvoice data found for invoice {$invoice->id}. This should not happen.");
            }
        } else {
            \Log::warning('Midtrans callback: Payment not successful', [
                'paymentId' => $paymentId,
                'status' => $paymentStatus,
                'invoice_id' => $payment->invoice_id
            ]);
        }

            if ($paymentStatus == 'success' && $payment && $payment->invoice_id) {
                session()->put('last_invoice_id', $payment->invoice_id);
                cookie()->queue('last_invoice_id', $payment->invoice_id, 60); // 60 menit
            }
            
            // Redirect ke order success dengan invoice_id di query string sebagai fallback
            $redirectUrl = route('order.success');
            if ($paymentStatus == 'success' && $payment && $payment->invoice_id) {
                $redirectUrl .= '?invoice_id=' . $payment->invoice_id;
            }
            
            return redirect($redirectUrl);
        } catch (\Exception $e) {
            \Log::error('Midtrans callback error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);
            return redirect()->route('produk')->with('error', 'Terjadi kesalahan saat memproses pembayaran. Silakan hubungi admin.');
        }
    }

    public function viewInvoice($id)
    {
        $invoice = HInvoice::with('customer')->findOrFail($id);

        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $cartItems = DB::table('dinvoice')
            ->leftJoin('product_variants', 'dinvoice.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'dinvoice.*',
                'products.name as product_name',
                'products.price as product_price',
                'product_variants.color as variant_color',
                'dinvoice.price as harga_custom',
                'dinvoice.quantity',
                'dinvoice.subtotal',
                'dinvoice.kebutuhan_custom',
                'dinvoice.bahan_custom',
                'dinvoice.warna_custom',
                'dinvoice.ukuran_custom',
                'dinvoice.jumlah_ring_custom',
                'dinvoice.pakai_tali_custom',
                'dinvoice.catatan_custom',
                'dinvoice.selected_size'
            )
            ->where('dinvoice.hinvoice_id', $invoice->id)
            ->get();

        if ($cartItems->isEmpty()) {
            $cartItems = DB::table('cart')
                ->leftJoin('product_variants', 'cart.variant_id', '=', 'product_variants.id')
                ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                ->select(
                    'cart.*',
                    'products.name as product_name',
                    'products.price as product_price',
                    'product_variants.color as variant_color'
                )
                ->where('cart.user_id', $invoice->customer_id)
                ->get();
        }

        $pdf = Pdf::loadView('exports.invoiceCust_pdf', compact('invoice', 'cartItems'));

        return $pdf->stream('Invoice-' . $invoice->code . '.pdf');
    }

    public function downloadInvoice($id)
    {
        $invoice = DB::table('hinvoice')->where('id', $id)->first();

        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $cartItems = DB::table('dinvoice')
            ->leftJoin('product_variants', 'dinvoice.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'dinvoice.*',
                'products.name as product_name',
                'products.price as product_price',
                'product_variants.color as variant_color',
                'dinvoice.price as harga_custom',
                'dinvoice.quantity',
                'dinvoice.subtotal',
                'dinvoice.kebutuhan_custom',
                'dinvoice.bahan_custom',
                'dinvoice.warna_custom',
                'dinvoice.ukuran_custom',
                'dinvoice.jumlah_ring_custom',
                'dinvoice.pakai_tali_custom',
                'dinvoice.catatan_custom',
                'dinvoice.selected_size'
            )
            ->where('dinvoice.hinvoice_id', $invoice->id)
            ->get();

        if ($cartItems->isEmpty()) {
            $cartItems = DB::table('cart')
                ->leftJoin('product_variants', 'cart.variant_id', '=', 'product_variants.id')
                ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                ->select(
                    'cart.*',
                    'products.name as product_name',
                    'products.price as product_price',
                    'product_variants.color as variant_color'
                )
                ->where('cart.user_id', $invoice->customer_id)
                ->get();
        }

        $pdf = Pdf::loadView('exports.invoiceCust_pdf', compact('invoice', 'cartItems'));

        return $pdf->download('Invoice-' . $invoice->code . '.pdf');
    }

    public function uploadBukti(Request $request, $id)
    {
        $request->validate([
            'photo' => 'nullable|image|max:2048',
            'signature' => 'nullable|image|max:2048',
        ]);

        $invoice = HInvoice::findOrFail($id);

        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('YmdHis');
            $randomString = \Str::random(10);
            $filename = 'delivery_proof_' . $invoice->code . '_' . $timestamp . '_' . $randomString . '_' . \Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            
            $path = $file->storeAs('delivery_proofs', $filename, 'public');
            $invoice->delivery_proof_photo = $path;
            
            \Log::info('Delivery proof photo uploaded', [
                'original_name' => $originalName,
                'unique_file_name' => $filename,
                'stored_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'invoice_id' => $invoice->id,
                'invoice_code' => $invoice->code,
                'timestamp' => $timestamp
            ]);
        }

        if ($request->hasFile('signature')) {
            $file = $request->file('signature');
            
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('YmdHis');
            $randomString = \Str::random(10);
            $filename = 'delivery_signature_' . $invoice->code . '_' . $timestamp . '_' . $randomString . '_' . \Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            
            $path = $file->storeAs('delivery_signatures', $filename, 'public');
            $invoice->delivery_signature = $path;
            
            \Log::info('Delivery signature uploaded', [
                'original_name' => $originalName,
                'unique_file_name' => $filename,
                'stored_path' => $path,
                'file_size' => $file->getSize(),
                'mime_type' => $file->getMimeType(),
                'invoice_id' => $invoice->id,
                'invoice_code' => $invoice->code,
                'timestamp' => $timestamp
            ]);
        }

        $invoice->save();

        return redirect()->back()->with('success', 'Bukti kirim berhasil diupload.');
    }

    private function validateStockAvailability($cartIds)
    {
        $carts = DB::table('cart')->whereIn('id', $cartIds)->get();
        $insufficientStock = [];

        foreach ($carts as $cart) {
            if ($cart->variant_id) {
                $productVariant = DB::table('product_variants')
                    ->join('products', 'product_variants.product_id', '=', 'products.id')
                    ->where('product_variants.id', $cart->variant_id)
                    ->select('products.name', 'product_variants.color', 'product_variants.stock')
                    ->first();

                if ($productVariant) {
                    if ($productVariant->stock < $cart->quantity) {
                        $insufficientStock[] = "{$productVariant->name} - {$productVariant->color} (Stok: {$productVariant->stock}, Diminta: {$cart->quantity})";
                    }
                }
            }
        }

        if (!empty($insufficientStock)) {
            return [
                'valid' => false,
                'message' => 'Stok tidak mencukupi untuk produk berikut: ' . implode(', ', $insufficientStock)
            ];
        }

        return ['valid' => true, 'message' => ''];
    }

    private function reduceStockFromCart($cartIds)
    {
        $carts = DB::table('cart')->whereIn('id', $cartIds)->get();
        foreach ($carts as $cart) {
            if ($cart->variant_id) {
                $productVariant = DB::table('product_variants')
                    ->where('id', $cart->variant_id)
                    ->first();

                if ($productVariant && $productVariant->stock >= $cart->quantity) {
                    DB::table('product_variants')
                        ->where('id', $cart->variant_id)
                        ->update([
                            'stock' => $productVariant->stock - $cart->quantity
                        ]);
                    
                    // Log pengurangan stok
                    \Log::info("Stok berkurang: Product Variant ID {$cart->variant_id}, Qty: {$cart->quantity}, Stok baru: " . ($productVariant->stock - $cart->quantity));
                } else {
                    // Log error jika stok tidak cukup
                    \Log::error("Stok tidak cukup: Product Variant ID {$cart->variant_id}, Stok tersedia: {$productVariant->stock}, Qty diminta: {$cart->quantity}");
                }
            } elseif ($cart->kebutuhan_custom) {
                // Stok custom biasanya dikelola secara terpisah atau tidak ada stok
                \Log::info("Produk custom tidak mengurangi stok: {$cart->kebutuhan_custom}");
            }
        }
    }
}
