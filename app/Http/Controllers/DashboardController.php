<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\Product;
use App\Models\HInvoice;
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
                            ->orderBy('created_at', 'desc')
                            ->take(5)
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

        return view('admin.dashboard', compact(
            'employeeCount',
            'customerCount',
            'productCount',
            'totalPenjualan',
            'recentInvoices',
            'days',
            'sales'
        ));
    }
}
