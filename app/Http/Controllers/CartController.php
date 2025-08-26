<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Product; // Added this import for Product model
use App\Models\ProductVariant; // Added this import for ProductVariant model

class CartController extends Controller
{
    public function view()
    {
        $user = Session::get('user');
        $cartItems = Cart::where('user_id', $user['id'])
                        ->with(['variant.product']) // Load relationship
                        ->get();

        return view('cart', compact('cartItems'));
    }

    public function addItem(Request $request, $id)
    {
        $user = Session::get('user');
        $quantity = $request->quantity ?? 1;
        $negotiatedPrice = $request->negotiated_price ?? null;

        // Cari produk
        $product = Product::find($id);
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // Ambil variant pertama dari produk (atau buat default)
        $variant = ProductVariant::where('product_id', $id)->first();
        if (!$variant) {
            // Jika tidak ada variant, buat default
            $variant = new ProductVariant();
            $variant->product_id = $id;
            $variant->color = 'default';
            $variant->stock = 999; // Default stock tinggi
            $variant->save();
        }

        // Cek apakah sudah ada di cart dengan variant yang sama
        $cartExist = Cart::where('user_id', $user['id'])
                        ->where('variant_id', $variant->id)
                        ->whereNull('kebutuhan_custom')
                        ->first();

        if ($cartExist) {
            // Update quantity jika sudah ada
            $cartExist->quantity += $quantity;
            $cartExist->save();
        } else {
            // Buat cart item baru
            $cart = new Cart();
            $cart->user_id = $user['id'];
            $cart->variant_id = $variant->id;
            $cart->quantity = $quantity;
            
            // Jika ada harga negosiasi, simpan sebagai harga custom
            if ($negotiatedPrice) {
                $cart->harga_custom = $negotiatedPrice;
                $cart->kebutuhan_custom = "Hasil negosiasi - Harga final: Rp " . number_format($negotiatedPrice, 0, ',', '.');
            }
            
            $cart->save();
        }

        $message = $negotiatedPrice 
            ? "Produk hasil negosiasi berhasil ditambahkan ke keranjang dengan harga Rp " . number_format($negotiatedPrice, 0, ',', '.')
            : "Produk berhasil ditambahkan ke keranjang.";

        return redirect()->route('keranjang')->with('success', $message);
    }

    public function addItemFromCart(Request $request)
    {
        $user = Session::get('user');
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Cari produk
        $product = Product::find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        // Ambil variant pertama dari produk (atau buat default)
        $variant = ProductVariant::where('product_id', $productId)->first();
        if (!$variant) {
            // Jika tidak ada variant, buat default
            $variant = new ProductVariant();
            $variant->product_id = $productId;
            $variant->color = 'default';
            $variant->stock = 999; // Default stock tinggi
            $variant->save();
        }

        // Cek apakah sudah ada di cart dengan variant yang sama
        $cartExist = Cart::where('user_id', $user['id'])
                        ->where('variant_id', $variant->id)
                        ->whereNull('kebutuhan_custom')
                        ->first();

        if ($cartExist) {
            // Update quantity jika sudah ada
            $cartExist->quantity += $quantity;
            $cartExist->save();
        } else {
            // Buat cart item baru
            $cart = new Cart();
            $cart->user_id = $user['id'];
            $cart->variant_id = $variant->id;
            $cart->quantity = $quantity;
            $cart->save();
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
