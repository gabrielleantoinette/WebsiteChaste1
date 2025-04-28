<?php

namespace App\Http\Controllers;

use App\Models\HInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class GudangController extends Controller
{
    public function viewTransaksiGudang()
    {
        $invoices = HInvoice::where('status', 'dikemas')->whereNull('gudang_id')->get();
        return view('admin.gudang-transaksi.view', compact('invoices'));
    }

    public function detailTransaksiGudang($id)
    {
        $invoice = HInvoice::with('customer', 'gudang')->findOrFail($id); // Pakai Eloquent + eager load relasi

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


        return view('admin.gudang-transaksi.detail', compact('invoice', 'cartItems'));
    }

    public function assignGudang($id)
    {
        $invoice = HInvoice::find($id);
        $user = Session::get('user');
        $invoice->gudang_id = $user->id;
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil menyiapkan barang');
    }
}
