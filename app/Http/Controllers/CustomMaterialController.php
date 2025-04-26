<?php

namespace App\Http\Controllers;

use App\Models\CustomMaterial;
use App\Models\CustomMaterialVariant;
use Illuminate\Http\Request;

class CustomMaterialController extends Controller
{
    public function view()
    {
        $customMaterials = CustomMaterial::all();
        return view('admin.custom-materials.view', compact('customMaterials'));
    }

    public function create()
    {
        return view('admin.custom-materials.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'color' => 'nullable|string',
        ]);

        CustomMaterial::create([
            'name'  => $request->name,
            'price' => $request->price,// boleh kosong
        ]);

        return redirect('/admin/custom-materials')->with('success', 'Bahan berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $material = CustomMaterial::with('variants')->findOrFail($id);
        return view('admin.custom-materials.edit', compact('material'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name'  => 'required|string',
            'price' => 'required|numeric',
            'stock' => 'required|integer',
            'color' => 'nullable|string',
        ]);

        $material = CustomMaterial::findOrFail($id);

        $material->update([
            'name'  => $request->name,
            'price' => $request->price,
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return redirect('/admin/custom-materials')->with('success', 'Bahan berhasil diupdate.');
    }

    // --- VARIAN (Warna per Bahan) ---

    public function createVariant($id)
    {
        $material = CustomMaterial::findOrFail($id);
        return view('admin.custom-materials.variants.create', compact('material'));
    }

    public function createVariantAction(Request $request, $id)
    {
        $request->validate([
            'color' => 'required|string',
            'stock' => 'required|integer',
        ]);

        CustomMaterialVariant::create([
            'custom_material_id' => $id,
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return redirect()->back()->with('success', 'Warna berhasil ditambahkan.');
    }

    public function editVariant($id)
    {
        $variant = CustomMaterialVariant::findOrFail($id);
        return view('admin.custom-materials.variants.edit', compact('variant'));
    }

    public function updateVariant(Request $request, $id)
    {
        $request->validate([
            'color' => 'required|string',
            'stock' => 'required|integer',
        ]);

        $variant = CustomMaterialVariant::findOrFail($id);

        $variant->update([
            'color' => $request->color,
            'stock' => $request->stock,
        ]);

        return redirect('/admin/custom-materials/detail/' . $variant->custom_material_id)->with('success', 'Varian berhasil diupdate.');
    }
}
