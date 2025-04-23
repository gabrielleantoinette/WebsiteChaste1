<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function view()
    {
        return view('admin.products.view', [
            'products' => Product::all()
        ]);
    }

    public function create()
    {
        return view('admin.products.create');
    }

    public function createProductAction(Request $request)
    {
        $product = new Product();
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->image = $request->input('image');
        $product->price = $request->input('price');
        $product->size = $request->input('size');
        $product->live = $request->input('live') == "1";
        $product->save();

        return redirect('/admin/products');
    }

    public function detail($id)
    {
        $product = Product::find($id);

        return view('admin.products.detail', [
            'product' => $product,
        ]);
    }

    public function updateProductAction(Request $request, $id)
    {
        $product = Product::find($id);
        $product->name = $request->input('name');
        $product->description = $request->input('description');
        $product->image = $request->input('image');
        $product->price = $request->input('price');
        $product->size = $request->input('size');
        $product->live = $request->input('live');
        $product->save();

        return redirect('/admin/products');
    }

    public function updateMinPriceAction(Request $request, $id)
    {
        $product = Product::find($id);
        $product->min_price = $request->input('min_price');
        $product->save();

        return redirect('/admin/products');
    }

    public function createVariant($id)
    {
        return view('admin.products.create-variant', [
            'product' => Product::find($id)
        ]);
    }

    public function createVariantAction(Request $request, $id)
    {
        $product = Product::find($id);
        $product->variants()->create([
            'color' => $request->input('color'),
            'stock' => $request->input('stock'),
        ]);
        return redirect('/admin/products/detail/' . $id);
    }
}
