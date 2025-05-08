<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index()
    {
        $hutang = PurchaseOrder::with('supplier')
            ->whereIn('status', ['belum_dibayar', 'sebagian_dibayar'])
            ->orderBy('due_date', 'asc')
            ->get();

        return view('admin.keuangan.hutangsupplier', compact('hutang'));
    }

    public function show($id)
    {
        $po = PurchaseOrder::with(['supplier', 'items.rawMaterial', 'payments'])->findOrFail($id);
        return view('admin.keuangan.hutangsupplier-detail', compact('po'));
    }

    public function create()
    {
        $suppliers = Supplier::all();
        return view('admin.keuangan.purchase-order-create', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'supplier_name' => 'required|string|max:255',
            'po_code' => 'required|string|max:100|unique:purchase_orders,code',
            'order_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:order_date',
            'total' => 'required|numeric|min:1',
        ]);
        
        // Cek jika supplier sudah ada
        $supplier = Supplier::firstOrCreate(['name' => $request->supplier_name]);
        
        // Simpan PO
        PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'code' => $request->po_code,
            'order_date' => $request->order_date,
            'due_date' => $request->due_date,
            'total' => $request->total,
            'status' => 'belum_dibayar',
        ]);        

        return redirect()->route('keuangan.hutang.index')->with('success', 'PO berhasil ditambahkan.');
    }
}
