<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;

class CheckoutController extends Controller
{
    public function index()
    {
        // Cek apakah session login ada
        if (!session()->has('isLoggedIn')) {
            return redirect()->route('login')->with('error', 'Silahkan login dahulu untuk checkout.');
        }

        // Ambil id customer dari session
        $customerId = session()->get('customer_id'); // Pastikan di loginmu simpan 'customer_id'

        // Kalau customer_id tidak ketemu, redirect
        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Session habis, silahkan login ulang.');
        }

        // Ambil cart items berdasarkan customer_id
        $checkoutItems = Cart::where('user_id', $customerId)->get();

        // Hitung subtotal
        $subtotalProduk = 0;
        foreach ($checkoutItems as $item) {
            $variant = \App\Models\ProductVariant::find($item->variant_id);
            $price = $variant ? $variant->price : 0;
        
            $subtotalProduk += $item->quantity * $price;
        }

        $subtotalPengiriman = 19000;

        // Ambil alamat dari session (kalau kamu simpan alamat)
        $alamat_default_user = session()->get('customer_address', '');

        return view('checkout', compact('checkoutItems', 'subtotalProduk', 'subtotalPengiriman', 'alamat_default_user'));
    }
}
