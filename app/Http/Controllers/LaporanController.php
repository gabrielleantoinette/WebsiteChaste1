<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\OrderModel;
use App\Models\Cart;
use Barryvdh\DomPDF\Facade\Pdf;

class LaporanController extends Controller
{
    public function penjualanPDF()
    {
        $orderCarts = OrderModel::pluck('cart_ids'); // contoh: ["[1,2]", "[3]"]

        // Ubah jadi array integer [1,2,3]
        $cartIds = $orderCarts->flatMap(function ($json) {
            return json_decode($json, true) ?? [];
        })->unique();

        // Ambil cart yang hanya dipakai di transaksi
        $details = Cart::with('variant.product')
            ->whereIn('id', $cartIds)
            ->get();

        $topProducts = $details->groupBy('variant_id')->map(function ($group) {
            return [
                'product' => $group->first()->variant->product->name ?? '-',
                'variant' => $group->first()->variant->variant_name ?? '-',
                'jumlah_terjual' => $group->sum('quantity'),
            ];
        })->sortByDesc('jumlah_terjual')->take(5);

        $topColors = $details->groupBy('variant.color')->map(function ($group, $color) {
            return [
                'warna' => $color ?? '-',
                'jumlah_terjual' => $group->sum('quantity'),
            ];
        })->sortByDesc('jumlah_terjual')->take(5);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('exports.laporan-penjualan', compact('topProducts', 'topColors'));
        return $pdf->download('laporan-penjualan.pdf');
    }
}
