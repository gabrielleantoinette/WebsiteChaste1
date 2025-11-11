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
use App\Support\ReportDateRange;

class OwnerController extends Controller
{
    public function viewAssignDriver()
    {
        $drivers = Employee::where('role', 'driver')->get();
        
        // Hanya tampilkan transaksi yang sudah disiapkan gudang (status 'dikemas' dan sudah ada gudang_id)
        $pengirimanNormal = HInvoice::where('status', 'dikemas')
            ->whereNotNull('gudang_id')
            ->whereNotIn('status', ['retur_diajukan', 'retur_diambil'])
            ->get();
            
        $pengirimanRetur = HInvoice::whereIn('status', ['retur_diajukan', 'retur_diambil'])->get();
        
        return view('admin.assign-driver.view', compact('pengirimanNormal', 'pengirimanRetur', 'drivers'));
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

            // Kirim notifikasi ke customer bahwa retur disetujui
            $notificationService->notifyReturnApproved($invoice->id, $invoice->customer_id, [
                'order_id' => $invoice->code,
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

            // Kirim notifikasi ke customer bahwa pesanan dikirim
            $notificationService->notifyOrderShipped($invoice->id, $invoice->customer_id, [
                'invoice_code' => $invoice->code,
                'customer_name' => $invoice->customer->name
            ]);
        }
        
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil memilih driver');
    }

    public function transactionsIndex(Request $request)
    {
        $filter = $request->input('filter', 'semua');
        $search = $request->input('search');

        // Filter waktu - menggunakan receive_date untuk data yang benar
        if ($filter !== 'semua') {
            $now = Carbon::now();
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
        }

        // Pendapatan (HInvoice) - menggunakan receive_date
        $pendapatanQuery = \App\Models\HInvoice::query();
        if ($filter !== 'semua') {
            $pendapatanQuery->whereBetween('receive_date', [$start, $end]);
        }
        if ($search) {
            $pendapatanQuery->where('code', 'like', "%$search%");
        }
        $pendapatan = $pendapatanQuery->get();

        // Pengeluaran (DebtPayment)
        $pengeluaranQuery = DebtPayment::query();
        if ($filter !== 'semua') {
            $pengeluaranQuery->whereBetween('payment_date', [$start, $end]);
        }
        if ($search) {
            $pengeluaranQuery->whereHas('purchaseOrder', function($q) use ($search) {
                $q->where('code', 'like', "%$search%");
            });
        }
        $pengeluaran = $pengeluaranQuery->get();

        // Hutang Piutang (PurchaseOrder)
        $hutangQuery = PurchaseOrder::query();
        if ($filter !== 'semua') {
            $hutangQuery->whereBetween('order_date', [$start, $end]);
        }
        if ($search) {
            $hutangQuery->where('code', 'like', "%$search%");
        }
        $hutang = $hutangQuery->get();

        // Payments data untuk view - menggunakan receive_date dengan pagination
        $paymentsQuery = \App\Models\PaymentModel::with(['hinvoice.customer']);
        if ($filter !== 'semua') {
            $paymentsQuery->whereHas('hinvoice', function($q) use ($start, $end) {
                $q->whereBetween('receive_date', [$start, $end]);
            });
        }
        if ($search) {
            $paymentsQuery->where(function($query) use ($search) {
                $query->whereHas('hinvoice', function($q) use ($search) {
                    $q->where('code', 'like', "%$search%");
                })->orWhereHas('hinvoice.customer', function($q) use ($search) {
                    $q->where('name', 'like', "%$search%");
                });
            });
        }
        // Stats (global totals, not limited by pagination)
        $paymentsStatsQuery = clone $paymentsQuery;
        $totalPayments = (clone $paymentsStatsQuery)->count();
        $paidPayments = (clone $paymentsStatsQuery)->where('is_paid', true)->count();
        $unpaidPayments = $totalPayments - $paidPayments;
        $totalPaymentsAmount = (clone $paymentsStatsQuery)->sum('amount');

        // Paginated list
        $payments = $paymentsQuery->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('admin.kelola-transaksi.view', compact(
            'pendapatan', 'pengeluaran', 'hutang', 'payments', 'filter', 'search',
            'totalPayments', 'paidPayments', 'unpaidPayments', 'totalPaymentsAmount'
        ));
    }



    // Method untuk download PDF laporan transaksi
    public function downloadLaporanTransaksi(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        // Data untuk PDF
        $pendapatan = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();
        
        $pengeluaran = DebtPayment::with(['purchaseOrder.supplier'])
            ->whereBetween('payment_date', [$start, $end])
            ->orderBy('payment_date')
            ->get();
        
        $hutangPiutang = PurchaseOrder::with(['supplier', 'items'])
            ->whereBetween('order_date', [$start, $end])
            ->orderBy('order_date')
            ->get();
        
        $hutangCustomer = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->whereHas('payments', function($q) {
                $q->where('method', 'hutang')->where('is_paid', 0);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $totalPendapatan = $pendapatan->sum('grand_total');
        $totalPengeluaran = $pengeluaran->sum('amount_paid');
        $totalHutang = $hutangPiutang->sum('total_amount');
        $totalHutangCustomer = $hutangCustomer->sum('grand_total');
        $laba = $totalPendapatan - $totalPengeluaran;

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-transaksi-owner', compact(
            'pendapatan', 'pengeluaran', 'hutangPiutang', 'hutangCustomer',
            'totalPendapatan', 'totalPengeluaran', 'totalHutang', 'totalHutangCustomer',
            'laba', 'periodeLabel', 'periodeStart', 'periodeEnd'
        ));

        return $pdf->download('laporan-transaksi-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }

    // Method untuk download laporan payment gateway
    public function downloadLaporanPaymentGateway(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        // Data payment gateway
        $transaksiBerhasil = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->where('status', 'lunas')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $transaksiPending = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->where('status', 'Menunggu Pembayaran')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $transaksiGagal = HInvoice::with(['customer', 'payments'])
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->where('status', '!=', 'lunas')
            ->where('status', '!=', 'Menunggu Pembayaran')
            ->whereHas('payments', function($q) {
                $q->whereIn('method', ['midtrans', 'transfer', 'cash']);
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $totalPenjualan = $transaksiBerhasil->sum('grand_total');
        $totalTransaksi = $transaksiBerhasil->count() + $transaksiPending->count() + $transaksiGagal->count();

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-payment-gateway', compact(
            'transaksiBerhasil', 'transaksiPending', 'transaksiGagal',
            'totalPenjualan', 'totalTransaksi', 'periodeLabel', 'periodeStart', 'periodeEnd'
        ));

        return $pdf->download('laporan-payment-gateway-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }

    // Method untuk download laporan negosiasi
    public function downloadLaporanNegosiasi(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        // Data negosiasi
        $negosiasiBerhasil = NegotiationTable::with(['customer', 'product'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'disetujui')
            ->orderBy('created_at')
            ->get();

        $negosiasiGagal = NegotiationTable::with(['customer', 'product'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'ditolak')
            ->orderBy('created_at')
            ->get();

        $negosiasiPending = NegotiationTable::with(['customer', 'product'])
            ->whereBetween('created_at', [$start, $end])
            ->where('status', 'pending')
            ->orderBy('created_at')
            ->get();

        $totalNegosiasi = $negosiasiBerhasil->count() + $negosiasiGagal->count() + $negosiasiPending->count();
        $persentaseBerhasil = $totalNegosiasi > 0 ? ($negosiasiBerhasil->count() / $totalNegosiasi) * 100 : 0;

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-negosiasi', compact(
            'negosiasiBerhasil', 'negosiasiGagal', 'negosiasiPending',
            'totalNegosiasi', 'persentaseBerhasil', 'periodeLabel', 'periodeStart', 'periodeEnd'
        ));

        return $pdf->download('laporan-negosiasi-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }

    public function downloadLaporanDriver(Request $request)
    {
        $periode = ReportDateRange::fromRequest($request, 'bulanan');
        $start = $periode['start']->copy();
        $end = $periode['end']->copy();

        $deliveryStatuses = ['dikirim', 'sampai', 'completed'];
        $returnStatuses = ['retur_diajukan', 'retur_diambil', 'retur_selesai'];

        $tasks = HInvoice::with(['customer', 'driver'])
            ->whereNotNull('driver_id')
            ->where(function ($query) use ($start, $end) {
                $query->whereBetween('receive_date', [$start, $end])
                    ->orWhere(function ($sub) use ($start, $end) {
                        $sub->whereNull('receive_date')
                            ->whereBetween('created_at', [$start, $end]);
                    });
            })
            ->orderByRaw('COALESCE(receive_date, created_at) ASC')
            ->get();

        $driverSummaries = $tasks->groupBy('driver_id')->map(function ($group) use ($deliveryStatuses, $returnStatuses) {
            $driver = $group->first()->driver;
            $deliveryTasks = $group->filter(fn($task) => in_array(strtolower($task->status), $deliveryStatuses));
            $returnTasks = $group->filter(fn($task) => in_array(strtolower($task->status), $returnStatuses));

            return [
                'driver' => $driver,
                'total_tasks' => $group->count(),
                'delivery_total' => $deliveryTasks->count(),
                'delivery_completed' => $deliveryTasks->filter(fn($task) => strtolower($task->status) === 'sampai' || strtolower($task->status) === 'completed')->count(),
                'return_total' => $returnTasks->count(),
                'return_completed' => $returnTasks->filter(fn($task) => strtolower($task->status) === 'retur_selesai')->count(),
            ];
        })->values();

        $totalTasks = $tasks->count();
        $totalDeliveryTasks = $tasks->filter(fn($task) => in_array(strtolower($task->status), $deliveryStatuses))->count();
        $totalReturnTasks = $tasks->filter(fn($task) => in_array(strtolower($task->status), $returnStatuses))->count();
        $totalCompletedTasks = $tasks->filter(fn($task) => in_array(strtolower($task->status), ['sampai', 'completed', 'retur_selesai']))->count();

        $periodeLabel = $periode['label'];
        $periodeStart = $start;
        $periodeEnd = $end;

        $pdf = Pdf::loadView('exports.laporan-driver-owner', [
            'tasks' => $tasks,
            'driverSummaries' => $driverSummaries,
            'totalTasks' => $totalTasks,
            'totalDeliveryTasks' => $totalDeliveryTasks,
            'totalReturnTasks' => $totalReturnTasks,
            'totalCompletedTasks' => $totalCompletedTasks,
            'periodeLabel' => $periodeLabel,
            'periodeStart' => $periodeStart,
            'periodeEnd' => $periodeEnd,
        ]);

        return $pdf->download('laporan-driver-' . $periode['range'] . '-' . $end->format('Y-m-d') . '.pdf');
    }
}
