<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
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
        return view('admin.keuangan.detail', compact('id'));
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
}
