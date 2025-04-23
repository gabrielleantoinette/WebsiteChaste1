<?php

namespace App\Http\Controllers;

use App\Models\HInvoice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class GudangController extends Controller
{
    public function viewTransaksiGudang()
    {
        $invoices = HInvoice::where('status', 'dikemas')->whereNull('gudang_id')->get();
        return view('admin.gudang-transaksi.view', compact('invoices'));
    }

    public function detailTransaksiGudang($id)
    {
        $invoice = HInvoice::find($id);
        return view('admin.gudang-transaksi.detail', compact('invoice'));
    }

    public function assignGudang($id)
    {
        $invoice = HInvoice::find($id);
        $user = Session::get('user');
        $invoice->gudang_id = $user->id;
        $invoice->save();

        return redirect()->back()->with('success', 'Berhasil menyiapkan barang');
    }
}
