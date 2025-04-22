<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CartController extends Controller
{
    public function view()
    {
        $user = Session::get('user');
        $cart = Cart::where('user_id', $user->id)->get();

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

    public function deleteItem($id)
    {
        $cart = Cart::find($id);
        $cart->delete();

        return redirect()->route('keranjang');
    }
}
