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
use App\Models\NegotiationTable;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\NotificationService;

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
        $invoice = HInvoice::with('customer')->find($id);
        $invoice->driver_id = $request->driver_id;
        
        // Beda status untuk pengiriman normal vs retur
        if ($invoice->status === 'retur_diajukan') {
            $invoice->status = 'retur_diambil'; // Status khusus untuk retur yang sudah di-assign driver
            
            // Kirim notifikasi ke driver untuk retur
            $notificationService = app(NotificationService::class);
            $notificationService->notifyReturnReadyForPickup([
                'id' => $invoice->id,
                'customer_name' => $invoice->customer->name
            ]);
        } else {
            $invoice->status = 'dikirim'; // Status untuk pengiriman normal
            
            // Kirim notifikasi ke driver untuk pengiriman
            $notificationService = app(NotificationService::class);
            $notificationService->notifyOrderReadyForDelivery([
                'id' => $invoice->id,
                'code' => $invoice->code,
                'customer_name' => $invoice->customer->name
            ]);
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



    // Method untuk download PDF laporan transaksi
    public function downloadLaporanTransaksi(Request $request)
    {
        $filter = $request->input('filter', 'bulan');
        $now = Carbon::now();

        // Filter waktu
        switch ($filter) {
            case 'hari':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $periode = 'Hari Ini (' . $start->format('d M Y') . ')';
                break;
            case 'minggu':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $periode = 'Minggu Ini (' . $start->format('d M Y') . ' - ' . $end->format('d M Y') . ')';
                break;
            case 'tahun':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $periode = 'Tahun ' . $now->year;
                break;
            case 'bulan':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $periode = 'Bulan ' . $now->format('F Y');
                break;
        }

        // Data untuk PDF
        $pendapatan = HInvoice::whereBetween('created_at', [$start, $end])
            ->with(['customer', 'payments'])
            ->get();
        
        $pengeluaran = DebtPayment::whereBetween('payment_date', [$start, $end])
            ->with(['purchaseOrder.supplier'])
            ->get();
        
        $hutangPiutang = PurchaseOrder::whereBetween('order_date', [$start, $end])
            ->with(['supplier', 'items'])
            ->get();
        
        $hutangCustomer = HInvoice::whereBetween('created_at', [$start, $end])
            ->whereHas('payments', function($q) {
                $q->where('method', 'hutang')->where('is_paid', 0);
            })
            ->with(['customer', 'payments'])
            ->get();

        $totalPendapatan = $pendapatan->sum('grand_total');
        $totalPengeluaran = $pengeluaran->sum('amount_paid');
        $totalHutang = $hutangPiutang->sum('total_amount');
        $totalHutangCustomer = $hutangCustomer->sum('grand_total');
        $laba = $totalPendapatan - $totalPengeluaran;

        $pdf = Pdf::loadView('exports.laporan-transaksi-owner', compact(
            'pendapatan', 'pengeluaran', 'hutangPiutang', 'hutangCustomer',
            'totalPendapatan', 'totalPengeluaran', 'totalHutang', 'totalHutangCustomer',
            'laba', 'periode'
        ));

        return $pdf->download('laporan-transaksi-' . $filter . '-' . $now->format('Y-m-d') . '.pdf');
    }

    // Method untuk download laporan payment gateway
    public function downloadLaporanPaymentGateway(Request $request)
    {
        $filter = $request->input('filter', 'bulan');
        $now = Carbon::now();

        // Filter waktu
        switch ($filter) {
            case 'hari':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $periode = 'Hari Ini (' . $start->format('d M Y') . ')';
                break;
            case 'minggu':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $periode = 'Minggu Ini (' . $start->format('d M Y') . ' - ' . $end->format('d M Y') . ')';
                break;
            case 'tahun':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $periode = 'Tahun ' . $now->year;
                break;
            case 'bulan':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $periode = 'Bulan ' . $now->format('F Y');
                break;
        }

        // Data payment gateway
        $transaksiBerhasil = HInvoice::whereBetween('created_at', [$start, $end])
            ->where('status', 'lunas')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->with(['customer', 'payments'])
            ->get();

        $transaksiPending = HInvoice::whereBetween('created_at', [$start, $end])
            ->where('status', 'Menunggu Pembayaran')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->with(['customer', 'payments'])
            ->get();

        $transaksiGagal = HInvoice::whereBetween('created_at', [$start, $end])
            ->where('status', '!=', 'lunas')
            ->where('status', '!=', 'Menunggu Pembayaran')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->with(['customer', 'payments'])
            ->get();

        $totalPenjualan = $transaksiBerhasil->sum('grand_total');
        $totalTransaksi = $transaksiBerhasil->count() + $transaksiPending->count() + $transaksiGagal->count();

        $pdf = Pdf::loadView('exports.laporan-payment-gateway', compact(
            'transaksiBerhasil', 'transaksiPending', 'transaksiGagal',
            'totalPenjualan', 'totalTransaksi', 'periode'
        ));

        return $pdf->download('laporan-payment-gateway-' . $filter . '-' . $now->format('Y-m-d') . '.pdf');
    }

    // Method untuk download laporan negosiasi
    public function downloadLaporanNegosiasi(Request $request)
    {
        $filter = $request->input('filter', 'bulan');
        $now = Carbon::now();

        // Filter waktu
        switch ($filter) {
            case 'hari':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $periode = 'Hari Ini (' . $start->format('d M Y') . ')';
                break;
            case 'minggu':
                $start = $now->copy()->startOfWeek();
                $end = $now->copy()->endOfWeek();
                $periode = 'Minggu Ini (' . $start->format('d M Y') . ' - ' . $end->format('d M Y') . ')';
                break;
            case 'tahun':
                $start = $now->copy()->startOfYear();
                $end = $now->copy()->endOfYear();
                $periode = 'Tahun ' . $now->year;
                break;
            case 'bulan':
            default:
                $start = $now->copy()->startOfMonth();
                $end = $now->copy()->endOfMonth();
                $periode = 'Bulan ' . $now->format('F Y');
                break;
        }

        // Data negosiasi
        $negosiasiBerhasil = NegotiationTable::whereBetween('created_at', [$start, $end])
            ->where('status', 'disetujui')
            ->with(['customer', 'product'])
            ->get();

        $negosiasiGagal = NegotiationTable::whereBetween('created_at', [$start, $end])
            ->where('status', 'ditolak')
            ->with(['customer', 'product'])
            ->get();

        $negosiasiPending = NegotiationTable::whereBetween('created_at', [$start, $end])
            ->where('status', 'pending')
            ->with(['customer', 'product'])
            ->get();

        $totalNegosiasi = $negosiasiBerhasil->count() + $negosiasiGagal->count() + $negosiasiPending->count();
        $persentaseBerhasil = $totalNegosiasi > 0 ? ($negosiasiBerhasil->count() / $totalNegosiasi) * 100 : 0;

        $pdf = Pdf::loadView('exports.laporan-negosiasi', compact(
            'negosiasiBerhasil', 'negosiasiGagal', 'negosiasiPending',
            'totalNegosiasi', 'persentaseBerhasil', 'periode'
        ));

        return $pdf->download('laporan-negosiasi-' . $filter . '-' . $now->format('Y-m-d') . '.pdf');
    }
}
