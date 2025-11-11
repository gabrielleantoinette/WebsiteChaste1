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
use App\Models\CustomMaterial;
use App\Services\NotificationService;
use Barryvdh\DomPDF\Facade\Pdf;

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
    
        // Query dinvoice untuk detail produk yang harus disiapkan
        $cartItems = DB::table('dinvoice')
            ->leftJoin('product_variants', 'dinvoice.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'dinvoice.*',
                'products.name as product_name',
                'products.size as product_size',
                'product_variants.color as variant_color',
                'dinvoice.price as harga_custom',
                'dinvoice.quantity',
                'dinvoice.subtotal',
                'dinvoice.kebutuhan_custom',
                'dinvoice.warna_custom'
            )
            ->where('dinvoice.hinvoice_id', $invoice->id)
            ->get();
    
        // Jika dinvoice kosong, coba ambil dari cart sebagai fallback
        if ($cartItems->isEmpty()) {
            $cartItems = DB::table('cart')
                ->leftJoin('product_variants', 'cart.variant_id', '=', 'product_variants.id')
                ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                ->select(
                    'cart.*',
                    'products.name as product_name',
                    'products.size as product_size',
                    'product_variants.color as variant_color'
                )
                ->where('cart.user_id', $invoice->customer_id)
                ->get();
        }
    
        return view('admin.gudang-transaksi.detail', compact('invoice', 'cartItems'));
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
                $nama = $item->product_name ?? $item->kebutuhan_custom ?? 'Produk';
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

    // Halaman Stok Barang
    public function viewStokBarang()
    {
        $products = Product::with(['variants', 'category'])
            ->where('live', true)
            ->orderBy('name')
            ->get();

        $customMaterials = \App\Models\CustomMaterial::with(['variants'])
            ->orderBy('name')
            ->get();

        $rawMaterials = \App\Models\RawMaterial::orderBy('name')->get();

        return view('admin.gudang.stok-barang', compact('products', 'customMaterials', 'rawMaterials'));
    }

    // Laporan Stok Harian
    public function laporanStokHarian(Request $request)
    {
        $periode = $request->get('periode', 'harian');
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $targetYear = $request->get('year');
        
        // Set tanggal berdasarkan periode
        switch ($periode) {
            case 'harian':
                $startDate = $tanggal;
                $endDate = $tanggal;
                break;
            case 'mingguan':
                $startDate = now()->startOfWeek()->format('Y-m-d');
                $endDate = now()->endOfWeek()->format('Y-m-d');
                break;
            case 'bulanan':
                $startDate = now()->startOfMonth()->format('Y-m-d');
                $endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'tahunan':
                $startDate = now()->startOfYear()->format('Y-m-d');
                $endDate = now()->endOfYear()->format('Y-m-d');
                break;
            default:
                $startDate = $tanggal;
                $endDate = $tanggal;
        }

        // Data stok saat ini
        $stokSaatIni = $this->getStokSaatIni();
        
        // Data stok masuk
        $stokMasuk = $this->getStokMasuk($startDate, $endDate);
        
        // Data stok keluar
        $stokKeluar = $this->getStokKeluar($startDate, $endDate);

        return view('admin.gudang.laporan-stok', compact(
            'stokSaatIni', 
            'stokMasuk', 
            'stokKeluar', 
            'periode', 
            'tanggal', 
            'startDate', 
            'endDate'
        ));
    }

    // Export PDF Laporan Stok
    public function exportLaporanStokPDF(Request $request)
    {
        if ($request->has('periode') && !$request->has('range')) {
            $request->merge(['range' => $request->input('periode')]);
        }
        if ($request->has('tanggal') && !$request->has('date')) {
            $request->merge(['date' => $request->input('tanggal')]);
        }
        if ($request->has('bulan') && !$request->has('month')) {
            $request->merge(['month' => $request->input('bulan')]);
        }
        if ($request->has('tahun') && !$request->has('year')) {
            $request->merge(['year' => $request->input('tahun')]);
        }

        $range = ReportDateRange::fromRequest($request, 'harian');
        $startDate = $range['start']->toDateString();
        $endDate = $range['end']->toDateString();
        $judulPeriode = $range['label'];

        $stokSaatIni = $this->getStokSaatIni();
        $stokMasuk = $this->getStokMasuk($startDate, $endDate);
        $stokKeluar = $this->getStokKeluar($startDate, $endDate);

        $periodeStart = $range['start'];
        $periodeEnd = $range['end'];
        $periodeLabel = $judulPeriode;

        $pdf = Pdf::loadView('exports.laporan_stok_pdf', compact(
            'stokSaatIni', 
            'stokMasuk', 
            'stokKeluar', 
            'judulPeriode', 
            'startDate', 
            'endDate',
            'periodeStart',
            'periodeEnd',
            'periodeLabel'
        ));

        return $pdf->download('laporan-stok-' . $range['range'] . '-' . date('Y-m-d') . '.pdf');
    }

    // Helper method untuk mendapatkan stok saat ini
    private function getStokSaatIni()
    {
        // Stok produk regular
        $products = Product::with(['variants', 'category'])
            ->where('live', true)
            ->get()
            ->map(function ($product) {
                $totalStock = $product->variants->sum('stock');
                return [
                    'id' => $product->id,
                    'nama' => $product->name,
                    'ukuran' => $product->size ?? '-',
                    'kategori' => $product->category->name ?? 'Tanpa Kategori',
                    'tipe' => 'Produk Regular',
                    'stok' => $totalStock,
                    'variants' => $product->variants->map(function ($variant) {
                        return [
                            'warna' => $variant->color,
                            'stok' => $variant->stock
                        ];
                    })
                ];
            });

        // Stok custom materials
        $customMaterials = \App\Models\CustomMaterial::with(['variants'])
            ->get()
            ->map(function ($material) {
                $totalStock = $material->variants->sum('stock');
                return [
                    'id' => $material->id,
                    'nama' => $material->name,
                    'ukuran' => '-',
                    'kategori' => 'Custom Material',
                    'tipe' => 'Custom Material',
                    'stok' => $totalStock,
                    'variants' => $material->variants->map(function ($variant) {
                        return [
                            'warna' => $variant->color,
                            'stok' => $variant->stock
                        ];
                    })
                ];
            });

        // Stok bahan baku
        $rawMaterials = \App\Models\RawMaterial::get()
            ->map(function ($material) {
                return [
                    'id' => $material->id,
                    'nama' => $material->name,
                    'ukuran' => '-',
                    'kategori' => 'Bahan Baku',
                    'tipe' => 'Bahan Baku',
                    'stok' => $material->stock,
                    'variants' => []
                ];
            });

        return $products->concat($customMaterials)->concat($rawMaterials);
    }

    // Helper method untuk mendapatkan stok masuk
    private function getStokMasuk($startDate, $endDate)
    {
        $stokMasuk = [];

        // Stok masuk dari work orders selesai
        $workOrdersSelesai = WorkOrder::with(['items'])
            ->where('status', 'selesai')
            ->whereBetween('completed_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        foreach ($workOrdersSelesai as $wo) {
            foreach ($wo->items as $item) {
                $key = 'Work Order ' . $wo->code;
                if (!isset($stokMasuk[$key])) {
                    $stokMasuk[$key] = [
                        'sumber' => $key,
                        'tanggal' => $wo->completed_at,
                        'tipe' => 'Produksi',
                        'items' => []
                    ];
                }
                
                $namaMaterial = $item->size_material ? "Material: {$item->size_material}" : "Material";
                $detailWarna = $item->color ? " - Warna: {$item->color}" : "";
                $namaLengkap = $namaMaterial . $detailWarna;
                
                $keterangan = 'Produksi Selesai';
                if ($item->remarks) {
                    $keterangan .= " - {$item->remarks}";
                }
                
                $stokMasuk[$key]['items'][] = [
                    'nama' => $namaLengkap,
                    'ukuran' => $item->size_material ?? '-',
                    'qty' => $item->completed_quantity,
                    'keterangan' => $keterangan
                ];
            }
        }

        // Stok masuk dari retur yang diterima
        $returDiterima = Returns::with(['invoice.customer'])
            ->where('status', 'selesai')
            ->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        foreach ($returDiterima as $retur) {
            $key = 'Retur #' . $retur->id;
            $customerName = $retur->invoice->customer->name ?? 'Customer';
            
            $keterangan = "Retur dari {$customerName}";
            if ($retur->description) {
                $keterangan .= " - {$retur->description}";
            }
            
            $stokMasuk[$key] = [
                'sumber' => $key,
                'tanggal' => $retur->updated_at,
                'tipe' => 'Retur Customer',
                'items' => [
                    [
                        'nama' => "Barang Retur dari {$customerName}",
                        'ukuran' => '-',
                        'qty' => 1,
                        'keterangan' => $keterangan
                    ]
                ]
            ];
        }

        return collect($stokMasuk)->sortBy('tanggal');
    }

    // Helper method untuk mendapatkan stok keluar
    private function getStokKeluar($startDate, $endDate)
    {
        $stokKeluar = [];

        // Stok keluar dari penjualan (menggunakan dinvoice)
        $penjualan = DB::table('dinvoice')
            ->join('hinvoice', 'dinvoice.hinvoice_id', '=', 'hinvoice.id')
            ->leftJoin('product_variants', 'dinvoice.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->whereBetween('hinvoice.created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->whereIn('hinvoice.status', ['dibayar', 'dikemas', 'Dikemas', 'dikirim', 'selesai', 'sampai'])
            ->select(
                'hinvoice.code as invoice_code',
                'hinvoice.created_at',
                'dinvoice.quantity',
                'products.name as product_name',
                'products.size as product_size',
                'product_variants.color as product_color',
                'dinvoice.price as harga_custom'
            )
            ->get();

        foreach ($penjualan as $item) {
            $key = 'Penjualan ' . $item->invoice_code;
            if (!isset($stokKeluar[$key])) {
                $stokKeluar[$key] = [
                    'sumber' => $key,
                    'tanggal' => $item->created_at,
                    'tipe' => 'Penjualan',
                    'items' => []
                ];
            }

            // Buat nama produk yang lebih detail
            if ($item->product_name) {
                $namaProduk = $item->product_name;
                $detailWarna = $item->product_color ? " - Warna: {$item->product_color}" : "";
                $namaLengkap = $namaProduk . $detailWarna;
            } else {
                $namaLengkap = 'Produk Custom';
            }

            $stokKeluar[$key]['items'][] = [
                'nama' => $namaLengkap,
                'ukuran' => $item->product_size ?? '-',
                'qty' => $item->quantity,
                'keterangan' => 'Penjualan'
            ];
        }

        // Stok keluar dari work orders (bahan baku)
        $workOrdersBahan = WorkOrder::with(['items'])
            ->whereIn('status', ['dibuat', 'dikerjakan', 'selesai'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        foreach ($workOrdersBahan as $wo) {
            $key = 'Work Order Bahan ' . $wo->code;
            if (!isset($stokKeluar[$key])) {
                $stokKeluar[$key] = [
                    'sumber' => $key,
                    'tanggal' => $wo->created_at,
                    'tipe' => 'Bahan Baku',
                    'items' => []
                ];
            }
            foreach ($wo->items as $item) {
                $namaMaterial = $item->size_material ? "Material: {$item->size_material}" : "Material";
                $detailWarna = $item->color ? " - Warna: {$item->color}" : "";
                $namaLengkap = $namaMaterial . $detailWarna;
                
                $keterangan = 'Work Order';
                if ($item->remarks) {
                    $keterangan .= " - {$item->remarks}";
                }
                
                $stokKeluar[$key]['items'][] = [
                    'nama' => $namaLengkap,
                    'ukuran' => $item->size_material ?? '-',
                    'qty' => $item->quantity,
                    'keterangan' => $keterangan
                ];
            }
        }

        // Stok keluar dari barang rusak
        $barangRusak = DamagedProduct::with(['product', 'variant', 'retur'])
            ->whereBetween('created_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->get();

        foreach ($barangRusak as $rusak) {
            $key = 'Barang Rusak #' . $rusak->id;
            
            $namaProduk = $rusak->product->name ?? 'Produk';
            $detailWarna = $rusak->variant && $rusak->variant->color ? " - Warna: {$rusak->variant->color}" : "";
            $namaLengkap = $namaProduk . $detailWarna;
            
            $keterangan = 'Barang Rusak';
            if ($rusak->damage_description) {
                $keterangan .= " - {$rusak->damage_description}";
            }
            
            $stokKeluar[$key] = [
                'sumber' => $key,
                'tanggal' => $rusak->created_at,
                'tipe' => 'Barang Rusak',
                'items' => [
                    [
                        'nama' => $namaLengkap,
                        'ukuran' => $rusak->product->size ?? '-',
                        'qty' => $rusak->quantity,
                        'keterangan' => $keterangan
                    ]
                ]
            ];
        }

        return collect($stokKeluar)->sortBy('tanggal');
    }
}
