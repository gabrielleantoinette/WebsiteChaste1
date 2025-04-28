<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HInvoice;
use Illuminate\Http\Request;

class OwnerController extends Controller
{
    public function viewAssignDriver()
    {
        $drivers = Employee::where('role', 'driver')->get();
        $invoices = HInvoice::all();
        return view('admin.assign-driver.view', compact('invoices', 'drivers'));
    }

    public function assignDriver($id, Request $request)
    {
        $invoice = HInvoice::find($id);
        $invoice->driver_id = $request->driver_id;
        $invoice->status = 'dikirim';
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil memilih driver');
    }
}
