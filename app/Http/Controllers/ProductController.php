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
        $name = $request->input('name');
        $description = $request->input('description');
        $image = $request->input('image');
        $live = $request->input('live');

        $product = new Product();
        $product->name = $name;
        $product->description = $description;
        $product->image = $image;
        $product->live = $live == "1";
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
        $product->live = $request->input('live');
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
        $product->variants()->create($request->all());
        return redirect('/admin/products/detail/' . $id);
    }
}
