<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use App\Services\NotificationService;
use Illuminate\Http\Request;

class KeuanganController extends Controller
{
    public function view(Request $request)
    {
        $query = \DB::table('hinvoice')
            ->join('customers', 'hinvoice.customer_id', '=', 'customers.id')
            ->select('hinvoice.*', 'customers.name as customer_name')
            ->orderByDesc('hinvoice.created_at');

        // Filter waktu
        switch ($request->filter) {
            case 'hari':
                $query->whereDate('hinvoice.created_at', today());
                break;
            case 'minggu':
                $query->whereBetween('hinvoice.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                break;
            case 'bulan':
                $query->whereMonth('hinvoice.created_at', now()->month)
                    ->whereYear('hinvoice.created_at', now()->year);
                break;
            case 'tahun':
                $query->whereYear('hinvoice.created_at', now()->year);
                break;
        }

        // Search keyword (by customer name or code)
        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customers.name', 'like', '%' . $request->search . '%')
                ->orWhere('hinvoice.code', 'like', '%' . $request->search . '%');
            });
        }

        $transaksi = $query->paginate(10)->withQueryString();

        return view('admin.keuangan.view', compact('transaksi'));
    }

    public function detail($id)
    {
        $invoice = \App\Models\HInvoice::with('customer')->findOrFail($id);
        return view('admin.keuangan.detail', compact('invoice'));
    }

    public function konfirmasi($id)
    {
        $invoice = \App\Models\HInvoice::findOrFail($id);
        if ($invoice->status === 'Menunggu Konfirmasi Pembayaran') {
            $invoice->status = 'Dikemas';
            $invoice->save();
            
            // Kirim notifikasi pembayaran dikonfirmasi ke admin
            $notificationService = app(NotificationService::class);
            $notificationService->notifyPayment($invoice->id, [
                'amount' => $invoice->grand_total,
                'customer_name' => $invoice->customer->name,
                'invoice_code' => $invoice->code
            ]);

            // Kirim notifikasi ke customer bahwa pembayaran dikonfirmasi
            $notificationService->sendToCustomer(
                'payment_confirmed',
                'Pembayaran Dikonfirmasi',
                "Pembayaran untuk pesanan {$invoice->code} telah dikonfirmasi. Pesanan Anda sedang diproses.",
                $invoice->customer_id,
                [
                    'data_type' => 'order',
                    'data_id' => $invoice->id,
                    'action_url' => "/orders/{$invoice->id}",
                    'priority' => 'high'
                ]
            );


            
            // Kirim notifikasi ke owner tentang action keuangan
            $notificationService->notifyFinanceAction([
                'message' => "Keuangan telah mengkonfirmasi pembayaran untuk pesanan {$invoice->code} sebesar Rp " . number_format($invoice->grand_total),
                'action_id' => $invoice->id,
                'action_url' => "/admin/keuangan/detail/{$invoice->id}",
                'priority' => 'high'
            ]);
            
            return redirect()->back()->with('success', 'Pembayaran dikonfirmasi & status diubah menjadi Dikemas!');
        }
        return redirect()->back()->with('success', 'Status tidak valid atau sudah diproses.');
    }

    public function create()
    {
        return view('admin.keuangan.create');
    }

    public function store(Request $request)
    {
        return redirect()->route('keuangan.view')->with('success', 'Transaksi berhasil disimpan!');
    }

    public function exportPDF(Request $request)
    {
        $query = \DB::table('hinvoice')
            ->join('customers', 'hinvoice.customer_id', '=', 'customers.id')
            ->select('hinvoice.*', 'customers.name as customer_name')
            ->orderByDesc('hinvoice.created_at');

        if ($request->filter) {
            switch ($request->filter) {
                case 'hari':
                    $query->whereDate('hinvoice.created_at', today());
                    break;
                case 'minggu':
                    $query->whereBetween('hinvoice.created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                    break;
                case 'bulan':
                    $query->whereMonth('hinvoice.created_at', now()->month)
                        ->whereYear('hinvoice.created_at', now()->year);
                    break;
                case 'tahun':
                    $query->whereYear('hinvoice.created_at', now()->year);
                    break;
            }
        }

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('customers.name', 'like', '%' . $request->search . '%')
                ->orWhere('hinvoice.code', 'like', '%' . $request->search . '%');
            });
        }

        $transaksi = $query->get();

        $pdf = Pdf::loadView('exports.laporantransaksipembelikeuangan', compact('transaksi'));

        return $pdf->download('laporan-keuangan-transaksi-pembeli.pdf');
    }

    public function dashboardKeuangan()
    {
        $today = now()->toDateString();
        $totalTransaksi = \App\Models\HInvoice::whereDate('created_at', $today)->count();
        $totalPemasukan = \App\Models\HInvoice::whereDate('created_at', $today)->sum('grand_total');
        $totalPengeluaran = \App\Models\DebtPayment::whereDate('payment_date', $today)->sum('amount_paid');

        $reminderDate = now()->addDays(3)->toDateString();
        $hutangJatuhTempo = \App\Models\HInvoice::with('customer')
            ->where('status', '!=', 'lunas')
            ->whereBetween('due_date', [now()->toDateString(), $reminderDate])
            ->get();

        return view('admin.dashboardkeuangan', compact('totalTransaksi', 'totalPemasukan', 'totalPengeluaran', 'hutangJatuhTempo'));
    }
}
