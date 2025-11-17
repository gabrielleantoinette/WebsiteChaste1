<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function view()
    {
        $products = Product::with('variants')->paginate(12);
        $totalProducts = Product::count();
        $activeProducts = Product::where('live', 1)->count();
        $inactiveProducts = $totalProducts - $activeProducts;
        $totalCategories = \App\Models\Categories::count();

        return view('admin.products.view', compact(
            'products', 'totalProducts', 'activeProducts', 'inactiveProducts', 'totalCategories'
        ));
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function createProductAction(Request $request)
    {
        try {
            // 1) Validasi
            $data = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'image'       => 'nullable|image|max:2048', // max 2MB
                'price'       => 'required|numeric|min:0',
                'size'        => 'required|in:2x3,3x4,4x6,6x8',
                'live'        => 'required|boolean',
            ]);

            // 2) Upload image jika ada
            if ($request->hasFile('image')) {
                try {
                    $data['image'] = $this->storeProductImage($request->file('image'), $data['name']);
                } catch (\Exception $e) {
                    \Log::error('Error uploading image in createProductAction', [
                        'error' => $e->getMessage()
                    ]);
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Gagal mengupload gambar: ' . $e->getMessage());
                }
            }

            // 3) Simpan
            $product = Product::create($data);

            // 4) Kirim notifikasi ke owner
            $notificationService = app(NotificationService::class);
            $notificationService->notifyAdminAction([
                'message' => "Admin telah menambahkan produk baru: {$product->name}",
                'action_id' => $product->id,
                'action_url' => "/admin/products/detail/{$product->id}",
                'priority' => 'normal'
            ]);

            return redirect()
                ->route('admin.products.view')
                ->with('success', 'Produk baru berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error creating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat menambahkan produk: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $product = Product::findOrFail($id);
        $user = Session::get('user');
        $isOwner = isset($user['role']) && $user['role'] === 'owner';

        return view('admin.products.detail', compact('product', 'isOwner'));
    }

    public function updateProductAction(Request $request, $id)
    {
        try {
            \Log::info('UpdateProductAction called', [
                'product_id' => $id,
                'has_file' => $request->hasFile('image'),
                'file_valid' => $request->hasFile('image') ? $request->file('image')->isValid() : false,
                'file_size' => $request->hasFile('image') ? $request->file('image')->getSize() : null,
                'all_input' => $request->except(['image']),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type')
            ]);
            
            // 1) Validasi
            $data = $request->validate([
                'name'        => 'required|string|max:255',
                'description' => 'nullable|string',
                'image'       => 'nullable|image|max:2048',
                'price'       => 'required|numeric|min:0',
                'size'        => 'required|in:2x3,3x4,4x6,6x8',
                'live'        => 'required|boolean',
            ]);

            $product = Product::findOrFail($id);
            
            \Log::info('Product found, starting update', [
                'product_id' => $id,
                'current_image' => $product->image
            ]);

            // 2) Jika ada upload baru, hapus file lama dan simpan yang baru
            if ($request->hasFile('image')) {
                try {
                    $this->deleteExistingImage($product->image);
                    $data['image'] = $this->storeProductImage($request->file('image'), $data['name']);
                } catch (\Exception $e) {
                    \Log::error('Error uploading image in updateProductAction', [
                        'error' => $e->getMessage(),
                        'product_id' => $id
                    ]);
                    return redirect()
                        ->back()
                        ->withInput()
                        ->with('error', 'Gagal mengupload gambar: ' . $e->getMessage());
                }
            }

            // 3) Update
            $product->update($data);

            // 4) Kirim notifikasi ke owner
            $notificationService = app(NotificationService::class);
            $notificationService->notifyAdminAction([
                'message' => "Admin telah mengupdate produk: {$product->name}",
                'action_id' => $product->id,
                'action_url' => "/admin/products/detail/{$product->id}",
                'priority' => 'normal'
            ]);

            return redirect()
                ->route('admin.products.view')
                ->with('success', 'Produk berhasil diupdate.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()
                ->back()
                ->withErrors($e->errors())
                ->withInput();
        } catch (\Exception $e) {
            \Log::error('Error updating product', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'product_id' => $id
            ]);
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Terjadi kesalahan saat mengupdate produk: ' . $e->getMessage());
        }
    }

    private function storeProductImage($file, string $productName): string
    {
        try {
            $directory = public_path('images/products');
            
            // Pastikan folder parent juga ada
            $parentDir = public_path('images');
            if (!File::exists($parentDir)) {
                File::makeDirectory($parentDir, 0775, true);
                \Log::info('Created images directory', ['path' => $parentDir]);
            }
            
            // Buat folder products jika belum ada
            if (!File::exists($directory)) {
                $created = File::makeDirectory($directory, 0775, true);
                \Log::info('Created products directory', [
                    'path' => $directory,
                    'created' => $created,
                    'exists_after' => File::exists($directory),
                    'is_writable' => is_writable($directory)
                ]);
            }
            
            // Pastikan folder writable
            if (!is_writable($directory)) {
                chmod($directory, 0775);
                \Log::warning('Changed directory permissions', ['path' => $directory]);
            }
            
            $extension = $file->getClientOriginalExtension();
            $filename = Str::slug($productName) . '-' . time() . '.' . $extension;
            $fullPath = $directory . '/' . $filename;
            
            \Log::info('Attempting to move uploaded file', [
                'source' => $file->getPathname(),
                'destination' => $fullPath,
                'directory_exists' => File::exists($directory),
                'directory_writable' => is_writable($directory),
                'file_size' => $file->getSize(),
                'file_error' => $file->getError(),
                'file_error_message' => $file->getErrorMessage(),
                'php_upload_max_filesize' => ini_get('upload_max_filesize'),
                'php_post_max_size' => ini_get('post_max_size'),
                'php_max_file_uploads' => ini_get('max_file_uploads')
            ]);
            
            // Cek error upload
            if ($file->getError() !== UPLOAD_ERR_OK) {
                $errorMessages = [
                    UPLOAD_ERR_INI_SIZE => 'File terlalu besar (melebihi upload_max_filesize)',
                    UPLOAD_ERR_FORM_SIZE => 'File terlalu besar (melebihi MAX_FILE_SIZE)',
                    UPLOAD_ERR_PARTIAL => 'File hanya ter-upload sebagian',
                    UPLOAD_ERR_NO_FILE => 'Tidak ada file yang diupload',
                    UPLOAD_ERR_NO_TMP_DIR => 'Folder temporary tidak ditemukan',
                    UPLOAD_ERR_CANT_WRITE => 'Gagal menulis file ke disk',
                    UPLOAD_ERR_EXTENSION => 'Upload dihentikan oleh extension PHP'
                ];
                $errorMsg = $errorMessages[$file->getError()] ?? 'Unknown upload error: ' . $file->getError();
                throw new \Exception($errorMsg);
            }
            
            // Gunakan move_uploaded_file untuk keamanan yang lebih baik
            if ($file->isValid()) {
                $moved = $file->move($directory, $filename);
                
                if (!$moved || !File::exists($fullPath)) {
                    throw new \Exception('Failed to move uploaded file. Check directory permissions.');
                }
                
                // Set permission file
                @chmod($fullPath, 0644);
                
                \Log::info('File uploaded successfully', [
                    'filename' => $filename,
                    'path' => $fullPath,
                    'size' => File::size($fullPath),
                    'url' => 'images/products/' . $filename
                ]);
                
                return 'images/products/' . $filename;
            } else {
                throw new \Exception('Uploaded file is not valid: ' . $file->getErrorMessage());
            }
        } catch (\Exception $e) {
            \Log::error('Error uploading product image', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'product_name' => $productName,
                'directory' => $directory ?? 'not set'
            ]);
            throw $e;
        }
    }

    private function deleteExistingImage(?string $path): void
    {
        if (!$path) {
            return;
        }

        $publicPath = public_path($path);
        if (File::exists($publicPath)) {
            File::delete($publicPath);
            return;
        }

        if (Storage::disk('public')->exists($path)) {
            Storage::disk('public')->delete($path);
        }
    }

    public function updateMinPricePerSizeAction(Request $request, $id)
    {
        $user = Session::get('user');
        // Hanya owner yang boleh update min_price_per_size
        if (!isset($user['role']) || $user['role'] !== 'owner') {
            return redirect()
                ->to('/admin/products/detail/' . $id)
                ->with('error', 'Anda tidak memiliki akses untuk mengubah batas tawar per ukuran.');
        }

        $product = Product::findOrFail($id);
        $minPricePerSize = $request->input('min_price_per_size', []);
        
        // Filter out empty values dan convert to integer
        $filteredPrices = [];
        foreach ($minPricePerSize as $size => $price) {
            if (!empty($price) && is_numeric($price)) {
                $filteredPrices[$size] = (int) $price;
            }
        }
        
        // Jika semua kosong, set null untuk auto-calculate
        if (empty($filteredPrices)) {
            $product->min_price_per_size = null;
        } else {
            $product->min_price_per_size = $filteredPrices;
        }
        
        $product->save();

        return redirect()
            ->to('/admin/products/detail/' . $id)
            ->with('success', 'Batas tawar per ukuran berhasil diupdate.');
    }

    public function updateMinBuyingStockAction(Request $request, $id)
    {
        $user = Session::get('user');
        // Hanya owner yang boleh update min_buying_stock
        if (!isset($user['role']) || $user['role'] !== 'owner') {
            return redirect()
                ->to('/admin/products/detail/' . $id)
                ->with('error', 'Anda tidak memiliki akses untuk mengubah minimal quantity.');
        }

        $request->validate(['min_buying_stock' => 'required|integer|min:0']);
        $product = Product::findOrFail($id);
        $minBuyingStock = (int) $request->input('min_buying_stock');
        $product->min_buying_stock = $minBuyingStock;
        $product->save();

        $message = $minBuyingStock === 0 
            ? 'Fitur tawar menawar untuk produk ini telah dinonaktifkan.'
            : 'Minimum quantity untuk tawar menawar berhasil diupdate.';

        return redirect()
            ->to('/admin/products/detail/' . $id)
            ->with('success', $message);
    }

    public function updateSizePricesAction(Request $request, $id)
    {
        $user = Session::get('user');
        // Hanya owner yang boleh update size_prices
        if (!isset($user['role']) || $user['role'] !== 'owner') {
            return redirect()
                ->to('/admin/products/detail/' . $id)
                ->with('error', 'Anda tidak memiliki akses untuk mengubah harga per ukuran.');
        }

        $product = Product::findOrFail($id);
        $sizePrices = $request->input('size_prices', []);
        
        // Validasi: harga 2x3 wajib diisi
        if (empty($sizePrices['2x3']) || !is_numeric($sizePrices['2x3'])) {
            return redirect()
                ->to('/admin/products/detail/' . $id)
                ->with('error', 'Harga untuk ukuran 2x3 wajib diisi karena merupakan harga dasar.');
        }
        
        // Filter out empty values dan convert to integer
        $filteredPrices = [];
        foreach ($sizePrices as $size => $price) {
            if (!empty($price) && is_numeric($price)) {
                $filteredPrices[$size] = (int) $price;
            }
        }
        
        // Pastikan 2x3 selalu ada
        if (!isset($filteredPrices['2x3'])) {
            $filteredPrices['2x3'] = (int) $sizePrices['2x3'];
        }
        
        // Update harga dasar (price) dengan harga 2x3
        $product->price = $filteredPrices['2x3'];
        
        // Jika hanya 2x3 yang diisi, set null untuk auto-calculate ukuran lain
        if (count($filteredPrices) === 1 && isset($filteredPrices['2x3'])) {
            $product->size_prices = null;
        } else {
            $product->size_prices = $filteredPrices;
        }
        
        $product->save();

        return redirect()
            ->to('/admin/products/detail/' . $id)
            ->with('success', 'Harga per ukuran berhasil diupdate.');
    }

    /**
     * Cek stok produk dan kirim notifikasi jika stok rendah
     */
    private function checkLowStock($productId)
    {
        $product = Product::find($productId);
        if ($product && $product->stock <= 10) { // Threshold stok rendah
            $notificationService = app(NotificationService::class);
            $notificationService->notifyLowStock($productId, [
                'name' => $product->name,
                'stock' => $product->stock,
                'min_stock' => 10
            ]);
        }
    }

    public function createVariant($id)
    {
        $product = Product::findOrFail($id);

        return view('admin.products.create-variant', compact('product'));
    }

    public function createVariantAction(Request $request, $id)
    {
        try {
            $request->validate([
                'color' => 'required|string|max:100',
                'stock' => 'required|integer|min:0',
            ]);

            $product = Product::findOrFail($id);
            $variant = $product->variants()->create($request->only('color', 'stock'));

            // Always return JSON for AJAX requests
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Variant berhasil ditambahkan.',
                    'variant' => $variant
                ]);
            }

            return redirect()->to('/admin/products/detail/' . $id)
                ->with('success', 'Variant berhasil ditambahkan.');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal menambahkan variant: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal menambahkan variant: ' . $e->getMessage());
        }
    }

    public function updateVariantAction(Request $request, $productId, $variantId)
    {
        try {
            // Debug: Log request data
            \Log::info('UpdateVariant Request Data:', [
                'productId' => $productId,
                'variantId' => $variantId,
                'all' => $request->all(),
                'color' => $request->input('color'),
                'stock' => $request->input('stock'),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson()
            ]);
            
            $request->validate([
                'color' => 'required|string|max:100',
                'stock' => 'required|integer|min:0',
            ]);

            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->firstOrFail();

            $variant->update($request->only('color', 'stock'));

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Variant berhasil diupdate.']);
            }

            return redirect()->to('/admin/products/detail/' . $productId)
                ->with('success', 'Variant berhasil diupdate.');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal mengupdate variant: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal mengupdate variant: ' . $e->getMessage());
        }
    }

    public function deleteVariantAction(Request $request, $productId, $variantId)
    {
        try {
            // Debug: Log delete request data
            \Log::info('DeleteVariant Request Data:', [
                'productId' => $productId,
                'variantId' => $variantId,
                'all' => $request->all(),
                'method' => $request->method(),
                'content_type' => $request->header('Content-Type'),
                'is_ajax' => $request->ajax(),
                'wants_json' => $request->wantsJson()
            ]);
            
            $variant = ProductVariant::where('id', $variantId)
                ->where('product_id', $productId)
                ->firstOrFail();

            $variant->delete();

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => true, 'message' => 'Variant berhasil dihapus.']);
            }

            return redirect()->to('/admin/products/detail/' . $productId)
                ->with('success', 'Variant berhasil dihapus.');
                
        } catch (\Exception $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Gagal menghapus variant: ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()->with('error', 'Gagal menghapus variant: ' . $e->getMessage());
        }
    }
}
