<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
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
        if (!session()->has('isLoggedIn')) {
            return redirect()->route('login')->with('error', 'Silakan login dahulu.');
        }

        $customerId = session()->get('customer_id');

        if (!$customerId) {
            return redirect()->route('login')->with('error', 'Session habis, silakan login ulang.');
        }

        $selectedItems = $request->input('selected_items'); // Ambil dari form

        if (!$selectedItems) {
            return redirect()->route('keranjang')->with('error', 'Pilih minimal satu barang untuk checkout.');
        }

        // Ambil barang produk biasa
        $produkItems = DB::table('cart')
            ->join('product_variants', 'cart.variant_id', '=', 'product_variants.id')
            ->join('products', 'product_variants.product_id', '=', 'products.id')
            ->select(
                'cart.id',
                'cart.quantity',
                'products.name as product_name',
                'products.price as product_price',
                'product_variants.color as variant_color'
            )
            ->where('cart.user_id', $customerId)
            ->whereNull('cart.kebutuhan_custom')
            ->whereIn('cart.id', $selectedItems) // <<< Tambahkan filter ini
            ->get();

        // Ambil barang custom
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
            ->whereNotNull('kebutuhan_custom')
            ->whereIn('id', $selectedItems) // <<< Tambahkan filter ini
            ->get();

        $cartIds = [];

        // Hitung subtotal produk + custom
        $subtotalProduk = 0;

        foreach ($produkItems as $item) {
            $subtotalProduk += $item->product_price * $item->quantity;
            $cartIds[] = $item->id;
        }

        foreach ($customItems as $item) {
            $subtotalProduk += $item->harga_custom * $item->quantity;
            $cartIds[] = $item->id;
        }

        $subtotalPengiriman = 0; // default 0, berubah saat user pilih ekspedisi
        $alamat_default_user = '';
        $customer = Customer::find($customerId);
        if ($customer) {
            $alamat_default_user = trim(
                ($customer->address ? $customer->address : '') .
                ($customer->city ? ', ' . $customer->city : '') .
                ($customer->province ? ', ' . $customer->province : '') .
                ($customer->postal_code ? ' ' . $customer->postal_code : '')
            );
        }

        // Cek apakah customer sudah pernah transaksi lunas/diterima
        $bolehHutang = \App\Models\HInvoice::where('customer_id', $customerId)
            ->whereIn('status', ['lunas', 'diterima'])
            ->exists();

        // Cek limit hutang dan jatuh tempo
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
        
        // Validasi limit hutang 10 juta
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
            'totalHutangAktif' => $totalHutangAktif,
            'limitHutang' => $limitHutang,
            'melebihiLimit' => $melebihiLimit,
        ]);
    }
}
