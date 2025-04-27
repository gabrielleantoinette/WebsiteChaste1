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
// use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use stdClass;

class InvoiceController extends Controller
{
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
        $request->validate([
            'address' => 'required|string',
            'cart_ids' => 'required|array',
            'shipping_method' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $customerId = auth()->user()->id;

        // Ambil semua barang yang dipilih
        $cartItems = Cart::whereIn('id', $request->cart_ids)->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Tidak ada barang yang dipilih.');
        }

        // Hitung subtotal produk
        $subtotalProduk = 0;
        foreach ($cartItems as $item) {
            $subtotalProduk += $item->quantity * $item->price;
        }

        // Ongkir fix misal Rp 19.000 kalau ekspedisi, Rp 0 kalau kurir perusahaan
        $shippingCost = $request->shipping_method == 'expedition' ? 19000 : 0;
        $grandTotal = $subtotalProduk + $shippingCost;

        // Buat hinvoice baru
        $invoice = Hinvoice::create([
            'code' => 'INV-' . strtoupper(Str::random(8)),
            'customer_id' => $customerId,
            'grand_total' => $grandTotal,
            'status' => 'Pending',
            'address' => $request->address,
            'is_paid' => 0,
            'is_online' => 1,
        ]);

        // (Optional) Kalau mau simpan detail item satu-satu ke tabel lain (hinvoice_items), bisa lanjut disini

        // Hapus barang di cart
        Cart::whereIn('id', $request->cart_ids)->delete();

        return redirect()->route('order.success')->with('success', 'Pesanan berhasil dibuat!');
    }
}
