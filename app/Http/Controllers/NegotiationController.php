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

        // Untuk saat ini, biarkan semua bisa akses halaman negosiasi
        // Validasi quantity akan dilakukan di frontend dan saat checkout

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

        // Logika sistem merespons yang lebih fleksibel
        $min = $product->min_price ?? ($product->price * 0.65); // Default 65% dari harga normal (lebih rendah)
        $max = $product->price;
        
        // Validasi tawaran customer (tidak reveal harga minimal)
        if ($offer < ($max * 0.5)) {
            return back()->with('error', "Tawaran terlalu rendah. Silakan tawar minimal 50% dari harga normal.");
        }
        
        if ($offer >= $max) {
            return back()->with('error', "Tawaran sudah mencapai atau melebihi harga normal. Tidak perlu tawar lagi.");
        }
        
        // Logika counter offer yang lebih dinamis
        $discountPercentage = (($max - $offer) / $max) * 100;
        
        // Sistem akan counter berdasarkan seberapa agresif tawaran customer
        if ($discountPercentage >= 40) {
            // Tawaran sangat agresif (diskon >40%), counter dengan diskon 15-20%
            $counterDiscount = rand(15, 20);
            $response = (int) ($max * (1 - ($counterDiscount / 100)));
        } elseif ($discountPercentage >= 25) {
            // Tawaran agresif (diskon 25-40%), counter dengan diskon 10-15%
            $counterDiscount = rand(10, 15);
            $response = (int) ($max * (1 - ($counterDiscount / 100)));
        } elseif ($discountPercentage >= 15) {
            // Tawaran sedang (diskon 15-25%), counter dengan diskon 5-10%
            $counterDiscount = rand(5, 10);
            $response = (int) ($max * (1 - ($counterDiscount / 100)));
        } else {
            // Tawaran ringan (diskon <15%), counter dengan diskon 3-5%
            $counterDiscount = rand(3, 5);
            $response = (int) ($max * (1 - ($counterDiscount / 100)));
        }
        
        // Pastikan response tidak lebih rendah dari tawaran customer
        if ($response <= $offer) {
            $response = (int) ($offer + ($max * 0.02)); // Tambah 2% dari harga normal
        }
        
        // Pastikan response tidak di bawah harga minimal
        if ($response < $min) {
            $response = (int) $min;
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
        ->with('success', "Tawaran #{$no} diproses")
        ->with('quantity', $request->quantity);

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
