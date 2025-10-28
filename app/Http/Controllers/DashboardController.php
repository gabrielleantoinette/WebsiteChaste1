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

        // Generate chart data HANYA dari data real (tanpa fallback)
        // 7 Hari Terakhir
        $dailyLabels = collect();
        $dailySales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dailyLabels->push($date->format('d M'));
            $salesAmount = HInvoice::whereDate('receive_date', $date)->sum('grand_total');
            $dailySales->push($salesAmount);
        }

        // 30 Hari Terakhir
        $monthlyLabels = collect();
        $monthlySales = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $monthlyLabels->push($date->format('d M'));
            $salesAmount = HInvoice::whereDate('receive_date', $date)->sum('grand_total');
            $monthlySales->push($salesAmount);
        }

        // 12 Bulan Terakhir
        $yearLabels = collect();
        $yearSales = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $yearLabels->push($month->format('M Y'));
            $salesAmount = HInvoice::whereYear('receive_date', $month->year)
                    ->whereMonth('receive_date', $month->month)
                    ->sum('grand_total');
            $yearSales->push($salesAmount);
        }

        // 5 Tahun Terakhir - PERBAIKAN: Mulai dari tahun 2020
        $yearlyLabels = collect();
        $yearlySales = collect();
        for ($i = 4; $i >= 0; $i--) {
            $year = now()->subYears($i);
            $yearlyLabels->push($year->format('Y'));
            $salesAmount = HInvoice::whereYear('receive_date', $year->year)->sum('grand_total');
            $yearlySales->push($salesAmount);
        }

        if ($role === 'admin') {
            $pendingOrders = HInvoice::with('customer')
                ->whereIn('status', ['Menunggu Pembayaran', 'Diproses'])
                ->limit(20)
                ->get();
            $lowStocks = ProductVariant::where('stock', '<', 10)->limit(20)->get();
            $returCount = Returns::where('status', 'diajukan')->count();
            return view('admin.dashboardadmin', compact('pendingOrders', 'lowStocks', 'returCount'));
        } elseif ($role === 'keuangan') {
            return redirect()->route('keuangan.dashboard');
        } elseif ($role === 'driver') {
            return redirect('/admin/dashboard-driver');
        } else { // owner atau role lain
            return view('admin.dashboard', compact(
                'employeeCount', 'customerCount', 'productCount', 'totalPenjualan',
                'recentInvoices', 'dailyLabels', 'dailySales',
                'monthlyLabels', 'monthlySales', 'yearLabels', 'yearSales',
                'yearlyLabels', 'yearlySales'
            ));
        }
    }
}