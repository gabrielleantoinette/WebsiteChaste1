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
        $stock = $request->input('stock');
        $price = $request->input('price');
        $image = $request->input('image');
        $live = $request->input('live');

        $isLive = true;
        if ($live == "false") {
            $isLive = false;
        }

        $product = new Product();
        $product->name = $name;
        $product->description = $description;
        $product->stock = $stock;
        $product->price = $price;
        $product->image = $image;
        $product->live = $isLive;
        $product->save();

        return redirect('/admin/products');
    }

    public function detail($id)
    {
        return view('admin.products.detail', [
            'product' => Product::find($id)
        ]);
    }

    public function updateProductAction(Request $request) {
        
    }
}
