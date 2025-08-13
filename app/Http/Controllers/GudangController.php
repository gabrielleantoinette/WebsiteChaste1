<?php

namespace App\Http\Controllers;

use App\Models\HInvoice;
use App\Models\Returns;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\DamagedProduct;
use App\Models\Product;
use App\Models\WorkOrder;
use App\Services\NotificationService;

class GudangController extends Controller
{
    public function viewTransaksiGudang()
    {
        $invoices = HInvoice::where('status', 'dikemas')->whereNull('gudang_id')->get();
        return view('admin.gudang-transaksi.view', compact('invoices'));
    }

    public function detailTransaksiGudang($id)
    {
        $invoice = HInvoice::with('customer', 'gudang')->findOrFail($id);
    
        $cartItems = DB::table('cart')
            ->leftJoin('product_variants', 'cart.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'cart.*',
                'products.name as product_name',
                'products.price as product_price',
                'product_variants.color as variant_color'
            )
            ->where('cart.user_id', $invoice->customer_id)
            ->get();
    
        // Hitung total manual
        $total = $cartItems->reduce(function ($carry, $item) {
            $price = $item->product_price ?? $item->harga_custom;
            return $carry + ($price * $item->quantity);
        }, 0);
    
        return view('admin.gudang-transaksi.detail', compact('invoice', 'cartItems', 'total'));
    }

    public function assignGudang(Request $request, $id)
    {
        $request->validate([
            'quality_proof_photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'quality_proof_photo.required' => 'Foto bukti kualitas barang wajib diupload',
            'quality_proof_photo.image' => 'File harus berupa gambar',
            'quality_proof_photo.mimes' => 'Format file harus jpeg, png, atau jpg',
            'quality_proof_photo.max' => 'Ukuran file maksimal 2MB'
        ]);

        $invoice = HInvoice::findOrFail($id);
        $user = Session::get('user');
        $invoice->gudang_id = $user->id;
        // Upload file
        if ($request->hasFile('quality_proof_photo')) {
            $file = $request->file('quality_proof_photo');
            $filename = 'quality_proof_' . $invoice->code . '_' . time() . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs('quality_proofs', $filename, 'public');
            $invoice->quality_proof_photo = $path;
        }
        $invoice->status = 'dikemas';
        $invoice->save();

        // Kirim notifikasi ke customer bahwa pesanan sedang dikemas
        $notificationService = app(NotificationService::class);
        $notificationService->notifyOrderStatus(
            $invoice->id,
            $invoice->customer_id,
            'processing',
            [
                'invoice_code' => $invoice->code,
                'customer_name' => $invoice->customer->name
            ]
        );

        // Kirim notifikasi ke owner tentang action gudang
        $notificationService->notifyWarehouseAction([
            'message' => "Gudang telah menyiapkan pesanan {$invoice->code} untuk customer {$invoice->customer->name}",
            'action_id' => $invoice->id,
            'action_url' => "/admin/gudang-transaksi/detail/{$invoice->id}",
            'priority' => 'normal'
        ]);

        return redirect()->back()->with('success', 'Berhasil menyiapkan barang dan upload foto bukti kualitas.');
    }

    // Dashboard Gudang
    public function dashboardGudang()
    {
        $user = Session::get('user');
        if (!$user || $user->role !== 'gudang') {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }
        // Pesanan siap diproses
        $orders = HInvoice::with('customer')
            ->where('status', 'dikemas')
            ->where(function($q) use ($user) {
                $q->whereNull('gudang_id')->orWhere('gudang_id', $user->id);
            })->get();
        $siapProsesCount = $orders->count();
        // Rangkuman produk/terpal perlu disiapkan
        $produkDisiapkan = [];
        $totalProdukDisiapkan = 0;
        foreach ($orders as $order) {
            foreach ($order->cartItems ?? [] as $item) {
                $nama = $item->product_name ?? $item->nama_custom ?? 'Produk';
                $qty = $item->quantity ?? 0;
                if (!isset($produkDisiapkan[$nama])) {
                    $produkDisiapkan[$nama] = ['nama' => $nama, 'qty' => 0];
                }
                $produkDisiapkan[$nama]['qty'] += $qty;
                $totalProdukDisiapkan += $qty;
            }
        }
        $produkDisiapkan = array_values($produkDisiapkan);
        
        // Data returan untuk gudang
        $returans = Returns::with(['invoice.customer'])
            ->whereIn('status', ['diajukan', 'diproses'])
            ->orderByDesc('created_at')
            ->get();
        $returanCount = $returans->count();
        
        // Data work order untuk gudang
        $workOrders = WorkOrder::with(['createdBy', 'items'])
            ->where('assigned_to', $user->id)
            ->whereIn('status', ['dibuat', 'dikerjakan'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        $workOrderStats = [
            'pending' => WorkOrder::where('assigned_to', $user->id)->where('status', 'dibuat')->count(),
            'in_progress' => WorkOrder::where('assigned_to', $user->id)->where('status', 'dikerjakan')->count(),
            'completed' => WorkOrder::where('assigned_to', $user->id)->where('status', 'selesai')->count(),
        ];
        
        return view('admin.dashboardgudang', compact('orders', 'siapProsesCount', 'totalProdukDisiapkan', 'produkDisiapkan', 'returans', 'returanCount', 'workOrders', 'workOrderStats'));
    }

    // Menampilkan daftar barang rusak untuk staf gudang
    public function viewBarangRusak()
    {
        $damagedProducts = DamagedProduct::with(['product', 'variant', 'retur.invoice.customer'])
            ->orderByDesc('created_at')
            ->get();
        return view('admin.gudang.barang-rusak', compact('damagedProducts'));
    }

    // Update status barang rusak menjadi diperbaiki dan update stok normal
    public function perbaikiBarangRusak($id)
    {
        $damaged = DamagedProduct::findOrFail($id);
        if ($damaged->status !== 'rusak') {
            return redirect()->back()->with('error', 'Barang sudah diperbaiki atau status tidak valid.');
        }
        // Update status
        $damaged->status = 'diperbaiki';
        $damaged->save();
        // Update stok normal produk (jika nanti diaktifkan)
        // $product = Product::find($damaged->product_id);
        // if ($product) {
        //     $product->stock += $damaged->quantity;
        //     $product->save();
        // }
        return redirect()->back()->with('success', 'Barang berhasil ditandai sebagai sudah diperbaiki.');
    }
}
