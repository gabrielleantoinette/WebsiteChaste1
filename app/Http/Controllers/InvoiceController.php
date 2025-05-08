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
use App\Models\PaymentModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Midtrans\Config;
use Midtrans\Snap;
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
        $alamat = $request->input('alamat');

        $code = 'INV-' . date('Ymd') . '-' . Str::random(5);

        $subtotalProduk = 0;

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

        // ⬇️ Ubah ini supaya insert sekaligus ambil ID
        $newInvoiceId = DB::table('hinvoice')->insertGetId([
            'code' => $code,
            'customer_id' => $customerId,
            'employee_id' => 1,
            'driver_id' => null,
            'gudang_id' => null,
            'accountant_id' => null,
            'grand_total' => $subtotalProduk,
            'status' => 'dikemas',
            'address' => $alamat,
            'is_online' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Midtrans
        $customer = Customer::find($customerId);
        $payload = [
            'transaction_details' => [
                'order_id'      => $newInvoiceId,
                'gross_amount'  => $subtotalProduk,
            ],
            'customer_details' => [
                'first_name'    => $customer->name,
                'email'         => $customer->email,
                // 'phone'         => '08888888888',
                // 'address'       => '',
            ],
            'item_details' => [
                [
                    'id'       => $newInvoiceId,
                    'price'    => $subtotalProduk,
                    'quantity' => 1,
                    'name'     => 'Invoice ' . $code
                ]
            ]
        ];
        $snapToken = Snap::getSnapToken($payload);

        $newPayment = new PaymentModel();
        $newPayment->invoice_id = $newInvoiceId;
        $newPayment->method = 'midtrans';
        $newPayment->amount = $subtotalProduk;
        $newPayment->snap_token = $snapToken;
        $newPayment->status = 'waiting';
        $newPayment->save();

        // ⬇️ Simpan ID invoice baru ke session
        session()->put('last_invoice_id', $newInvoiceId);

        return redirect()->route('order.success', ['snapToken' => $snapToken]);
    }


    // public function download()
    // {
    //     return response()->download(public_path('contoh-invoice.pdf'));
    // }

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
}
