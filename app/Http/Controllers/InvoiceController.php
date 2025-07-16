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

        // Tidak boleh hutang jika first order
        if ($paymentMethod == 'hutang' && $isFirstOrder) {
            return redirect()->back()->with('error', 'Anda harus minimal 1x transaksi lunas sebelum boleh berhutang.');
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
                    $subtotalProduk += ($product->price ?? 0) * $cart->quantity;
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
                        'price'    => $isFirstOrder ? $grandTotal / 2 : $grandTotal,
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
            $newPayment->amount = $isFirstOrder ? $grandTotal / 2 : $grandTotal;
            $newPayment->snap_token = $snapToken;
            $newPayment->save();

            // Simpan cartIds ke session untuk dihapus setelah payment sukses
            session()->put('cart_ids_to_delete', $cartIds);
            return redirect()->route('checkout.midtrans.payment', ['snapToken' => $snapToken, 'paymentId' => $newPayment->id]);
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
                DB::table('cart')->whereIn('id', $cartIds)->delete();
            }
            session()->forget('cart_ids_to_delete');
        }

        // TODO Update Invoice setelah payment berhasil.

        return redirect()->route('order.success');
    }

    public function viewInvoice($id)
    {
        $invoice = HInvoice::with('customer')->findOrFail($id);

        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        // JOIN cart ke product_variants dan products untuk ambil nama produk
        $cartItems = DB::table('cart')
            ->leftJoin('product_variants', 'cart.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'cart.*',
                'products.name as product_name',
                'products.price as product_price',  // INI PENTING!
                'product_variants.color as variant_color'
            )
            ->where('cart.user_id', $invoice->customer_id)
            ->get();

        $pdf = Pdf::loadView('exports.invoiceCust_pdf', compact('invoice', 'cartItems'));

        return $pdf->stream('Invoice-' . $invoice->code . '.pdf');
    }

    public function downloadInvoice($id)
    {
        $invoice = DB::table('hinvoice')->where('id', $id)->first();

        if (!$invoice) {
            abort(404, 'Invoice tidak ditemukan.');
        }

        $cartItems = DB::table('cart')
            ->leftJoin('product_variants', 'cart.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'cart.*',
                'products.name as product_name',
                'products.price as product_price',  // INI PENTING!
                'product_variants.color as variant_color'
            )
            ->where('cart.user_id', $invoice->customer_id)
            ->get();

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
}
