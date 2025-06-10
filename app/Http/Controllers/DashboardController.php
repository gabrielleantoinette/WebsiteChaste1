<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\Product;
use App\Models\HInvoice;
use App\Models\ProductVariant;
use App\Models\Returns;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $customerCount = Customer::count();
        $productCount  = Product::count();
        $totalPenjualan = HInvoice::whereDate('created_at', Carbon::today())->sum('grand_total');

        $recentInvoices = HInvoice::with('customer')
                        ->whereDate('created_at', Carbon::today())
                        ->orderBy('created_at', 'desc')
                        ->get();

        // Data untuk grafik 7 hari terakhir
        $days = collect();
        $sales = collect();

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days->push($date->format('d M'));
            $sales->push(
                HInvoice::whereDate('created_at', $date)->sum('grand_total')
            );
        }

        // Penjualan 30 hari terakhir (harian)
        $monthlyLabels = collect();
        $monthlySales = collect();

        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $monthlyLabels->push($date->format('d M'));
            $monthlySales->push(
                HInvoice::whereDate('created_at', $date)->sum('grand_total')
            );
        }

        // Penjualan 12 bulan terakhir (bulanan)
        $yearLabels = collect();
        $yearSales = collect();

        for ($i = 11; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $yearLabels->push($month->format('M Y'));
            $yearSales->push(
                HInvoice::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('grand_total')
            );
        }

        $user = session('user');
        $role = strtolower($user['role']);

        if ($role === 'admin') {
            $pendingOrders = HInvoice::with('customer')
            ->whereIn('status', ['Menunggu Pembayaran', 'Diproses'])
            ->get();
            $lowStocks = ProductVariant::where('stock', '<', 10)->get();
            $returCount = Returns::where('status', 'diajukan')->count(); 

            return view('admin.dashboardadmin', compact('pendingOrders', 'lowStocks', 'returCount'));
        }

        return view('admin.dashboard', compact(
            'employeeCount', 'customerCount', 'productCount', 'totalPenjualan',
            'recentInvoices', 'days', 'sales',
            'monthlyLabels', 'monthlySales', 'yearLabels', 'yearSales'
        ));           
    }
}
