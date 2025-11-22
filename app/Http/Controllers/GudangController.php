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
    
        $cartItems = DB::table('dinvoice')
            ->leftJoin('product_variants', 'dinvoice.variant_id', '=', 'product_variants.id')
            ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'dinvoice.*',
                'products.name as product_name',
                'dinvoice.selected_size as selected_size',
                'product_variants.color as variant_color',
                'dinvoice.price as harga_custom',
                'dinvoice.quantity',
                'dinvoice.subtotal',
                'dinvoice.kebutuhan_custom',
                'dinvoice.warna_custom',
                'dinvoice.bahan_custom',
                'dinvoice.ukuran_custom',
                'dinvoice.jumlah_ring_custom',
                'dinvoice.pakai_tali_custom',
                'dinvoice.catatan_custom'
            )
            ->where('dinvoice.hinvoice_id', $invoice->id)
            ->get();
    
        if ($cartItems->isEmpty()) {
            $cartItems = DB::table('cart')
                ->leftJoin('product_variants', 'cart.variant_id', '=', 'product_variants.id')
                ->leftJoin('products', 'product_variants.product_id', '=', 'products.id')
                ->select(
                    'cart.*',
                    'products.name as product_name',
                    'cart.selected_size as selected_size',
                    'cart.ukuran_custom as ukuran_custom',
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
            'quality_proof_photo' => 'required|array|min:1',
            'quality_proof_photo.*' => 'image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'quality_proof_photo.required' => 'Minimal 1 foto bukti kualitas barang wajib diupload',
            'quality_proof_photo.array' => 'Format file tidak valid',
            'quality_proof_photo.min' => 'Minimal 1 foto bukti kualitas barang wajib diupload',
            'quality_proof_photo.*.image' => 'File harus berupa gambar',
            'quality_proof_photo.*.mimes' => 'Format file harus jpeg, png, atau jpg',
            'quality_proof_photo.*.max' => 'Ukuran file maksimal 2MB per gambar'
        ]);

        $invoice = HInvoice::findOrFail($id);
        $user = Session::get('user');
        $invoice->gudang_id = $user->id;
        
        if ($request->hasFile('quality_proof_photo')) {
            $uploadedPaths = [];
            
            foreach ($request->file('quality_proof_photo') as $file) {
                $originalName = $file->getClientOriginalName();
                $extension = $file->getClientOriginalExtension();
                $timestamp = now()->format('YmdHis');
                $randomString = \Str::random(10);
                $filename = 'quality_proof_' . $invoice->code . '_' . $timestamp . '_' . $randomString . '_' . \Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
                
                $path = $file->storeAs('quality_proofs', $filename, 'public');
                $uploadedPaths[] = $path;
                
                \Log::info('Quality proof photo uploaded', [
                    'original_name' => $originalName,
                    'unique_file_name' => $filename,
                    'stored_path' => $path,
                    'file_size' => $file->getSize(),
                    'mime_type' => $file->getMimeType(),
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->code,
                    'gudang_id' => $user->id,
                    'timestamp' => $timestamp
                ]);
                
                if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                    \Log::error('Quality proof file not saved correctly', [
                        'path' => $path,
                        'invoice_id' => $invoice->id
                    ]);
                } else {
                    $storedFileSize = \Illuminate\Support\Facades\Storage::disk('public')->size($path);
                    \Log::info('Quality proof file verified', [
                        'path' => $path,
                        'original_size' => $file->getSize(),
                        'stored_size' => $storedFileSize,
                        'match' => $file->getSize() === $storedFileSize
                    ]);
                }
            }
            
            $invoice->quality_proof_photo = json_encode($uploadedPaths);
        }
        if ($invoice->shipping_courier && $invoice->shipping_courier !== 'kurir') {
            $invoice->status = 'dikirim_ke_agen';
        } else {
            $invoice->status = 'dikemas';
        }
        $invoice->save();
        
        if ($invoice->quality_proof_photo) {
            $photos = json_decode($invoice->quality_proof_photo, true);
            
            if (is_array($photos)) {
                $allFilesExist = true;
                foreach ($photos as $path) {
                    if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($path)) {
                        $allFilesExist = false;
                        \Log::warning('Quality proof file not found', [
                            'invoice_id' => $invoice->id,
                            'path' => $path
                        ]);
                    }
                }
                
                \Log::info('Invoice updated with quality proof photos', [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->code,
                    'photo_count' => count($photos),
                    'all_files_exist' => $allFilesExist,
                    'gudang_id' => $user->id
                ]);
            } else {
                $fileExists = \Illuminate\Support\Facades\Storage::disk('public')->exists($invoice->quality_proof_photo);
                
                \Log::info('Invoice updated with quality proof photo (legacy format)', [
                    'invoice_id' => $invoice->id,
                    'invoice_code' => $invoice->code,
                    'quality_proof_path' => $invoice->quality_proof_photo,
                    'file_exists' => $fileExists,
                    'gudang_id' => $user->id
                ]);
            }
        }

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

        $notificationService->notifyWarehouseAction([
            'message' => "Gudang telah menyiapkan pesanan {$invoice->code} untuk customer {$invoice->customer->name}",
            'action_id' => $invoice->id,
            'action_url' => "/admin/gudang-transaksi/detail/{$invoice->id}",
            'priority' => 'normal'
        ]);



        return redirect()->back()->with('success', 'Berhasil menyiapkan barang dan upload foto bukti kualitas.');
    }

    public function uploadQualityPhoto(Request $request, $id)
    {
        $request->validate([
            'photo' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ], [
            'photo.required' => 'Foto wajib diupload',
            'photo.image' => 'File harus berupa gambar',
            'photo.mimes' => 'Format file harus jpeg, png, atau jpg',
            'photo.max' => 'Ukuran file maksimal 2MB'
        ]);

        $invoice = HInvoice::findOrFail($id);
        $user = Session::get('user');
        
        if ($request->hasFile('photo')) {
            $file = $request->file('photo');
            $originalName = $file->getClientOriginalName();
            $extension = $file->getClientOriginalExtension();
            $timestamp = now()->format('YmdHis');
            $randomString = \Str::random(10);
            $filename = 'quality_proof_' . $invoice->code . '_' . $timestamp . '_' . $randomString . '_' . \Str::slug(pathinfo($originalName, PATHINFO_FILENAME)) . '.' . $extension;
            
            $path = $file->storeAs('quality_proofs', $filename, 'public');
            
            $existingPhotos = [];
            if ($invoice->quality_proof_photo) {
                $existingPhotos = json_decode($invoice->quality_proof_photo, true);
                if (!is_array($existingPhotos)) {
                    $existingPhotos = [$invoice->quality_proof_photo];
                }
            }
            
            $existingPhotos[] = $path;
            $invoice->quality_proof_photo = json_encode($existingPhotos);
            $invoice->save();
            
            $cleanPath = ltrim($path, '/');
            $imageUrl = url('/public/storage/' . $cleanPath);
            
            return response()->json([
                'success' => true,
                'message' => 'Foto berhasil diupload',
                'path' => $path,
                'url' => $imageUrl,
                'total_photos' => count($existingPhotos)
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Gagal mengupload foto'
        ], 400);
    }

    public function deleteQualityPhoto(Request $request, $id, $photoIndex)
    {
        $invoice = HInvoice::findOrFail($id);
        $user = Session::get('user');
        
        if (!$invoice->quality_proof_photo) {
            return response()->json([
                'success' => false,
                'message' => 'Tidak ada foto untuk dihapus'
            ], 404);
        }
        
        $photos = json_decode($invoice->quality_proof_photo, true);
        if (!is_array($photos)) {
            $photos = [$invoice->quality_proof_photo];
        }
        
        if (!isset($photos[$photoIndex])) {
            return response()->json([
                'success' => false,
                'message' => 'Foto tidak ditemukan'
            ], 404);
        }
        
        $photoToDelete = $photos[$photoIndex];
        
        if (\Illuminate\Support\Facades\Storage::disk('public')->exists($photoToDelete)) {
            \Illuminate\Support\Facades\Storage::disk('public')->delete($photoToDelete);
        }
        
        unset($photos[$photoIndex]);
        $photos = array_values($photos);
        
        if (empty($photos)) {
            $invoice->quality_proof_photo = null;
        } else {
            $invoice->quality_proof_photo = json_encode($photos);
        }
        $invoice->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Foto berhasil dihapus',
            'total_photos' => count($photos)
        ]);
    }

    public function finalizeGudang(Request $request, $id)
    {
        $invoice = HInvoice::findOrFail($id);
        $user = Session::get('user');
        
        if (!$invoice->quality_proof_photo) {
            return redirect()->back()->with('error', 'Minimal 1 foto bukti kualitas wajib diupload');
        }
        
        $photos = json_decode($invoice->quality_proof_photo, true);
        if (!is_array($photos)) {
            $photos = [$invoice->quality_proof_photo];
        }
        
        if (empty($photos)) {
            return redirect()->back()->with('error', 'Minimal 1 foto bukti kualitas wajib diupload');
        }
        
        $invoice->gudang_id = $user->id;
        
        if ($invoice->shipping_courier && $invoice->shipping_courier !== 'kurir') {
            $invoice->status = 'dikirim_ke_agen';
        } else {
            $invoice->status = 'dikemas';
        }
        $invoice->save();
        
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

        $notificationService->notifyWarehouseAction([
            'message' => "Gudang telah menyiapkan pesanan {$invoice->code} untuk customer {$invoice->customer->name}",
            'action_id' => $invoice->id,
            'action_url' => "/admin/gudang-transaksi/detail/{$invoice->id}",
            'priority' => 'normal'
        ]);

        return redirect()->back()->with('success', 'Berhasil menyiapkan barang.');
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
        
        $returans = Returns::with(['invoice.customer'])
            ->whereIn('status', ['diajukan', 'diproses'])
            ->orderByDesc('created_at')
            ->get();
        $returanCount = $returans->count();
        
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

    public function viewBarangRusak()
    {
        $damagedProducts = DamagedProduct::with(['product', 'variant', 'retur.invoice.customer'])
            ->orderByDesc('created_at')
            ->get();
        return view('admin.gudang.barang-rusak', compact('damagedProducts'));
    }

    public function perbaikiBarangRusak($id)
    {
        $damaged = DamagedProduct::findOrFail($id);
        if ($damaged->status !== 'rusak') {
            return redirect()->back()->with('error', 'Barang sudah diperbaiki atau status tidak valid.');
        }
        $damaged->status = 'diperbaiki';
        $damaged->save();
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
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $tahun = $request->get('tahun', now()->format('Y'));
        
        switch ($periode) {
            case 'harian':
                $startDate = $tanggal;
                $endDate = $tanggal;
                break;
            case 'mingguan':
                $selectedDate = \Carbon\Carbon::parse($tanggal);
                $startDate = $selectedDate->copy()->startOfWeek()->format('Y-m-d');
                $endDate = $selectedDate->copy()->endOfWeek()->format('Y-m-d');
                break;
            case 'bulanan':
                // Format bulan: YYYY-MM
                $selectedMonth = \Carbon\Carbon::createFromFormat('Y-m', $bulan);
                $startDate = $selectedMonth->copy()->startOfMonth()->format('Y-m-d');
                $endDate = $selectedMonth->copy()->endOfMonth()->format('Y-m-d');
                break;
            case 'tahunan':
                // Format tahun: YYYY
                $selectedYear = \Carbon\Carbon::createFromFormat('Y', $tahun);
                $startDate = $selectedYear->copy()->startOfYear()->format('Y-m-d');
                $endDate = $selectedYear->copy()->endOfYear()->format('Y-m-d');
                break;
            default:
                $startDate = $tanggal;
                $endDate = $tanggal;
        }

        $stokSaatIni = $this->getStokSaatIni();
        
        $stokMasuk = $this->getStokMasuk($startDate, $endDate);
        
        $stokKeluar = $this->getStokKeluar($startDate, $endDate);

        return view('admin.gudang.laporan-stok', compact(
            'stokSaatIni', 
            'stokMasuk', 
            'stokKeluar', 
            'periode', 
            'tanggal',
            'bulan',
            'tahun',
            'startDate', 
            'endDate'
        ));
    }

    // Export PDF Laporan Stok
    public function exportLaporanStokPDF(Request $request)
    {
        $periode = $request->get('periode', 'harian');
        $tanggal = $request->get('tanggal', now()->format('Y-m-d'));
        $bulan = $request->get('bulan', now()->format('Y-m'));
        $tahun = $request->get('tahun', now()->format('Y'));
        
        switch ($periode) {
            case 'harian':
                $startDate = $tanggal;
                $endDate = $tanggal;
                $judulPeriode = 'Harian - ' . \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
                break;
            case 'mingguan':
                $selectedDate = \Carbon\Carbon::parse($tanggal);
                $startDate = $selectedDate->copy()->startOfWeek()->format('Y-m-d');
                $endDate = $selectedDate->copy()->endOfWeek()->format('Y-m-d');
                $judulPeriode = 'Mingguan - ' . \Carbon\Carbon::parse($startDate)->translatedFormat('d F Y') . ' s/d ' . \Carbon\Carbon::parse($endDate)->translatedFormat('d F Y');
                break;
            case 'bulanan':
                $selectedMonth = \Carbon\Carbon::createFromFormat('Y-m', $bulan);
                $startDate = $selectedMonth->copy()->startOfMonth()->format('Y-m-d');
                $endDate = $selectedMonth->copy()->endOfMonth()->format('Y-m-d');
                $judulPeriode = 'Bulanan - ' . $selectedMonth->translatedFormat('F Y');
                break;
            case 'tahunan':
                $selectedYear = \Carbon\Carbon::createFromFormat('Y', $tahun);
                $startDate = $selectedYear->copy()->startOfYear()->format('Y-m-d');
                $endDate = $selectedYear->copy()->endOfYear()->format('Y-m-d');
                $judulPeriode = 'Tahunan - ' . $tahun;
                break;
            default:
                $startDate = $tanggal;
                $endDate = $tanggal;
                $judulPeriode = 'Harian - ' . \Carbon\Carbon::parse($tanggal)->translatedFormat('d F Y');
        }

        $stokSaatIni = $this->getStokSaatIni();
        $stokMasuk = $this->getStokMasuk($startDate, $endDate);
        $stokKeluar = $this->getStokKeluar($startDate, $endDate);

        $periodeStart = \Carbon\Carbon::parse($startDate);
        $periodeEnd = \Carbon\Carbon::parse($endDate);

        $pdf = Pdf::loadView('exports.laporan_stok_pdf', compact(
            'stokSaatIni', 
            'stokMasuk', 
            'stokKeluar', 
            'judulPeriode', 
            'startDate', 
            'endDate',
            'periodeStart',
            'periodeEnd'
        ));

        $filename = 'laporan-stok-' . $periode . '-' . date('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

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

    private function getStokMasuk($startDate, $endDate)
    {
        $stokMasuk = [];

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

    private function getStokKeluar($startDate, $endDate)
    {
        $stokKeluar = [];

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
