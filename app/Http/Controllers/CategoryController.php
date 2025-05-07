<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;
use App\Models\Product;

class CategoryController extends Controller
{
    public function view()
    {
        $categories = Categories::all();
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
        $category = Category::findOrFail($id);
        
        // Opsional: pastikan tidak ada produk yang masih pakai kategori ini
        if ($category->products()->count() > 0) {
            return redirect()->back()->with('error', 'Kategori tidak dapat dihapus karena masih digunakan oleh produk.');
        }

        $category->delete();

        return redirect()->route('admin.categories.view')->with('success', 'Kategori berhasil dihapus.');
    }

}
