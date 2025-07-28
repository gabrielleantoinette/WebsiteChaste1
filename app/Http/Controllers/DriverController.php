<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HInvoice;
use App\Models\Returns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DriverController extends Controller
{
    public function viewTransaksiDriver()
    {
        $user = Session::get('user');
        $drivers = Employee::where('role', 'driver')->get();
        
        // Ambil semua invoice yang di-assign ke driver ini (pengiriman normal)
        $normalDeliveries = HInvoice::where('driver_id', $user->id)
            ->whereNotIn('status', ['retur_diajukan', 'retur_diambil', 'retur_selesai'])
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        // Ambil semua retur yang di-assign ke driver ini
        $returns = HInvoice::where('driver_id', $user->id)
            ->whereIn('status', ['retur_diajukan', 'retur_diambil', 'retur_selesai'])
            ->with(['customer', 'returns'])
            ->get();
            
        // Gabungkan dan format data untuk ditampilkan
        $allTasks = collect();
        
        // Format pengiriman normal
        foreach ($normalDeliveries as $delivery) {
            $allTasks->push([
                'id' => $delivery->id,
                'code' => $delivery->code,
                'customer_name' => $delivery->customer->name ?? '-',
                'driver_name' => $delivery->driver->name ?? '-',
                'status' => $delivery->status,
                'address' => $delivery->address,
                'receive_date' => $delivery->receive_date,
                'type' => 'delivery',
                'details' => $delivery->details,
                'invoice' => $delivery
            ]);
        }
        
        // Format retur
        foreach ($returns as $retur) {
            $allTasks->push([
                'id' => $retur->id,
                'code' => $retur->code,
                'customer_name' => $retur->customer->name ?? '-',
                'driver_name' => $retur->driver->name ?? '-',
                'status' => $retur->status,
                'address' => $retur->address,
                'receive_date' => $retur->receive_date,
                'type' => 'return',
                'returns' => $retur->returns,
                'invoice' => $retur
            ]);
        }
        
        // Urutkan berdasarkan tanggal terbaru
        $allTasks = $allTasks->sortByDesc('receive_date');
        
        return view('admin.driver-transaksi.view', compact('allTasks', 'drivers'));
    }

    public function detailTransaksiDriver($id)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $invoice = HInvoice::with(['customer', 'details.product', 'details.variant', 'driver', 'gudang'])->findOrFail($id);
            
            // Pastikan ini adalah invoice yang di-assign ke driver ini
            if ($invoice->driver_id != $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat detail transaksi ini.');
            }
            
            return view('admin.driver-transaksi.detail', compact('invoice'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Transaksi tidak ditemukan atau terjadi kesalahan.');
        }
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
        
        // Pesanan siap dikirim (status: dikirim)
        $ordersReady = HInvoice::where('driver_id', $user->id)
            ->where('status', 'dikirim')
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        // Retur siap diambil (status: retur_diambil) yang sudah di-assign ke driver ini
        $returnsReady = HInvoice::where('driver_id', $user->id)
            ->where('status', 'retur_diambil')
            ->with(['customer', 'returns'])
            ->get();

        // Pengiriman dalam proses (status: dikirim)
        $ordersInProcess = HInvoice::where('driver_id', $user->id)
            ->where('status', 'dikirim')
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        // History pengiriman selesai (status: sampai)
        $ordersHistory = HInvoice::where('driver_id', $user->id)
            ->where('status', 'sampai')
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        // History pengambilan retur selesai (status: retur_selesai)
        $returnsHistory = HInvoice::where('driver_id', $user->id)
            ->where('status', 'retur_selesai')
            ->with(['customer', 'returns'])
            ->get();

        return view('admin.dashboarddriver', compact('ordersReady', 'returnsReady', 'ordersInProcess', 'ordersHistory', 'returnsHistory'));
    }

    // Method untuk menangani pengambilan retur
    public function pickupRetur($id)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $invoice = HInvoice::findOrFail($id);
            
            // Pastikan ini adalah retur yang di-assign ke driver ini
            if ($invoice->driver_id != $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengambil retur ini.');
            }
            
            // Update status retur
            $invoice->status = 'retur_selesai';
            $invoice->save();
            
            // Update status di tabel returns jika ada
            $retur = Returns::where('invoice_id', $id)->first();
            if ($retur) {
                $retur->status = 'selesai';
                $retur->save();
            }
            
            return redirect()->back()->with('success', 'Pengambilan retur berhasil diselesaikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil retur.');
        }
    }

    // Method untuk melihat detail retur
    public function detailRetur($id)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $invoice = HInvoice::with(['customer', 'returns'])->findOrFail($id);
            
            // Pastikan ini adalah retur yang di-assign ke driver ini
            if ($invoice->driver_id != $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat detail retur ini.');
            }
            
            return view('admin.driver-retur.detail', compact('invoice'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Retur tidak ditemukan atau terjadi kesalahan.');
        }
    }
}
