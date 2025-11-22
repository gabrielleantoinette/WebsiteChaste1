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
        $user = Session::get('user');
        $userId = is_array($user)
        ? $user['id']
        : $user->id;
        $neg = NegotiationTable::firstOrNew(
            ['user_id'    => $userId,
             'product_id' => $product->id]
        );

        $sizes = ['2x3', '3x4', '4x6', '6x8'];
        $sizeOptions = collect($sizes)->map(function ($size) use ($product) {
            return [
                'size' => $size,
                'price' => $product->getPriceForSize($size),
                'min_price' => $product->getMinPriceForSize($size),
            ];
        });

        return view('negosiasi', [
            'product' => $product,
            'neg'     => $neg,
            'sizeOptions' => $sizeOptions,
        ]);
    }

    public function tawar(Request $request, Product $product)
    {
        $request->validate([
            'harga' => 'required|integer|min:1',
            'quantity' => 'required|integer|min:1',
            'selected_size' => 'nullable|in:2x3,3x4,4x6,6x8'
        ]);

        $user  = Session::get('user');
        $userId = is_array($user)
        ? $user['id']
        : $user->id;

        $offer = (int) $request->input('harga');
        $quantity = (int) $request->input('quantity');
        $selectedSize = $request->input('selected_size', '2x3');
        
        $minBuyingStock = $product->min_buying_stock ?? 1;
        if ($quantity < $minBuyingStock) {
            return back()->with('error', "Minimal {$minBuyingStock} pcs untuk tawar menawar.");
        }
        
        session(['quantity' => $quantity, 'selected_size' => $selectedSize]);

        $neg = NegotiationTable::firstOrCreate(
            ['user_id'    => $userId,
             'product_id' => $product->id],
            ['status'      => 'proses',
             'final_price' => 0]
        );

        $attempts = collect([
            $neg->cust_nego_1,
            $neg->cust_nego_2,
            $neg->cust_nego_3
        ])->filter()->count();

        if ($attempts >= 3) {
            return back()->with('error', 'Sudah mencapai maksimal 3 kali tawar.');
        }

        $priceForSize = $product->getPriceForSize($selectedSize);
        $min = $product->getMinPriceForSize($selectedSize);
        $max = $priceForSize;
        
        if ($offer < ($max * 0.5)) {
            return back()->with('error', "Tawaran Anda Rp " . number_format($offer, 0, ',', '.') . " terlalu rendah. Silakan tawar minimal 50% dari harga normal (Rp " . number_format($max * 0.5, 0, ',', '.') . ").");
        }
        
        if ($offer >= $max) {
            return back()->with('error', "Tawaran Anda Rp " . number_format($offer, 0, ',', '.') . " sudah mencapai atau melebihi harga normal Rp " . number_format($max, 0, ',', '.') . ". Tidak perlu tawar lagi, silakan beli dengan harga normal.");
        }
        
        $discountPercentage = (($max - $offer) / $max) * 100;
        $difference = $max - $offer;
        
        if ($discountPercentage >= 40) {
            $percentage = rand(20, 30) / 100;
            $response = (int) ($offer + ($difference * $percentage));
        } elseif ($discountPercentage >= 25) {
            $percentage = rand(30, 40) / 100;
            $response = (int) ($offer + ($difference * $percentage));
        } elseif ($discountPercentage >= 15) {
            $percentage = rand(40, 50) / 100;
            $response = (int) ($offer + ($difference * $percentage));
        } else {
            $percentage = rand(50, 70) / 100;
            $response = (int) ($offer + ($difference * $percentage));
        }
        
        if ($response <= $offer) {
            $response = (int) ($offer + 1000);
        }
        
        if ($response > $max) {
            $response = (int) $max;
        }
        
        if ($response < $min) {
            if ($min > $offer) {
                $response = (int) $min;
            } else {
                $response = (int) ($offer + 1000);
            }
        }

        $no = $attempts + 1;
        $neg->{"cust_nego_$no"}   = $offer;
        $neg->{"seller_nego_$no"} = $response;
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

        NegotiationTable::where('user_id', $userId)
                        ->where('product_id', $product->id)
                        ->delete();

        return redirect()
            ->route('produk.negosiasi', $product)
            ->with('success', 'Negosiasi telah di-reset.');
    }

}
