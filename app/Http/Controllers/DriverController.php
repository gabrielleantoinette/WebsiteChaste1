<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DriverController extends Controller
{
    public function viewTransaksiDriver()
    {
        $user = Session::get('user');
        $drivers = Employee::where('role', 'driver')->get();
        $invoices = HInvoice::where('status', 'dikirim')->orWhere('status', 'sampai')->where('driver_id', $user->id)->get();
        return view('admin.driver-transaksi.view', compact('invoices', 'drivers'));
    }

    public function detailTransaksiDriver($id)
    {
        $invoice = HInvoice::with(['customer', 'details.product', 'details.variant'])->findOrFail($id);
        return view('admin.driver-transaksi.detail', compact('invoice'));
    }

    public function finishTransaksi($id, Request $request)
    {
        $invoice = HInvoice::find($id);
        $invoice->status = 'sampai';
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil menyelesaikan transaksi');
    }

    public function dashboardDriver()
    {
        $user = Session::get('user');
        // Pesanan/retur siap dikirim atau diambil (status: dikirim, retur: status = 'siap diambil')
        $ordersReady = \App\Models\HInvoice::where('driver_id', $user->id)
            ->where('status', 'dikirim')
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
        $returnsReady = \App\Models\Returns::where('status', 'siap diambil')->with(['invoice.customer'])->get();

        // Pengiriman dalam proses (status: dikirim)
        $ordersInProcess = \App\Models\HInvoice::where('driver_id', $user->id)
            ->where('status', 'dikirim')
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
        // History pengiriman selesai (status: sampai)
        $ordersHistory = \App\Models\HInvoice::where('driver_id', $user->id)
            ->where('status', 'sampai')
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();

        return view('admin.dashboarddriver', compact('ordersReady', 'returnsReady', 'ordersInProcess', 'ordersHistory'));
    }
}
