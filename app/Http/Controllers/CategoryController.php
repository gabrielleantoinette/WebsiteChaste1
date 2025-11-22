<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Product;

class CategoryController extends Controller
{
    public function view(Request $request)
    {
        $query = Categories::withCount('products');
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'LIKE', "%{$search}%");
        }
        
        $categories = $query->get();
        return view('admin.categories.view', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required']);
        Categories::create(['name' => $request->name, 'is_active' => true]);
        return redirect()->route('admin.categories.view')->with('success', 'Kategori ditambahkan.');
    }

    public function detail($id)
    {
        $category = Categories::findOrFail($id);
        $products = Product::where('category_id', $id)->get();
        return view('admin.categories.detail', compact('category', 'products'));
    }

    public function update(Request $request, $id)
    {
        $category = Categories::findOrFail($id);
        $category->update(['name' => $request->name, 'is_active' => $request->is_active]);
        return redirect()->route('admin.categories.detail', $id)->with('success', 'Kategori diperbarui.');
    }

    public function addProductView($id)
    {
        $category = Categories::findOrFail($id);
        $products = Product::whereNull('category_id')->get();
        return view('admin.categories.add-product', compact('products', 'category'));
    }

    public function addProductToCategory(Request $request, $id)
    {
        $product = Product::findOrFail($request->product_id);
        $product->category_id = $id;
        $product->save();
        return redirect()->route('admin.categories.detail', $id)->with('success', 'Produk ditambahkan ke kategori.');
    }

    public function removeProductFromCategory(Request $request, $id)
    {
        $product = Product::findOrFail($request->product_id);
        $product->category_id = null;
        $product->save();
        return redirect()->route('admin.categories.detail', $id)->with('success', 'Produk dihapus dari kategori.');
    }
    public function destroy($id)
    {
        try {
            $category = Categories::withCount('products')->findOrFail($id);
            
            if ($category->products_count > 0) {
                return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh ' . $category->products_count . ' produk.');
            }

            $category->delete();

            return redirect()->route('admin.categories.view')->with('success', 'Kategori berhasil dihapus.');
        } catch (\Exception $e) {
            \Log::error('Error deleting category', [
                'category_id' => $id,
                'error' => $e->getMessage()
            ]);
            return redirect()->back()->with('error', 'Gagal menghapus kategori: ' . $e->getMessage());
        }
    }

}
