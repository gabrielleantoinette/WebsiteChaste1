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
        $user = session('user');
        $role = strtolower(is_array($user) ? ($user['role'] ?? '') : ($user->role ?? ''));

        // Data untuk dashboard umum (owner)
        $employeeCount = \App\Models\Employee::count();
        $customerCount = \App\Models\Customer::count();
        $productCount  = \App\Models\Product::count();
        $totalPenjualan = \App\Models\HInvoice::whereDate('created_at', now()->toDateString())->sum('grand_total');
        $recentInvoices = \App\Models\HInvoice::with('customer')
                        ->whereDate('created_at', now()->toDateString())
                        ->orderBy('created_at', 'desc')
                        ->get();
        $days = collect();
        $sales = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $days->push($date->format('d M'));
            $sales->push(
                \App\Models\HInvoice::whereDate('created_at', $date)->sum('grand_total')
            );
        }
        $monthlyLabels = collect();
        $monthlySales = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $monthlyLabels->push($date->format('d M'));
            $monthlySales->push(
                \App\Models\HInvoice::whereDate('created_at', $date)->sum('grand_total')
            );
        }
        $yearLabels = collect();
        $yearSales = collect();
        for ($i = 11; $i >= 0; $i--) {
            $month = now()->subMonths($i);
            $yearLabels->push($month->format('M Y'));
            $yearSales->push(
                \App\Models\HInvoice::whereYear('created_at', $month->year)
                        ->whereMonth('created_at', $month->month)
                        ->sum('grand_total')
            );
        }

        if ($role === 'admin') {
            $pendingOrders = \App\Models\HInvoice::with('customer')
                ->whereIn('status', ['Menunggu Pembayaran', 'Diproses'])
                ->get();
            $lowStocks = \App\Models\ProductVariant::where('stock', '<', 10)->get();
            $returCount = \App\Models\Returns::where('status', 'diajukan')->count();
            return view('admin.dashboardadmin', compact('pendingOrders', 'lowStocks', 'returCount'));
        } elseif ($role === 'keuangan') {
            return redirect()->route('keuangan.dashboard');
        } else { // owner atau role lain
            return view('admin.dashboard', compact(
                'employeeCount', 'customerCount', 'productCount', 'totalPenjualan',
                'recentInvoices', 'days', 'sales',
                'monthlyLabels', 'monthlySales', 'yearLabels', 'yearSales'
            ));
        }
    }
}
