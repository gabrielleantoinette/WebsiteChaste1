<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\HInvoice;
use App\Models\OrderModel;
use App\Models\Cart;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class LaporanController extends Controller
{
    public function penjualanPDF()
    {
        $orderCarts = OrderModel::pluck('cart_ids'); 

        $cartIds = $orderCarts->flatMap(function ($json) {
            return json_decode($json, true) ?? [];
        })->unique();

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

    public function customerPDF()
    {
        $allCustomers = Customer::all();
        $now = Carbon::now();

        // Total pelanggan
        $totalCustomers = $allCustomers->count();

        // Pelanggan baru (dalam 30 hari terakhir)
        $newCustomers = $allCustomers->filter(function ($c) use ($now) {
            return $c->created_at >= $now->subDays(30);
        });
        $newCustomerCount = $newCustomers->count();
        $newCustomerPercentage = $totalCustomers > 0 ? round(($newCustomerCount / $totalCustomers) * 100, 2) : 0;

        // Hitung pelanggan yang pernah beli
        $invoiceGroups = HInvoice::select('customer_id')
            ->groupBy('customer_id')
            ->selectRaw('count(*) as order_count, customer_id')
            ->get();

        $repeatOrderCount = $invoiceGroups->where('order_count', '>', 1)->count();
        $oneTimeCustomerCount = $invoiceGroups->where('order_count', 1)->count();

        // Berdasarkan lokasi (city)
        $locationStats = $allCustomers->groupBy('city')->map(function ($group) {
            return $group->count();
        });

        // Umur rata-rata
        $validCustomers = $allCustomers->filter(fn($c) => $c->birth_date);
        $avgAge = $validCustomers->count() > 0
            ? round($validCustomers->avg(fn($c) => Carbon::parse($c->birth_date)->age), 1)
            : null;

        $pdf = Pdf::loadView('exports.laporan-customer', compact(
            'totalCustomers', 'newCustomerCount', 'newCustomerPercentage',
            'repeatOrderCount', 'oneTimeCustomerCount', 'locationStats', 'avgAge'
        ));

        return $pdf->download('laporan-customer.pdf');
    }
}
