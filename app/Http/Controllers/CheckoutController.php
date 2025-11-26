<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Cart;
use App\Models\Customer;
use App\Models\OrderModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Snap;

class CheckoutController extends Controller
{
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
        // Set midtrans configuration
        Config::$serverKey = 'SB-Mid-server-GkW-oS9nOpd2CktkXZve26qV';
        Config::$isProduction = false;
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }

    public function index(Request $request)
    {
        $user = Session::get('user');
        
        if (!$user) {
            return redirect()->route('login')->with('error', 'Silakan login dahulu.');
        }

        if (!is_array($user) || ($user['role'] ?? '') !== 'customer') {
            return redirect()->route('login')->with('error', 'Akses ditolak.');
        }

        $customerId = $user['id'];

        $selectedItems = $request->input('selected_items');
        
        \Log::info('Checkout Debug:', [
            'selected_items' => $selectedItems,
            'customer_id' => $customerId,
            'all_request' => $request->all()
        ]);

        if (!$selectedItems) {
            return redirect()->route('keranjang')->with('error', 'Pilih minimal satu barang untuk checkout.');
        }

        $produkItems = DB::table('cart')
            ->join('product_variants', 'cart.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'cart.id',
                'cart.quantity',
                'cart.harga_custom',
                'cart.kebutuhan_custom',
                'cart.selected_size',
                'products.id as product_id',
                'products.name as product_name',
                'products.price as product_price',
                'products.size_prices',
                'products.size as product_size',
                'product_variants.color as variant_color'
            )
            ->where('cart.user_id', $customerId)
            ->whereNotNull('cart.variant_id') // Ada variant = produk normal
            ->whereIn('cart.id', $selectedItems)
            ->get();
        
        // Convert to collection dan hitung harga berdasarkan ukuran
        $produkItems = $produkItems->map(function ($item) {
            $product = Product::find($item->product_id);
            if ($product) {
                $selectedSize = $item->selected_size ?? '2x3';
                $item->calculated_price = $item->harga_custom ?? $product->getPriceForSize($selectedSize);
            } else {
                $item->calculated_price = $item->product_price ?? 0;
            }
            return $item;
        });

        $customItems = DB::table('cart')
            ->select(
                'id',
                'quantity',
                'kebutuhan_custom',
                'ukuran_custom',
                'warna_custom',
                'jumlah_ring_custom',
                'pakai_tali_custom',
                'catatan_custom',
                'harga_custom'
            )
            ->where('user_id', $customerId)
            ->where(function($query) {
                $query->whereNull('variant_id')
                      ->orWhere('variant_id', 0);
            })
            ->whereIn('id', $selectedItems)
            ->get();

        \Log::info('Custom Items Query Result:', [
            'custom_items_count' => $customItems->count(),
            'custom_items' => $customItems->toArray(),
            'selected_items' => $selectedItems
        ]);

        $cartIds = [];

        $subtotalProduk = 0;

        foreach ($produkItems as $item) {
            $price = $item->calculated_price ?? $item->product_price ?? 0;
            $subtotalProduk += $price * $item->quantity;
            $cartIds[] = $item->id;
        }

        foreach ($customItems as $item) {
            $subtotalProduk += $item->harga_custom * $item->quantity;
            $cartIds[] = $item->id;
        }

        $subtotalPengiriman = 0; // default 0, berubah saat user pilih ekspedisi
        $alamat_default_user = '';
        $customer = Customer::find($customerId);
        $isFromSurabaya = false;
        if ($customer) {
            $alamat_default_user = trim(
                ($customer->address ? $customer->address : '') .
                ($customer->city ? ', ' . $customer->city : '') .
                ($customer->province ? ', ' . $customer->province : '') .
                ($customer->postal_code ? ' ' . $customer->postal_code : '')
            );
            
            $isFromSurabaya = (strtolower($customer->city ?? '') === 'surabaya' || 
                              strtolower($customer->province ?? '') === 'jawa timur' && 
                              str_contains(strtolower($customer->city ?? ''), 'surabaya'));
        }

        $bolehHutang = \App\Models\HInvoice::where('customer_id', $customerId)
            ->whereIn('status', ['lunas', 'diterima'])
            ->exists();

        // Cek apakah customer boleh menggunakan COD (minimal 1x transaksi selesai dan lunas)
        // Transaksi selesai dan lunas: status 'diterima' atau 'lunas' dengan payment is_paid = 1
        // Atau status 'diterima'/'lunas' dengan payment method COD (karena COD dibayar saat pengiriman)
        $bolehCOD = \App\Models\HInvoice::where('customer_id', $customerId)
            ->whereIn('status', ['diterima', 'lunas'])
            ->where(function($query) {
                $query->whereHas('payments', function($q) {
                    $q->where('is_paid', 1);
                })->orWhereHas('payments', function($q) {
                    $q->where('method', 'cod')->where('is_paid', 1);
                });
            })
            ->exists();

        $hutangInvoices = \App\Models\HInvoice::where('customer_id', $customerId)
            ->with(['payments' => function($q) {
                $q->where('is_paid', 0);
            }])
            ->get();
        $filteredHutang = $hutangInvoices->filter(function($inv) {
            $p = $inv->payments->first();
            return $p && in_array($p->method, ['cod', 'hutang']) && $p->is_paid == 0;
        });
        $totalHutangAktif = $filteredHutang->sum(function($inv) {
            return $inv->grand_total - ($inv->paid_amount ?? 0);
        });
        $adaHutangTerlambat = $filteredHutang->contains(function($inv) {
            $p = $inv->payments->first();
            return $p && $p->method == 'hutang' && now()->gt($inv->created_at->addMonth()) && ($inv->grand_total - ($inv->paid_amount ?? 0)) > 0;
        });
        
        $limitHutang = 10000000; // 10 juta
        $totalHutangSetelahTransaksi = $totalHutangAktif + $subtotalProduk;
        $melebihiLimit = $totalHutangSetelahTransaksi > $limitHutang;
        
        $disableCheckout = $totalHutangAktif >= $limitHutang || $adaHutangTerlambat || $melebihiLimit;

        return view('checkout', [
            'produkItems' => $produkItems,
            'customItems' => $customItems,
            'subtotalProduk' => $subtotalProduk,
            'subtotalPengiriman' => $subtotalPengiriman,
            'alamat_default_user' => $alamat_default_user,
            'disableCheckout' => $disableCheckout,
            'bolehHutang' => $bolehHutang,
            'bolehCOD' => $bolehCOD,
            'totalHutangAktif' => $totalHutangAktif,
            'limitHutang' => $limitHutang,
            'melebihiLimit' => $melebihiLimit,
            'isFromSurabaya' => $isFromSurabaya,
        ]);
    }
}
