<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Models\HInvoice;
use App\Models\Returns;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class DriverController extends Controller
{
    public function viewTransaksiDriver()
    {
        $user = Session::get('user');
        $drivers = Employee::where('role', 'driver')->get();
        
        $normalDeliveries = HInvoice::where('driver_id', $user->id)
            ->whereNotIn('status', ['retur_diajukan', 'retur_diambil', 'retur_selesai'])
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        $returns = HInvoice::where('driver_id', $user->id)
            ->whereIn('status', ['retur_diajukan', 'retur_diambil', 'retur_selesai'])
            ->with(['customer', 'returns'])
            ->get();
            
        $allTasks = collect();
        
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
        
        if ($invoice->status === 'dikirim_ke_agen' && $invoice->shipping_courier && $invoice->shipping_courier !== 'kurir') {
            $request->validate([
                'tracking_number' => 'required|string|max:100',
            ], [
                'tracking_number.required' => 'Nomor resi ekspedisi wajib diisi.',
            ]);
            
            $invoice->tracking_number = $request->tracking_number;
            $invoice->status = 'dikirim';
            $invoice->save();
            
            $notificationService = app(NotificationService::class);
            $notificationService->notifyOrderShipped($invoice->id, $invoice->customer_id, [
                'invoice_code' => $invoice->code,
                'customer_name' => $invoice->customer->name,
                'tracking_number' => $request->tracking_number,
                'courier' => $invoice->shipping_courier
            ]);
            
            return redirect()->back()->with('success', 'Nomor resi berhasil disimpan. Pesanan sekarang dalam status "Dikirim".');
        }
        
        $invoice->status = 'sampai';
        $invoice->save();

        $notificationService = app(NotificationService::class);
        
        $notificationService->notifyDriverAction([
            'message' => "Driver telah menyelesaikan pengiriman pesanan {$invoice->code} ke {$invoice->customer->name}",
            'action_id' => $invoice->id,
            'action_url' => "/admin/driver-transaksi/detail/{$invoice->id}",
            'priority' => 'normal'
        ]);
        $notificationService->notifyOrderStatus(
            $invoice->id,
            $invoice->customer_id,
            'delivered',
            [
                'invoice_code' => $invoice->code,
                'customer_name' => $invoice->customer->name
            ]
        );

        return redirect()->back()->with('success', 'Berhasil menyelesaikan transaksi');
    }

    public function dashboardDriver()
    {
        $user = Session::get('user');
        
        $ordersReady = HInvoice::where('driver_id', $user->id)
            ->whereIn('status', ['dikirim', 'dikirim_ke_agen'])
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        $returnsReady = HInvoice::where('driver_id', $user->id)
            ->where('status', 'retur_diambil')
            ->with(['customer', 'returns'])
            ->get();

        $ordersInProcess = HInvoice::where('driver_id', $user->id)
            ->whereIn('status', ['dikirim', 'dikirim_ke_agen'])
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        $ordersHistory = HInvoice::where('driver_id', $user->id)
            ->where('status', 'sampai')
            ->with(['customer', 'details.product', 'details.variant'])
            ->get();
            
        $returnsHistory = HInvoice::where('driver_id', $user->id)
            ->where('status', 'retur_selesai')
            ->with(['customer', 'returns'])
            ->get();

        return view('admin.dashboarddriver', compact('ordersReady', 'returnsReady', 'ordersInProcess', 'ordersHistory', 'returnsHistory'));
    }

    public function pickupRetur($id)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $invoice = HInvoice::findOrFail($id);
            
            if ($invoice->driver_id != $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk mengambil retur ini.');
            }
            
            $invoice->status = 'retur_selesai';
            $invoice->save();
            
            $retur = Returns::where('invoice_id', $id)->first();
            if ($retur) {
                $retur->status = 'selesai';
                $retur->save();
            }

            $notificationService = app(NotificationService::class);
            $notificationService->sendToCustomer(
                'return_completed',
                'Retur Selesai Diambil',
                "Retur untuk pesanan {$invoice->code} telah selesai diambil oleh tim kami. Terima kasih atas kerjasamanya.",
                $invoice->customer_id,
                [
                    'data_type' => 'return',
                    'data_id' => $invoice->id,
                    'action_url' => "/retur/{$invoice->id}",
                    'priority' => 'normal',
                    'icon' => 'fas fa-check-double'
                ]
            );
            
            return redirect()->back()->with('success', 'Pengambilan retur berhasil diselesaikan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat mengambil retur.');
        }
    }

    public function viewReturDriver()
    {
        $user = Session::get('user');
        
        $returns = HInvoice::where('driver_id', $user->id)
            ->whereIn('status', ['retur_diajukan', 'retur_diambil', 'retur_selesai'])
            ->with(['customer', 'returns'])
            ->orderBy('receive_date', 'desc')
            ->get();
            
        return view('admin.driver-retur.view', compact('returns'));
    }

    public function detailRetur($id)
    {
        try {
            $user = Session::get('user');
            if (!$user) {
                return redirect('/login')->with('error', 'Silakan login terlebih dahulu.');
            }

            $invoice = HInvoice::with(['customer', 'returns'])->findOrFail($id);
            
            if ($invoice->driver_id != $user->id) {
                return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk melihat detail retur ini.');
            }
            
            return view('admin.driver-retur.detail', compact('invoice'));
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Retur tidak ditemukan atau terjadi kesalahan.');
        }
    }
}
