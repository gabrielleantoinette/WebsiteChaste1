<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\Product;
use App\Models\HInvoice;
use App\Models\ProductVariant;
use App\Models\Returns;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $user = session('user');
        $role = strtolower(is_array($user) ? ($user['role'] ?? '') : ($user->role ?? ''));

        // Data dasar dengan query yang lebih efisien
        $employeeCount = Employee::count();
        $customerCount = Customer::count();
        $productCount = Product::count();
        
        // Query yang lebih efisien untuk penjualan hari ini
        $today = now()->toDateString();
        $totalPenjualan = HInvoice::whereDate('receive_date', $today)->sum('grand_total');
        
        // Recent invoices dengan limit untuk performa
        $recentInvoices = HInvoice::with('customer')
                        ->whereDate('receive_date', $today)
                        ->orderBy('receive_date', 'desc')
                        ->limit(10)
                        ->get();

        // Generate chart data berdasarkan data aktual
        $dailyData = HInvoice::selectRaw("DATE(COALESCE(receive_date, created_at)) as tanggal, SUM(grand_total) as total")
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->limit(7)
            ->get()
            ->reverse()
            ->values();

        $dailyLabels = $dailyData->map(function ($row) {
            return Carbon::parse($row->tanggal)->translatedFormat('d M');
        });
        $dailySales = $dailyData->map(function ($row) {
            return (float) $row->total;
        });

        $thirtyDayData = HInvoice::selectRaw("DATE(COALESCE(receive_date, created_at)) as tanggal, SUM(grand_total) as total")
            ->groupBy('tanggal')
            ->orderBy('tanggal', 'desc')
            ->limit(30)
            ->get()
            ->reverse()
            ->values();

        $monthlyLabels = $thirtyDayData->map(function ($row) {
            return Carbon::parse($row->tanggal)->translatedFormat('d M');
        });
        $monthlySales = $thirtyDayData->map(function ($row) {
            return (float) $row->total;
        });

        $monthlyAggregate = HInvoice::selectRaw("DATE_FORMAT(COALESCE(receive_date, created_at), '%Y-%m') as periode, SUM(grand_total) as total")
            ->groupBy('periode')
            ->orderBy('periode', 'desc')
            ->limit(12)
            ->get()
            ->reverse()
            ->values();

        $yearLabels = $monthlyAggregate->map(function ($row) {
            return Carbon::createFromFormat('Y-m', $row->periode)->translatedFormat('M Y');
        });
        $yearSales = $monthlyAggregate->map(function ($row) {
            return (float) $row->total;
        });

        $yearlyData = HInvoice::selectRaw("DATE_FORMAT(COALESCE(receive_date, created_at), '%Y') as tahun, SUM(grand_total) as total")
            ->groupBy('tahun')
            ->orderBy('tahun', 'desc')
            ->limit(5)
            ->get()
            ->reverse()
            ->values();

        $yearlyLabels = $yearlyData->pluck('tahun');
        $yearlySales = $yearlyData->map(function ($row) {
            return (float) $row->total;
        });

        if ($role === 'admin') {
            $ordersChartLabels = collect();
            $ordersChartData = collect();

            for ($i = 6; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $ordersChartLabels->push($date->translatedFormat('D'));

                $count = HInvoice::where(function ($query) use ($date) {
                    $query->whereDate('receive_date', $date->toDateString())
                        ->orWhere(function ($sub) use ($date) {
                            $sub->whereNull('receive_date')
                                ->whereDate('created_at', $date->toDateString());
                        });
                })->count();

                $ordersChartData->push($count);
            }

            $rawStatusCounts = HInvoice::select('status', DB::raw('COUNT(*) as total'))
                ->groupBy('status')
                ->pluck('total', 'status');

            $statusBuckets = [
                'Menunggu Pembayaran' => [
                    'menunggu pembayaran', 'Menunggu Pembayaran', 'pending', 'unpaid'
                ],
                'Diproses' => [
                    'diproses', 'Diproses', 'processing', 'dikemas'
                ],
                'Selesai' => [
                    'selesai', 'Selesai', 'completed', 'sampai', 'lunas'
                ],
                'Dibatalkan' => [
                    'dibatalkan', 'Dibatalkan', 'cancelled'
                ],
            ];

            $statusChartLabels = array_keys($statusBuckets);
            $statusChartData = array_map(function ($aliases) use ($rawStatusCounts) {
                return collect($aliases)->sum(function ($alias) use ($rawStatusCounts) {
                    return $rawStatusCounts[$alias] ?? 0;
                });
            }, $statusBuckets);

            $pendingOrders = HInvoice::with('customer')
                ->whereIn('status', ['Menunggu Pembayaran', 'Diproses'])
                ->limit(20)
                ->get();
            $lowStocks = ProductVariant::where('stock', '<', 10)->limit(20)->get();
            $returCount = Returns::where('status', 'diajukan')->count();
            return view('admin.dashboardadmin', compact(
                'pendingOrders',
                'lowStocks',
                'returCount',
                'ordersChartLabels',
                'ordersChartData',
                'statusChartLabels',
                'statusChartData'
            ));
        } elseif ($role === 'keuangan') {
            return redirect()->route('keuangan.dashboard');
        } elseif ($role === 'driver') {
            return redirect('/admin/dashboard-driver');
        } else { // owner atau role lain
            return view('admin.dashboard', compact(
                'employeeCount', 'customerCount', 'productCount', 'totalPenjualan',
                'recentInvoices', 'dailyLabels', 'dailySales',
                'monthlyLabels', 'monthlySales', 'yearLabels', 'yearSales',
                'yearlyLabels', 'yearlySales', 'role'
            ));
        }
    }
}