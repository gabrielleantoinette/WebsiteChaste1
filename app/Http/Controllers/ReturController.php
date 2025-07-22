<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Returns;
use App\Models\HInvoice;
use App\Models\DamagedProduct;
use App\Models\DInvoice;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    // Tampilkan daftar retur
    public function index()
    {
        $returs = \App\Models\Returns::with(['invoice', 'customer', 'courier'])->orderBy('created_at', 'desc')->get();
        return view('admin.retur.index', compact('returs'));
    }

    // Tampilkan detail retur
    public function show($id)
    {
        $retur = Returns::with(['invoice', 'customer', 'courier'])->findOrFail($id);
        return view('admin.retur.detail', compact('retur'));
    }

    // Proses retur ke barang rusak
    public function process($id)
    {
        $retur = Returns::with(['invoice', 'customer'])->findOrFail($id);
        DB::beginTransaction();
        try {
            // Ambil detail produk dari invoice terkait
            $invoice = $retur->invoice;
            $dInvoices = DInvoice::where('hinvoice_id', $invoice->id)->get();
            foreach ($dInvoices as $dInv) {
                DamagedProduct::create([
                    'product_id' => $dInv->product_id,
                    'variant_id' => $dInv->variant_id,
                    'return_id' => $retur->id,
                    'quantity' => $dInv->quantity,
                    'damage_description' => $retur->description,
                    'damage_media_path' => $retur->media_path,
                    'status' => 'rusak',
                ]);
            }
            // Update status retur
            $retur->status = 'diproses';
            $retur->save();
            DB::commit();
            return redirect()->route('admin.retur.detail', $retur->id)
                ->with('success', 'Barang retur berhasil diproses ke stok barang rusak.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
} 