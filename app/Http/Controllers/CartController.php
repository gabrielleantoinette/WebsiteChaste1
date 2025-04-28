<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function view()
    {
        $user = Session::get('user');
        $cartItems = Cart::where('user_id', $user['id'])->get(); // <- pastikan ini

        return view('cart', compact('cartItems'));
    }

    public function addItem(Request $request, $id)
    {
        $variantId = $request->variant_id;
        $quantity = $request->quantity;

        $user = Session::get('user');

        $cartExist = Cart::where('user_id', $user['id'])->where('variant_id', $variantId)->first();

        if ($cartExist) {
            $cartExist->quantity += $quantity;

            if ($cartExist->quantity <= 0) {
                $cartExist->delete();
            }

            $cartExist->save();
        } else {
            Cart::create([
                'user_id' => $user['id'],                
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('keranjang');
    }

    public function addCustomItem(Request $request)
    {
        $user = Session::get('user');

        $validated = $request->validate([
            'harga_custom' => 'required|numeric',
            'kebutuhan_custom' => 'nullable|string',
            'ukuran_custom' => 'nullable|string',
            'warna_custom' => 'nullable|string',
            'jumlah_ring_custom' => 'nullable|string',
            'pakai_tali_custom' => 'nullable|string',
            'catatan_custom' => 'nullable|string',
            'quantity' => 'required|integer|min:1', // <-- TAMBAHKAN VALIDASI INI
        ]);

        $cart = new Cart();
        $cart->user_id = $user['id'];
        $cart->variant_id = 0;
        $cart->quantity = $validated['quantity']; // <-- GANTI dari 1 menjadi dari form
        $cart->harga_custom = $validated['harga_custom'];
        $cart->kebutuhan_custom = $validated['kebutuhan_custom'];
        $cart->ukuran_custom = $validated['ukuran_custom'];
        $cart->warna_custom = $validated['warna_custom'];
        $cart->jumlah_ring_custom = $validated['jumlah_ring_custom'];
        $cart->pakai_tali_custom = $validated['pakai_tali_custom'];
        $cart->catatan_custom = $validated['catatan_custom'];
        $cart->save();

        return redirect()->route('keranjang')->with('success', 'Custom Terpal berhasil ditambahkan ke keranjang.');
    }



    public function deleteItem($id)
    {
        $cart = Cart::find($id);
        $cart->delete();

        return redirect()->route('keranjang');
    }
}
