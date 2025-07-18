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
        $invoice = HInvoice::with('customer', 'gudang')->findOrFail($id);
    
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
    
        // Hitung total manual
        $total = $cartItems->reduce(function ($carry, $item) {
            $price = $item->product_price ?? $item->harga_custom;
            return $carry + ($price * $item->quantity);
        }, 0);
    
        return view('admin.gudang-transaksi.detail', compact('invoice', 'cartItems', 'total'));
    }

    public function assignGudang($id)
    {
        $invoice = HInvoice::find($id);
        $user = Session::get('user');
        $invoice->gudang_id = $user->id;
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil menyiapkan barang');
    }

    // Dashboard Gudang
    public function dashboardGudang()
    {
        $user = Session::get('user');
        if (!$user || $user->role !== 'gudang') {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }
        // Pesanan siap diproses
        $orders = HInvoice::with('customer')
            ->where('status', 'dikemas')
            ->where(function($q) use ($user) {
                $q->whereNull('gudang_id')->orWhere('gudang_id', $user->id);
            })->get();
        $siapProsesCount = $orders->count();
        // Rangkuman produk/terpal perlu disiapkan
        $produkDisiapkan = [];
        $totalProdukDisiapkan = 0;
        foreach ($orders as $order) {
            foreach ($order->cartItems ?? [] as $item) {
                $nama = $item->product_name ?? $item->nama_custom ?? 'Produk';
                $qty = $item->quantity ?? 0;
                if (!isset($produkDisiapkan[$nama])) {
                    $produkDisiapkan[$nama] = ['nama' => $nama, 'qty' => 0];
                }
                $produkDisiapkan[$nama]['qty'] += $qty;
                $totalProdukDisiapkan += $qty;
            }
        }
        $produkDisiapkan = array_values($produkDisiapkan);
        return view('admin.dashboardgudang', compact('orders', 'siapProsesCount', 'totalProdukDisiapkan', 'produkDisiapkan'));
    }
}
