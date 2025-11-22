<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use Illuminate\Support\Str;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class HutangController extends Controller
{
    public function index(Request $request)
    {
            $query = PurchaseOrder::with('supplier');

        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        if ($request->has('search') && $request->search != '') {
            $query->where(function ($q) use ($request) {
                $q->where('code', 'like', '%' . $request->search . '%')
                ->orWhereHas('supplier', function ($q2) use ($request) {
                    $q2->where('name', 'like', '%' . $request->search . '%');
                });
            });
        }

        $hutang = $query->orderBy('created_at', 'desc')->paginate(5); // tampilkan 5 per halaman, urutkan dari yang terbaru

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
            'items' => 'required|array|min:1',
            'items.*.material_name' => 'required|string|max:255',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:1',
            'items.*.subtotal' => 'required|numeric|min:1',
        ]);
        
        // Cek jika supplier sudah ada
        $supplier = Supplier::firstOrCreate(['name' => $request->supplier_name]);
        
        // Simpan PO
        $po = PurchaseOrder::create([
            'supplier_id' => $supplier->id,
            'code' => $request->po_code,
            'order_date' => $request->order_date,
            'due_date' => $request->due_date,
            'total' => $request->total,
            'status' => 'belum_dibayar',
        ]);

        // Simpan item-item
        foreach ($request->items as $item) {
            \App\Models\PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'material_name' => $item['material_name'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'subtotal' => $item['subtotal'],
            ]);
        }

        return redirect()->route('keuangan.hutang.index')->with('success', 'PO berhasil ditambahkan.');
    }

    public function exportPDF()
    {
        $hutang = PurchaseOrder::with('supplier')
        ->orderBy('created_at', 'desc')
        ->get();

        $total = $hutang->sum('total');

        $pdf = Pdf::loadView('exports.hutangsupplier_pdf', [
            'hutang' => $hutang,
            'total' => $total,
            'tanggal' => now()->format('d M Y H:i')
        ]);

        return $pdf->download('laporan_piutang_supplier_'.now()->format('Ymd').'.pdf');
    }

    public function storePayment(Request $request, $id)
    {
        $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'payment_date' => 'required|date',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048',
            'notes' => 'nullable|string|max:500',
        ]);

        $po = PurchaseOrder::findOrFail($id);
        
        $totalPaid = $po->payments->sum('amount_paid');
        $remainingDebt = $po->total - $totalPaid;
        
        if ($request->amount_paid > $remainingDebt) {
            return back()->withErrors(['amount_paid' => 'Jumlah pembayaran tidak boleh melebihi sisa hutang']);
        }

        $proofPath = $request->file('payment_proof')->store('payment_proofs', 'public');

        $payment = \App\Models\DebtPayment::create([
            'purchase_order_id' => $po->id,
            'payment_date' => $request->payment_date,
            'amount_paid' => $request->amount_paid,
            'payment_proof' => $proofPath,
            'notes' => $request->notes,
        ]);

        $newTotalPaid = $totalPaid + $request->amount_paid;
        if ($newTotalPaid >= $po->total) {
            $po->status = 'lunas';
        } else {
            $po->status = 'sebagian_dibayar';
        }
        $po->save();

        return redirect()->route('keuangan.hutang.show', $po->id)
            ->with('success', 'Pembayaran berhasil disimpan!');
    }

}
