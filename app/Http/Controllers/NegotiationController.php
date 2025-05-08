<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\NegotiationTable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class NegotiationController extends Controller
{
    public function show(Product $product)
    {
        $user = Session::get('user');  // ambil object user dari session
        $userId = is_array($user)
        ? $user['id']
        : $user->id;
        $neg = NegotiationTable::firstOrNew(
            ['user_id'    => $userId,
             'product_id' => $product->id]
        );

        return view('negosiasi', [
            'product' => $product,
            'neg'     => $neg,
        ]);
    }

    public function tawar(Request $request, Product $product)
    {
        $request->validate(['harga' => 'required|integer|min:1']);

        $user  = Session::get('user');
        $userId = is_array($user)
        ? $user['id']
        : $user->id;

        $offer = (int) $request->input('harga');

        $neg = NegotiationTable::firstOrCreate(
            ['user_id'    => $userId,          // ← pakai $user->id
             'product_id' => $product->id],
            ['status'      => 'proses',
             'final_price' => 0]
        );

        // Hitung berapa kali customer sudah menawar
        $attempts = collect([
            $neg->cust_nego_1,
            $neg->cust_nego_2,
            $neg->cust_nego_3
        ])->filter()->count();

        if ($attempts >= 3) {
            return back()->with('error', 'Sudah mencapai maksimal 3 kali tawar.');
        }

        // Logika sistem merespons
        $min = $product->min_price;  // kolom min_price harus ada di products
        $max = $product->price;

        if ($offer <= $min) {
            $response = $offer;
        } else {
            // Contoh counter: setengah jalan
            $response = (int) ceil($max - (($max - $offer) / 2));
        }

        // Simpan ke kolom attempt berikutnya
        $no = $attempts + 1;
        $neg->{"cust_nego_$no"}   = $offer;
        $neg->{"seller_nego_$no"} = $response;

        // Kalau ini tawaran ke-3, jadikan final
        if ($no === 3) {
            $neg->status      = 'final';
            $neg->final_price = $response;
        }

        $neg->save();

        return redirect()
        ->route('produk.negosiasi', $product)
        ->with('success', "Tawaran #{$no} diproses");

    }

    public function reset(Product $product)
    {
        $user = Session::get('user');
        $userId = is_array($user) ? $user['id'] : $user->id;

        // Hapus record negosiasi untuk user + product
        NegotiationTable::where('user_id', $userId)
                        ->where('product_id', $product->id)
                        ->delete();

        return redirect()
            ->route('produk.negosiasi', $product)
            ->with('success', 'Negosiasi telah di-reset.');
    }

}
