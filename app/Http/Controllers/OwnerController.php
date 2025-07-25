<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HInvoice;
use Illuminate\Http\Request;
use App\Models\PaymentModel;
use App\Models\DebtPayment;
use Carbon\Carbon;
use App\Models\PurchaseOrder;
use App\Models\Supplier;

class OwnerController extends Controller
{
    public function viewAssignDriver()
    {
        $drivers = Employee::where('role', 'driver')->get();
        $pengirimanNormal = HInvoice::whereNotIn('status', ['retur_diajukan', 'retur_diambil'])->get();
        $pengambilanRetur = HInvoice::whereIn('status', ['retur_diajukan', 'retur_diambil'])->get();
        return view('admin.assign-driver.view', compact('pengirimanNormal', 'pengambilanRetur', 'drivers'));
    }

    public function assignDriver($id, Request $request)
    {
        $invoice = HInvoice::find($id);
        $invoice->driver_id = $request->driver_id;
        
        // Beda status untuk pengiriman normal vs retur
        if ($invoice->status === 'retur_diajukan') {
            $invoice->status = 'retur_diambil'; // Status khusus untuk retur yang sudah di-assign driver
        } else {
            $invoice->status = 'dikirim'; // Status untuk pengiriman normal
        }
        
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil memilih driver');
    }

    public function transactionsIndex(Request $request)
    {
        $filter = $request->input('filter', 'bulan');
        $search = $request->input('search');
        $now = Carbon::now();

        // Filter waktu
        switch ($filter) {
            case 'hari':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                break;
            case 'minggu':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                break;
            case 'tahun':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                break;
            case 'bulan':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                break;
        }

        // Pendapatan (HInvoice)
        $pendapatan = \App\Models\HInvoice::whereBetween('created_at', [$start, $end]);
        if ($search) {
            $pendapatan = $pendapatan->where('code', 'like', "%$search%");
        }
        $pendapatan = $pendapatan->get();

        // Pengeluaran (DebtPayment)
        $pengeluaran = DebtPayment::whereBetween('payment_date', [$start, $end]);
        if ($search) {
            $pengeluaran = $pengeluaran->whereHas('purchaseOrder', function($q) use ($search) {
                $q->where('code', 'like', "%$search%");
            });
        }
        $pengeluaran = $pengeluaran->get();

        // Hutang Piutang (PurchaseOrder)
        $hutang = PurchaseOrder::whereBetween('order_date', [$start, $end]);
        if ($search) {
            $hutang = $hutang->where('code', 'like', "%$search%");
        }
        $hutang = $hutang->get();

        return view('admin.kelola-transaksi.view', compact('pendapatan', 'pengeluaran', 'hutang', 'filter', 'search'));
    }
}
