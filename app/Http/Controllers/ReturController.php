<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Returns;
use App\Models\HInvoice;
use App\Models\DamagedProduct;
use App\Models\DInvoice;
use App\Models\Product;
use App\Services\NotificationService;
use Illuminate\Support\Facades\DB;

class ReturController extends Controller
{
    // Tampilkan daftar retur
    public function index()
    {
        $returs = \App\Models\Returns::with(['invoice', 'customer', 'driver'])->orderBy('created_at', 'desc')->get();
        return view('admin.retur.index', compact('returs'));
    }

    // Tampilkan detail retur
    public function show($id)
    {
        $retur = Returns::with(['invoice', 'customer', 'driver'])->findOrFail($id);
        return view('admin.retur.detail', compact('retur'));
    }

    // Proses retur ke barang rusak
    public function process($id)
    {
        $retur = Returns::with(['invoice', 'customer'])->findOrFail($id);
        DB::beginTransaction();
        try {
            // Debug: Log informasi retur
            \Log::info('Processing retur ID: ' . $retur->id);
            \Log::info('Invoice ID: ' . $retur->invoice_id);
            
            // Ambil detail produk dari invoice terkait
            $invoice = $retur->invoice;
            $dInvoices = DInvoice::where('hinvoice_id', $invoice->id)->get();
            
            \Log::info('Found ' . $dInvoices->count() . ' DInvoice records');
            
            // Jika tidak ada data di dinvoice, coba ambil dari cart
            if ($dInvoices->count() == 0) {
                \Log::info('No DInvoice records found, trying to get from cart');
                $cartItems = \App\Models\Cart::where('user_id', $invoice->customer_id)->get();
                \Log::info('Found ' . $cartItems->count() . ' Cart records');
                
                foreach ($cartItems as $cartItem) {
                    \Log::info('Processing Cart Item - Variant ID: ' . $cartItem->variant_id . ', Quantity: ' . $cartItem->quantity);
                    
                    // Ambil product_id dari variant jika ada
                    $productId = null;
                    if ($cartItem->variant_id) {
                        $variant = \App\Models\ProductVariant::find($cartItem->variant_id);
                        $productId = $variant ? $variant->product_id : null;
                    }
                    
                    // Handle produk biasa dan custom
                    if ($productId || $cartItem->kebutuhan_custom) {
                        $damagedProduct = DamagedProduct::create([
                            'product_id' => $productId ?? 0, // 0 untuk custom
                            'variant_id' => $cartItem->variant_id,
                            'return_id' => $retur->id,
                            'quantity' => $cartItem->quantity,
                            'damage_description' => $retur->description,
                            'damage_media_path' => $retur->media_path,
                            'status' => 'rusak',
                        ]);
                        
                        \Log::info('Created DamagedProduct ID: ' . $damagedProduct->id . ' from cart');
                    }
                }
            } else {
                // Proses dari dinvoice seperti sebelumnya
                foreach ($dInvoices as $dInv) {
                    \Log::info('Processing DInvoice - Product ID: ' . $dInv->product_id . ', Variant ID: ' . $dInv->variant_id . ', Quantity: ' . $dInv->quantity);
                    
                    $damagedProduct = DamagedProduct::create([
                        'product_id' => $dInv->product_id,
                        'variant_id' => $dInv->variant_id,
                        'return_id' => $retur->id,
                        'quantity' => $dInv->quantity,
                        'damage_description' => $retur->description,
                        'damage_media_path' => $retur->media_path,
                        'status' => 'rusak',
                    ]);
                    
                    \Log::info('Created DamagedProduct ID: ' . $damagedProduct->id);
                }
            }
            
            // Update status retur
            $retur->status = 'diproses';
            $retur->save();
            
            \Log::info('Retur status updated to: diproses');
            
            // Kirim notifikasi ke gudang tentang retur yang diproses
            $notificationService = app(NotificationService::class);
            $notificationService->notifyReturRequest($retur->id, [
                'customer_name' => $retur->customer->name,
                'order_id' => $retur->invoice->code,
                'description' => $retur->description
            ]);


            
            DB::commit();
            return redirect()->route('admin.retur.detail', $retur->id)
                ->with('success', 'Barang retur berhasil diproses ke stok barang rusak.');
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error processing retur: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    // Tampilkan daftar barang rusak
    public function damagedProducts()
    {
        $damagedProducts = DamagedProduct::with(['product', 'variant', 'retur.invoice.customer'])
            ->orderBy('created_at', 'desc')
            ->get();
        return view('admin.retur.damaged-products', compact('damagedProducts'));
    }
} 