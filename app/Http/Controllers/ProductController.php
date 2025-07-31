<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function view()
    {
        return view('admin.products.view', [
            'products' => Product::all(),
        ]);
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function createProductAction(Request $request)
    {
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
            $path = $request->file('image')
                ->store('products', 'public'); // disimpan di storage/app/public/products
            $data['image'] = $path;
        }

        // 3) Simpan
        Product::create($data);

        return redirect()
            ->route('admin.products.view')
            ->with('success', 'Produk baru berhasil ditambahkan.');
    }

    public function detail($id)
    {
        $product = Product::findOrFail($id);

        return view('admin.products.detail', compact('product'));
    }

    public function updateProductAction(Request $request, $id)
    {
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

        // 2) Jika ada upload baru, hapus file lama dan simpan yang baru
        if ($request->hasFile('image')) {
            // hapus lama
            if ($product->image && Storage::disk('public')->exists($product->image)) {
                Storage::disk('public')->delete($product->image);
            }
            // simpan baru
            $path = $request->file('image')->store('products', 'public');
            $data['image'] = $path;
        }

        // 3) Update
        $product->update($data);

        return redirect()
            ->route('admin.products.view')
            ->with('success', 'Produk berhasil diupdate.');
    }

    public function updateMinPriceAction(Request $request, $id)
    {
        $request->validate(['min_price' => 'required|numeric|min:0']);
        $product = Product::findOrFail($id);
        $product->min_price = $request->input('min_price');
        $product->save();

        return redirect()
            ->route('admin.products.view')
            ->with('success', 'Minimal price berhasil diupdate.');
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
        $request->validate([
            'color' => 'required|string|max:100',
            'stock' => 'required|integer|min:0',
        ]);

        $product = Product::findOrFail($id);
        $product->variants()->create($request->only('color', 'stock'));

        return redirect()->to('/admin/products/detail/' . $id)
            ->with('success', 'Variant berhasil ditambahkan.');
    }
}
