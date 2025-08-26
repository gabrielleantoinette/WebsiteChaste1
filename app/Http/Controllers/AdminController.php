<?php

namespace App\Http\Controllers;

use App\Models\RawMaterial;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    /**
     * Menampilkan halaman manajemen stok bahan baku
     */
    public function viewRawMaterialStock()
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $rawMaterials = RawMaterial::orderBy('name')->get();
        
        return view('admin.raw-material-stock', compact('rawMaterials'));
    }

    /**
     * Update stok bahan baku
     */
    public function updateRawMaterialStock(Request $request, $id)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'stock' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255'
        ], [
            'stock.required' => 'Stok wajib diisi',
            'stock.numeric' => 'Stok harus berupa angka',
            'stock.min' => 'Stok tidak boleh negatif'
        ]);

        $rawMaterial = RawMaterial::findOrFail($id);
        $oldStock = $rawMaterial->stock;
        $newStock = $request->stock;
        
        $rawMaterial->update([
            'stock' => $newStock
        ]);

        // Log perubahan stok
        \Log::info('Raw material stock updated', [
            'material_id' => $id,
            'material_name' => $rawMaterial->name,
            'old_stock' => $oldStock,
            'new_stock' => $newStock,
            'difference' => $newStock - $oldStock,
            'updated_by' => $user->id,
            'notes' => $request->notes
        ]);

        return back()->with('success', "Stok bahan baku '{$rawMaterial->name}' berhasil diupdate dari {$oldStock} m² menjadi {$newStock} m².");
    }

    /**
     * Tambah stok bahan baku (increment)
     */
    public function addRawMaterialStock(Request $request, $id)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'add_stock' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255'
        ], [
            'add_stock.required' => 'Jumlah penambahan stok wajib diisi',
            'add_stock.numeric' => 'Jumlah harus berupa angka',
            'add_stock.min' => 'Jumlah minimal 0.01 m²'
        ]);

        $rawMaterial = RawMaterial::findOrFail($id);
        $oldStock = $rawMaterial->stock;
        $addStock = $request->add_stock;
        $newStock = $oldStock + $addStock;
        
        $rawMaterial->update([
            'stock' => $newStock
        ]);

        // Log penambahan stok
        \Log::info('Raw material stock added', [
            'material_id' => $id,
            'material_name' => $rawMaterial->name,
            'old_stock' => $oldStock,
            'added_stock' => $addStock,
            'new_stock' => $newStock,
            'updated_by' => $user->id,
            'notes' => $request->notes
        ]);

        return back()->with('success', "Berhasil menambahkan {$addStock} m² stok bahan baku '{$rawMaterial->name}'. Stok sekarang: {$newStock} m².");
    }

    /**
     * Kurangi stok bahan baku (decrement)
     */
    public function reduceRawMaterialStock(Request $request, $id)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'reduce_stock' => 'required|numeric|min:0.01',
            'notes' => 'nullable|string|max:255'
        ], [
            'reduce_stock.required' => 'Jumlah pengurangan stok wajib diisi',
            'reduce_stock.numeric' => 'Jumlah harus berupa angka',
            'reduce_stock.min' => 'Jumlah minimal 0.01 m²'
        ]);

        $rawMaterial = RawMaterial::findOrFail($id);
        $oldStock = $rawMaterial->stock;
        $reduceStock = $request->reduce_stock;
        
        if ($reduceStock > $oldStock) {
            return back()->with('error', "Stok tidak mencukupi. Stok saat ini: {$oldStock} m², ingin mengurangi: {$reduceStock} m².");
        }
        
        $newStock = $oldStock - $reduceStock;
        
        $rawMaterial->update([
            'stock' => $newStock
        ]);

        // Log pengurangan stok
        \Log::info('Raw material stock reduced', [
            'material_id' => $id,
            'material_name' => $rawMaterial->name,
            'old_stock' => $oldStock,
            'reduced_stock' => $reduceStock,
            'new_stock' => $newStock,
            'updated_by' => $user->id,
            'notes' => $request->notes
        ]);

        return back()->with('success', "Berhasil mengurangi {$reduceStock} m² stok bahan baku '{$rawMaterial->name}'. Stok sekarang: {$newStock} m².");
    }

    /**
     * Tambah bahan baku baru
     */
    public function createRawMaterial(Request $request)
    {
        $user = Session::get('user');
        if (!$user || !in_array($user->role, ['admin', 'owner'])) {
            return redirect('/admin')->with('error', 'Akses ditolak.');
        }

        $request->validate([
            'name' => 'required|string|max:255|unique:raw_materials,name',
            'color' => 'required|string|max:50',
            'stock' => 'required|numeric|min:0',
            'notes' => 'nullable|string|max:255'
        ], [
            'name.required' => 'Nama bahan baku wajib diisi',
            'name.unique' => 'Nama bahan baku sudah ada',
            'color.required' => 'Warna wajib dipilih',
            'stock.required' => 'Stok awal wajib diisi',
            'stock.numeric' => 'Stok harus berupa angka',
            'stock.min' => 'Stok tidak boleh negatif'
        ]);

        $rawMaterial = RawMaterial::create([
            'name' => $request->name,
            'color' => $request->color,
            'stock' => $request->stock
        ]);

        // Log penambahan bahan baku baru
        \Log::info('New raw material created', [
            'material_id' => $rawMaterial->id,
            'material_name' => $rawMaterial->name,
            'color' => $rawMaterial->color,
            'initial_stock' => $rawMaterial->stock,
            'created_by' => $user->id,
            'notes' => $request->notes
        ]);

        return back()->with('success', "Bahan baku '{$rawMaterial->name}' berhasil ditambahkan dengan stok awal {$rawMaterial->stock} m².");
    }
}
