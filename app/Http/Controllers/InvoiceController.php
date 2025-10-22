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

    public function view()
    {
        $invoices = HInvoice::all();
        return view('admin.invoices.view', compact('invoices'));
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
        $invoice = HInvoice::find($id);
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
        $paymentMethod = $request->input('payment_method');
        $shippingCost = $request->input('shipping_cost');
        $alamat = $request->input('address');
        $invoiceCode = 'INV-' . date('Ymd') . '-' . Str::random(5);
        $isFirstOrder = HInvoice::where('customer_id', $customerId)->count() == 0;
        // Validasi upload bukti transfer jika transfer bank
        if ($paymentMethod == 'transfer') {
            $request->validate([
                'bukti_transfer' => 'required|image|max:2048',
            ]);
        }

        // cari apakah ini pesanan pertama customer?
        $isFirstOrder = HInvoice::where('customer_id', $customerId)->count() == 0;

        // Validasi limit hutang 10 juta untuk customer langganan
        if ($paymentMethod == 'hutang') {
            if ($isFirstOrder) {
                return redirect()->back()->with('error', 'Anda harus minimal 1x transaksi lunas sebelum boleh berhutang.');
            }
            
            // Hitung total hutang customer saat ini
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

        // Validasi stok sebelum membuat transaksi
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
                    $product = DB::table('product_variants')
                        ->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->where('product_variants.id', $cart->variant_id)
                        ->select('products.price')
                        ->first();
                    // Gunakan harga custom jika ada (hasil negosiasi), jika tidak gunakan harga produk normal
                    $price = $cart->harga_custom ?? $product->price;
                    $subtotalProduk += ($price ?? 0) * $cart->quantity;
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
            $transferProofPath = $request->file('bukti_transfer')->store('bukti_transfer', 'public');
        }
        // ⬇️ Ubah ini supaya insert sekaligus ambil ID
        $newInvoiceId = DB::table('hinvoice')->insertGetId([
            'code' => $invoiceCode,
            'customer_id' => $customerId,
            'employee_id' => 1,
            'address' => $alamat,
            'is_online' => 0,
            'status' => $statusInvoice,
            'is_dp' => $isFirstOrder ? true : false,
            'dp_amount' => $isFirstOrder ? $grandTotal / 2 : 0,
            'grand_total' => $grandTotal,
            'shipping_cost' => $shippingCost ?? 0,
            'due_date' => now()->addDays(30), // <-- otomatis 30 hari dari sekarang
            'created_at' => now(),
            'updated_at' => now(),
            'transfer_proof' => $transferProofPath,
        ]);

        // Midtrans
        // Hanya buat midtrans kalau payment_method adalah ewallet.
        if ($paymentMethod == 'midtrans') {
            $customer = Customer::find($customerId);
            $payload = [
                'transaction_details' => [
                    'order_id'      => now() . '-' . $newInvoiceId,
                    'gross_amount'  => $isFirstOrder ? $grandTotal / 2 : $grandTotal,
                ],
                'customer_details' => [
                    'first_name'    => $customer->name,
                    'email'         => $customer->email,
                ],
                'item_details' => [
                    [
                        'id'       => $newInvoiceId,
                        'price'    => $grandTotal,
                        'quantity' => 1,
                        'name'     => 'Invoice ' . $invoiceCode
                    ]
                ]
            ];
            $snapToken = Snap::getSnapToken($payload);

            $newPayment = new PaymentModel();
            $newPayment->invoice_id = $newInvoiceId;
            $newPayment->method = 'midtrans';
            $newPayment->type = $isFirstOrder ? 'dp' : 'full';
            $newPayment->is_paid = false;
            $newPayment->amount = $grandTotal;
            $newPayment->snap_token = $snapToken;
            $newPayment->save();

            // Simpan detail produk ke dinvoice langsung saat checkout Midtrans
            if (!empty($cartIds)) {
                // Hapus dinvoice records yang sudah ada untuk invoice ini (jika ada)
                DB::table('dinvoice')->where('hinvoice_id', $newInvoiceId)->delete();
                
                $carts = DB::table('cart')->whereIn('id', $cartIds)->get();
                
                foreach ($carts as $cart) {
                    if ($cart->variant_id) {
                        // Produk biasa
                        $product = DB::table('product_variants')
                            ->join('products', 'product_variants.product_id', '=', 'products.id')
                            ->where('product_variants.id', $cart->variant_id)
                            ->select('products.id as product_id', 'products.price')
                            ->first();
                        
                        if ($product) {
                            // Gunakan harga custom jika ada (hasil negosiasi), jika tidak gunakan harga produk normal
                            $price = $cart->harga_custom ?? $product->price;
                            DB::table('dinvoice')->insert([
                                'hinvoice_id' => $newInvoiceId,
                                'product_id' => $product->product_id,
                                'variant_id' => $cart->variant_id,
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
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    }
                }
            }
            
            // Simpan cartIds ke session untuk dihapus setelah payment sukses
            session()->put('cart_ids_to_delete', $cartIds);
            return redirect()->route('checkout.midtrans.payment', ['snapToken' => $snapToken, 'paymentId' => $newPayment->id]);
        }

        // Simpan detail produk ke tabel dinvoice
        if (!empty($cartIds)) {
            // Hapus dinvoice records yang sudah ada untuk invoice ini (jika ada)
            DB::table('dinvoice')->where('hinvoice_id', $newInvoiceId)->delete();
            
            $carts = DB::table('cart')->whereIn('id', $cartIds)->get();
            
            foreach ($carts as $cart) {
                if ($cart->variant_id) {
                    // Produk biasa
                    $product = DB::table('product_variants')
                        ->join('products', 'product_variants.product_id', '=', 'products.id')
                        ->where('product_variants.id', $cart->variant_id)
                        ->select('products.id as product_id', 'products.price')
                        ->first();
                    
                    if ($product) {
                        // Gunakan harga custom jika ada (hasil negosiasi), jika tidak gunakan harga produk normal
                        $price = $cart->harga_custom ?? $product->price;
                        DB::table('dinvoice')->insert([
                            'hinvoice_id' => $newInvoiceId,
                            'product_id' => $product->product_id,
                            'variant_id' => $cart->variant_id,
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
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        }

        // Kurangi stok untuk transaksi non-midtrans
        if (!empty($cartIds)) {
            $this->reduceStockFromCart($cartIds);
        }

        // Tambahkan: untuk metode hutang/cod/transfer_bank, insert ke tabel payment
        if (in_array($paymentMethod, ['cod', 'hutang', 'transfer_bank'])) {
            PaymentModel::create([
                'invoice_id' => $newInvoiceId,
                'method' => $paymentMethod,
                'type' => 'full',
                'is_paid' => 0,
                'amount' => $grandTotal,
            ]);
        }

        // Hapus item keranjang yang sudah di-checkout (untuk cod/hutang/transfer_bank)
        if (!empty($cartIds)) {
            DB::table('cart')->whereIn('id', $cartIds)->delete();
        }

        // Kirim notifikasi pesanan baru ke admin
        $customer = Customer::find($customerId);
        $notificationService = app(NotificationService::class);
        $notificationService->notifyNewOrder($newInvoiceId, [
            'customer_name' => $customer->name,
            'invoice_code' => $invoiceCode,
            'total_amount' => $grandTotal
        ]);

        // Kirim notifikasi ke owner tentang action customer
        $notificationService->notifyCustomerAction([
            'message' => "Customer {$customer->name} telah membuat pesanan baru dengan kode {$invoiceCode}",
            'action_id' => $newInvoiceId,
            'action_url' => "/admin/invoices/detail/{$newInvoiceId}",
            'priority' => 'high'
        ]);

        // Kirim notifikasi ke customer
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
        $paymentStatus = $request->query('status');

        $paymentId = $request->query('paymentId');
        $payment = PaymentModel::find($paymentId);
        $payment->is_paid = $paymentStatus == 'success' ? true : false;
        $payment->save();

        // Set session last_invoice_id agar halaman sukses tidak error
        session()->put('last_invoice_id', $payment->invoice_id);

        // Hapus cart setelah pembayaran sukses (midtrans)
        if ($paymentStatus == 'success' && session()->has('cart_ids_to_delete')) {
            $cartIds = session('cart_ids_to_delete');
            if (!empty($cartIds)) {
                // Validasi stok sebelum mengurangi (untuk memastikan tidak ada perubahan stok selama proses pembayaran)
                $stockValidation = $this->validateStockAvailability($cartIds);
                if ($stockValidation['valid']) {
                    // Kurangi stok sebelum menghapus cart
                    $this->reduceStockFromCart($cartIds);
                    DB::table('cart')->whereIn('id', $cartIds)->delete();
                } else {
                    // Jika stok tidak cukup, kembalikan pembayaran atau handle sesuai kebijakan
                    \Log::error("Stok tidak cukup setelah pembayaran midtrans: " . $stockValidation['message']);
                }
            }
            session()->forget('cart_ids_to_delete');
        }

        // Kirim notifikasi ke customer jika pembayaran berhasil
        if ($paymentStatus == 'success') {
            $invoice = HInvoice::find($payment->invoice_id);
            $customer = Customer::find($invoice->customer_id);
            
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

            // Kirim notifikasi ke admin tentang pembayaran berhasil
            $notificationService->notifyPayment($payment->id, [
                'amount' => $payment->amount,
                'customer_name' => $customer->name,
                'invoice_code' => $invoice->code
            ]);
        }

        // Update Invoice setelah payment berhasil
        if ($paymentStatus == 'success') {
            $invoice = HInvoice::find($payment->invoice_id);
            if ($invoice) {
                $invoice->status = 'Dikemas'; // Langsung ubah ke status Dikemas agar masuk ke gudang
                $invoice->is_paid = 1;
                $invoice->paid_amount = $payment->amount;
                $invoice->save();
                
                // Kirim notifikasi ke owner tentang pesanan baru
                $notificationService = app(NotificationService::class);
                $notificationService->notifyOrderCreated($invoice->id, $customer->id, [
                    'total_amount' => $payment->amount,
                    'invoice_code' => $invoice->code,
                    'customer_name' => $customer->name
                ]);
                
                // Kirim notifikasi ke gudang tentang pesanan baru yang perlu diproses
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
                
                // Kirim notifikasi ke owner tentang pesanan baru
                $notificationService->notifyWarehouseAction([
                    'message' => "Pesanan baru {$invoice->code} dari {$customer->name} sebesar Rp " . number_format($payment->amount) . " siap untuk diproses",
                    'action_id' => $invoice->id,
                    'action_url' => "/admin/gudang-transaksi/detail/{$invoice->id}",
                    'priority' => 'high'
                ]);
                
                // Kirim notifikasi ke admin tentang pesanan baru
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
                
                // Cek apakah dinvoice sudah ada (seharusnya sudah dibuat saat checkout)
                $existingDInvoice = DB::table('dinvoice')->where('hinvoice_id', $invoice->id)->first();
                if (!$existingDInvoice) {
                    \Log::warning("No dinvoice data found for invoice {$invoice->id}. This should not happen.");
                }
            }
        }

        return redirect()->route('order.success');
    }

    public function viewInvoice($id)
    {
        $invoice = HInvoice::with('customer')->findOrFail($id);

        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        // Ambil data dari dinvoice yang sudah dibuat saat pembayaran
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
                'dinvoice.subtotal'
            )
            ->where('dinvoice.hinvoice_id', $invoice->id)
            ->get();

        // Jika tidak ada data di dinvoice, coba ambil dari cart (untuk transaksi lama)
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

        // Ambil data dari dinvoice yang sudah dibuat saat pembayaran
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
                'dinvoice.subtotal'
            )
            ->where('dinvoice.hinvoice_id', $invoice->id)
            ->get();

        // Jika tidak ada data di dinvoice, coba ambil dari cart (untuk transaksi lama)
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

        $invoice = HInvoice::findOrFail($id); // Pakai model kamu

        if ($request->hasFile('photo')) {
            $invoice->delivery_proof_photo = $request->file('photo')->store('delivery_proofs', 'public');
        }

        if ($request->hasFile('signature')) {
            $invoice->delivery_signature = $request->file('signature')->store('delivery_signatures', 'public');
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
            // Untuk produk custom, tidak perlu validasi stok
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
                // Ambil stok saat ini dari product_variants
                $productVariant = DB::table('product_variants')
                    ->where('id', $cart->variant_id)
                    ->first();

                if ($productVariant && $productVariant->stock >= $cart->quantity) {
                    // Kurangi stok
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
                // Untuk produk custom, stok tidak dikelola di tabel product_variants
                // Stok custom biasanya dikelola secara terpisah atau tidak ada stok
                \Log::info("Produk custom tidak mengurangi stok: {$cart->kebutuhan_custom}");
            }
        }
    }
}
