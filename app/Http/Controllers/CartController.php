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
        $cart = Cart::where('user_id', $user['id'])->get();

        return view('cart', compact('cart'));
    }

    public function addItem(Request $request, $id)
    {
        $variantId = $request->variant_id;
        $quantity = $request->quantity;

        $user = Session::get('user');

        $cartExist = Cart::where('user_id', $user->id)->where('variant_id', $variantId)->first();

        if ($cartExist) {
            $cartExist->quantity += $quantity;

            if ($cartExist->quantity <= 0) {
                $cartExist->delete();
            }

            $cartExist->save();
        } else {
            Cart::create([
                'user_id' => $user->id,
                'variant_id' => $variantId,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->route('keranjang');
    }

    public function addCustomItem(Request $request)
    {
        $user = Session::get('user');
        $userId = $user ? $user['id'] : null;

        if (!$userId) {
            return back()->withErrors('User belum login.');
        }

        $validated = $request->validate([
            'bahan' => 'required|integer',
            'warna' => 'required|string',
            'kebutuhan' => 'required|string',
            'panjang' => 'required|numeric',
            'lebar' => 'required|numeric',
            'tinggi' => 'nullable|numeric',
            'jumlah_ring' => 'required|integer',
            'pakai_tali' => 'nullable|boolean',
            'catatan' => 'nullable|string',
            'harga_custom' => 'required|numeric', // <<< tambahkan validasi harga custom
        ]);

        \DB::table('cart')->insert([
            'user_id' => $userId,
            'variant_id' => null, // karena ini custom, tidak ada variant
            'quantity' => 1, // custom terpal 1 pesanan
            'harga_custom' => $validated['harga_custom'], // simpan harga custom
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('keranjang')->with('success', 'Custom Terpal berhasil ditambahkan ke keranjang.');
    }


    public function deleteItem($id)
    {
        $cart = Cart::find($id);
        $cart->delete();

        return redirect()->route('keranjang');
    }
}
