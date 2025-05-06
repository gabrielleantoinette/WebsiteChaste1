<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\Customer;
use App\Models\Product;
use App\Models\DInvoice;
use App\Models\HInvoice;

class DashboardController extends Controller
{
    public function index()
    {
        $employeeCount = Employee::count();
        $customerCount = Customer::count();
        $productCount  = Product::count();
        $totalPenjualan = HInvoice::sum('grand_total');
        
        $recentInvoices = HInvoice::with('customer')
                            ->orderBy('created_at', 'desc')
                            ->take(5)
                            ->get();

        return view('admin.dashboard', compact(
            'employeeCount',
            'customerCount',
            'productCount',
            'totalPenjualan',
            'recentInvoices'
        ));
    }
}
