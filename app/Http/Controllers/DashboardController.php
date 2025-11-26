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
                        ->orderBy('created_at', 'desc')
                        ->orderBy('id', 'desc')
                        ->limit(10)
                        ->get();

        // Generate chart data berdasarkan data aktual
        $dailyLabels = collect();
        $dailySales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $dailyLabels->push($date->translatedFormat('d M'));
            $total = HInvoice::where(function ($query) use ($date) {
                $query->whereDate('receive_date', $date->toDateString())
                    ->orWhere(function ($sub) use ($date) {
                        $sub->whereNull('receive_date')
                            ->whereDate('created_at', $date->toDateString());
                    });
            })->sum('grand_total');
            $dailySales->push((float) $total);
        }

        $monthlyLabels = collect();
        $monthlySales = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $monthlyLabels->push($date->translatedFormat('d M'));
            $total = HInvoice::where(function ($query) use ($date) {
                $query->whereDate('receive_date', $date->toDateString())
                    ->orWhere(function ($sub) use ($date) {
                        $sub->whereNull('receive_date')
                            ->whereDate('created_at', $date->toDateString());
                    });
            })->sum('grand_total');
            $monthlySales->push((float) $total);
        }

        $yearLabels = collect();
        $yearSales = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $yearLabels->push($month->translatedFormat('M Y'));
            $total = HInvoice::where(function ($query) use ($month) {
                $query->whereYear('receive_date', $month->year)
                    ->whereMonth('receive_date', $month->month)
                    ->orWhere(function ($sub) use ($month) {
                        $sub->whereNull('receive_date')
                            ->whereYear('created_at', $month->year)
                            ->whereMonth('created_at', $month->month);
                    });
            })->sum('grand_total');
            $yearSales->push((float) $total);
        }

        $yearlyLabels = collect();
        $yearlySales = collect();
        for ($i = 4; $i >= 0; $i--) {
            $year = Carbon::now()->subYears($i)->year;
            $yearlyLabels->push((string) $year);
            $total = HInvoice::where(function ($query) use ($year) {
                $query->whereYear('receive_date', $year)
                    ->orWhere(function ($sub) use ($year) {
                        $sub->whereNull('receive_date')
                            ->whereYear('created_at', $year);
                    });
            })->sum('grand_total');
            $yearlySales->push((float) $total);
        }

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