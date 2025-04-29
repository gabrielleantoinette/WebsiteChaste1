<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Categories;

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
        Categories::create(['name' => $request->name, 'productId' => 0]);
        return redirect()->route('admin.categories.view')->with('success', 'Kategori ditambahkan.');
    }

    public function edit($id)
    {
        $category = Categories::findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required']);
        $category = Categories::findOrFail($id);
        $category->update(['name' => $request->name]);
        return redirect()->route('admin.categories.view')->with('success', 'Kategori diperbarui.');
    }

    public function destroy($id)
    {
        $category = Categories::findOrFail($id);
        $category->delete();
        return redirect()->route('admin.categories.view')->with('success', 'Kategori dihapus.');
    }

}
