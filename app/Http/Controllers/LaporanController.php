<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use App\Models\HInvoice;
use App\Models\OrderModel;
use App\Models\Cart;
use App\Models\Returns;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use App\Models\DInvoice;

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

    public function rataRataPDF()
    {
        $totalCustomer = Customer::count();
        $invoiceGroups = HInvoice::groupBy('customer_id')
            ->selectRaw('customer_id, COUNT(*) as jumlah_transaksi, SUM(grand_total) as total_belanja')
            ->get();

        $rataRataPerCustomer = $invoiceGroups->map(function ($row) {
            return [
                'customer_id' => $row->customer_id,
                'jumlah_transaksi' => $row->jumlah_transaksi,
                'total_belanja' => $row->total_belanja,
                'rata_rata_belanja' => $row->jumlah_transaksi > 0 ? $row->total_belanja / $row->jumlah_transaksi : 0
            ];
        });

        $rataRataGlobal = $rataRataPerCustomer->avg('rata_rata_belanja');

        $pdf = Pdf::loadView('exports.laporan-ratarata', compact('rataRataGlobal', 'rataRataPerCustomer'));
        return $pdf->download('laporan-pesanan-rata-rata.pdf');
    }

    public function returPDF()
    {
        // Ambil semua data retur dengan relasi
        $returns = Returns::with(['customer', 'invoice', 'driver'])->get();
        
        // Statistik umum
        $totalReturns = $returns->count();
        $totalCustomers = $returns->unique('customer_id')->count();
        
        // Analisis berdasarkan status
        $statusStats = $returns->groupBy('status')->map(function ($group) {
            return $group->count();
        });
        
        // Analisis berdasarkan bulan (6 bulan terakhir)
        $monthlyStats = $returns->groupBy(function ($return) {
            return Carbon::parse($return->created_at)->format('Y-m');
        })->map(function ($group) {
            return $group->count();
        })->sortKeys()->take(6);
        
        // Analisis berdasarkan customer
        $customerStats = $returns->groupBy('customer_id')->map(function ($group, $customerId) {
            $customer = $group->first()->customer;
            return [
                'customer_name' => $customer ? $customer->name : 'Unknown',
                'return_count' => $group->count(),
                'latest_return' => $group->sortByDesc('created_at')->first()->created_at
            ];
        })->sortByDesc('return_count')->take(10);
        
        // Analisis berdasarkan driver (jika ada)
        $driverStats = $returns->filter(function ($return) {
            return $return->driver;
        })->groupBy('driver.id')->map(function ($group, $driverId) {
            $driver = $group->first()->driver;
            return [
                'driver_name' => $driver ? $driver->name : 'Unknown',
                'return_count' => $group->count()
            ];
        })->sortByDesc('return_count');
        
        // Analisis produk yang diretur (melalui dinvoice)
        $productStats = collect();
        $colorStats = collect();
        
        foreach ($returns as $return) {
            if ($return->invoice) {
                // Ambil data dinvoice dari invoice
                $dInvoices = DInvoice::with(['product', 'variant'])
                    ->where('hinvoice_id', $return->invoice->id)
                    ->get();
                
                foreach ($dInvoices as $dInvoice) {
                    if ($dInvoice->product && $dInvoice->variant) {
                        $productKey = $dInvoice->product->name . ' - ' . $dInvoice->variant->variant_name;
                        
                        // Update product stats
                        $existingProduct = $productStats->firstWhere('product_key', $productKey);
                        if ($existingProduct) {
                            $existingProduct['return_count']++;
                        } else {
                            $productStats->push([
                                'product_key' => $productKey,
                                'product_name' => $dInvoice->product->name,
                                'variant_name' => $dInvoice->variant->variant_name,
                                'return_count' => 1
                            ]);
                        }
                        
                        // Update color stats
                        $color = $dInvoice->variant->color ?? 'Unknown';
                        $existingColor = $colorStats->firstWhere('color', $color);
                        if ($existingColor) {
                            $existingColor['return_count']++;
                        } else {
                            $colorStats->push([
                                'color' => $color,
                                'return_count' => 1
                            ]);
                        }
                    }
                }
            }
        }
        
        // Sort dan ambil top products/colors
        $productStats = $productStats->sortByDesc('return_count')->take(10);
        $colorStats = $colorStats->sortByDesc('return_count')->take(10);
        
        // Tambahkan informasi produk ke setiap retur
        foreach ($returns as $return) {
            $productInfo = 'N/A';
            if ($return->invoice) {
                $dInvoices = DInvoice::with(['product', 'variant'])
                    ->where('hinvoice_id', $return->invoice->id)
                    ->get();
                
                $productNames = $dInvoices->map(function ($dInvoice) {
                    if ($dInvoice->product && $dInvoice->variant) {
                        return $dInvoice->product->name . ' (' . $dInvoice->variant->variant_name . ')';
                    }
                    return 'Unknown';
                })->toArray();
                $productInfo = implode(', ', $productNames);
            }
            $return->product_info = $productInfo;
        }
        
        // Top returned product dan color
        $topReturnedProduct = $productStats->first() ? 
            $productStats->first()['product_name'] . ' - ' . $productStats->first()['variant_name'] : 'N/A';
        $topReturnedColor = $colorStats->first() ? $colorStats->first()['color'] : 'N/A';
        $topReturnedCustomer = $customerStats->first() ? $customerStats->first()['customer_name'] : 'N/A';
        
        // Periode laporan
        $startDate = $returns->min('created_at');
        $endDate = $returns->max('created_at');
        
        $pdf = Pdf::loadView('exports.laporan-retur', compact(
            'returns', 'totalReturns', 'totalCustomers', 'statusStats', 
            'monthlyStats', 'customerStats', 'driverStats', 'startDate', 'endDate',
            'productStats', 'colorStats', 'topReturnedProduct', 'topReturnedColor', 'topReturnedCustomer'
        ));
        
        return $pdf->download('laporan-barang-retur.pdf');
    }
}
