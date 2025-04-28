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

    public function finishTransaksi($id, Request $request)
    {
        $invoice = HInvoice::find($id);
        $invoice->status = 'sampai';
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil menyelesaikan transaksi');
    }
}
