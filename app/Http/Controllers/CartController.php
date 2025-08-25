<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
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

    public function addItem(Request $request)
    {
        $variantId = $request->variant_id;
        $quantity = $request->quantity;

        $user = Session::get('user');

        // Validasi stok sebelum menambahkan ke cart
        $productVariant = DB::table('product_variants')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->where('product_variants.id', $variantId)
            ->select('products.name', 'product_variants.color', 'product_variants.stock')
            ->first();

        if (!$productVariant) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // Cek apakah sudah ada di cart
        $cartExist = Cart::where('user_id', $user['id'])->where('variant_id', $variantId)->first();
        $totalQuantity = $quantity;
        
        if ($cartExist) {
            $totalQuantity += $cartExist->quantity;
        }

        // Validasi stok
        if ($productVariant->stock < $totalQuantity) {
            return redirect()->back()->with('error', "Stok tidak mencukupi. Stok tersedia: {$productVariant->stock}, Total yang diminta: {$totalQuantity}");
        }

        if ($cartExist) {
            $cartExist->quantity += $quantity;

            if ($cartExist->quantity <= 0) {
                $cartExist->delete();
            } else {
                $cartExist->save();
            }
        } else {
            Cart::create([
                'user_id' => $user['id'],
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('keranjang')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
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
