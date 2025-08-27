<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\HInvoice;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use App\Models\Returns;
use App\Services\NotificationService;
use Carbon\Carbon;


class CustomerController extends Controller
{
    public function view()
    {
        $customers = Customer::all();
        return view('admin.customers.view', compact('customers'));
    }

    public function create()
    {
        return view('admin.customers.create');
    }

    public function createCustomerAction(Request $request)
    {
        $customer = new Customer();
        $customer->name = $request->name;
        $customer->email = $request->email;
        $customer->password = $request->password;
        $customer->phone = $request->phone;
        $customer->save();
        return redirect('/admin/customers');
    }

    public function detail($id)
    {
        $customer = Customer::find($id);
        return view('admin.customers.detail', compact('customer'));
    }

    public function updateCustomerAction(Request $request, $id = null)
    {
        if (!$id) {
            $user = Session::get('user');
            $id = $user['id'];
        }
        $customer = Customer::find($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',
            'postal_code' => 'nullable|string|max:255',
            'birth_date' => 'nullable|date',
            'gender' => 'nullable|string|max:255',
            'profile_picture' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'password' => 'nullable|string|max:255',
        ]);

        $customer->fill($validated);
        if ($request->filled('password')) {
            $customer->password = $request->password; // hash jika perlu
        }
        if ($request->hasFile('profile_picture')) {
            $profile_picture = $request->file('profile_picture')->store('photos', 'public');
            $customer->profile_picture = basename($profile_picture);
        }
        $customer->save();

        // Redirect sesuai asal update
        if ($request->route()->getName() === 'profile.update') {
            return redirect()->route('profile')->with('success', 'Profil berhasil diperbarui!');
        } else {
            return redirect('/admin/customers/detail/' . $id)->with('success', 'Customer berhasil diperbarui!');
        }
    }


    // PUBLIC FUNCTIONS
    public function viewProducts()
    {
        $products = Product::all();
        return view('produk', compact('products'));
    }

    public function produk(Request $request)
    {
        $products = Product::where('live', true);

        // Search berdasarkan nama atau deskripsi produk
        if ($request->filled('search')) {
            $search = $request->search;
            $products->where(function($query) use ($search) {
                $query->where('name', 'LIKE', "%{$search}%")
                      ->orWhere('description', 'LIKE', "%{$search}%");
            });
        }

        // Filter kategori berdasarkan ID
        if ($request->has('kategori')) {
            $kategoriMap = ['plastik' => 1, 'kain' => 2, 'karet' => 3];
            $kategoriIds = collect($request->kategori)->map(fn($key) => $kategoriMap[$key] ?? null)->filter();
            $products->whereIn('category_id', $kategoriIds);
        }

        // Filter ukuran
        if ($request->has('ukuran')) {
            $products->whereIn('size', $request->input('ukuran'));
        }

        // Filter harga
        if ($request->filled('harga_min')) {
            $products->where('price', '>=', $request->harga_min);
        }

        if ($request->filled('harga_max')) {
            $products->where('price', '<=', $request->harga_max);
        }

        // Filter warna berdasarkan product variants
        if ($request->has('warna')) {
            $products->whereHas('variants', function($query) use ($request) {
                $query->whereIn('color', $request->warna);
            });
        }

        // Sorting
        $sortBy = $request->get('sort', 'name');
        $sortOrder = $request->get('order', 'asc');
        
        // Handle sorting options
        if ($request->has('sort')) {
            switch ($sortBy) {
                case 'price':
                    if ($request->get('order') == 'desc') {
                        $products->orderBy('price', 'desc');
                    } else {
                        $products->orderBy('price', 'asc');
                    }
                    break;
                case 'name':
                default:
                    if ($request->get('order') == 'desc') {
                        $products->orderBy('name', 'desc');
                    } else {
                        $products->orderBy('name', 'asc');
                    }
                    break;
            }
        } else {
            // Default sorting
            $products->orderBy('name', 'asc');
        }

        $products = $products->paginate(9)->withQueryString();

        return view('produk', compact('products'));
    }


    public function detailProduct($id)
    {
        $product = Product::find($id);
        $variants = ProductVariant::where('product_id', $id)->get();
        
        // Ambil review untuk produk ini
        $reviews = \App\Models\ProductReview::with(['user', 'order'])
                                           ->where('product_id', $id)
                                           ->approved()
                                           ->orderBy('created_at', 'desc')
                                           ->get();
        
        $averageRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();
        
        return view('produk-detail', compact('product', 'variants', 'reviews', 'averageRating', 'totalReviews'));
    }

    public function viewTransaction(Request $request)
    {
        $user = Session::get('user');
        $statusMap = [
            'menunggukonfirmasi' => 'Menunggu Konfirmasi Pembayaran',
            'dikemas' => ['dibayar', 'Dikemas', 'dikemas'], // Tambahkan semua variasi status dikemas
            'dikirim' => ['dikirim', 'sampai'],
            'diterima' => 'diterima',
            'pengembalian' => 'retur_diajukan', // Ubah dari 'pengembalian' ke 'retur_diajukan'
            'beripenilaian' => 'diterima',
        ];
        $transactions = HInvoice::where('customer_id', $user['id'])
            ->when($request->filled('status'), function ($query) use ($request, $statusMap) {
                $key = $request->status;
                if (isset($statusMap[$key])) {
                    $status = $statusMap[$key];
                    if (is_array($status)) {
                        $query->whereIn('status', $status);
                    } else {
                        $query->where('status', $status);
                    }
                }
            })
            ->get();
        return view('transaction-list', compact('transactions'));
    }

    public function detailTransaction($id)
    {
        $transaction = HInvoice::find($id);
        $user = Session::get('user');
        
        // Ambil detail produk dari dinvoice
        $dinvoice = \App\Models\DInvoice::where('hinvoice_id', $id)->first();
        $product = null;
        $productId = null;
        
        if ($dinvoice) {
            $productId = $dinvoice->product_id;
            $product = \App\Models\Product::find($productId);
        }
        
        // Cek apakah user sudah review untuk order ini
        $hasReviewed = false;
        if ($productId && $user) {
            $hasReviewed = \App\Models\ProductReview::where('user_id', $user['id'])
                ->where('order_id', $id)
                ->where('product_id', $productId)
                ->exists();
        }
        
        return view('transaction-detail', compact('transaction', 'product', 'productId', 'hasReviewed', 'dinvoice'));
    }

    public function viewProfile()
    {
        $user = Session::get('user');
        $customer = Customer::find($user['id']);

        $menungguPembayaranCount = HInvoice::where('customer_id', $user['id'])->where('status', 'Menunggu Konfirmasi Pembayaran')->count();
        $dikemasCount = HInvoice::where('customer_id', $user['id'])->whereIn('status', ['dibayar', 'Dikemas', 'dikemas'])->count();
        $dikirimCount = HInvoice::where('customer_id', $user['id'])->whereIn('status', ['dikirim', 'sampai'])->count();
        $reviewCount = HInvoice::where('customer_id', $user['id'])->where('status', 'diterima')->count();

        // Hitung total hutang dan jumlah nota belum lunas (hanya payment method hutang/cod)
        $invoices = HInvoice::where('customer_id', $user['id'])
            ->with(['payments' => function($q) {
                $q->where('is_paid', 0);
            }])
            ->get();
        $filtered = $invoices->filter(function($inv) {
            $p = $inv->payments->first();
            return $p && in_array($p->method, ['cod', 'hutang']) && $p->is_paid == 0;
        });
        $totalHutang = $filtered->sum(function($inv) {
            return $inv->grand_total - ($inv->paid_amount ?? 0);
        });
        $jumlahNotaBelumLunas = $filtered->count();

        return view('profile', compact('customer', 'dikemasCount', 'dikirimCount', 'reviewCount', 'menungguPembayaranCount', 'totalHutang', 'jumlahNotaBelumLunas'));
    }

    public function transaksiDiterima($id)
    {
        $transaction = HInvoice::find($id);
        $transaction->status = 'diterima';
        $transaction->save();
        return redirect()->back();
    }

    public function filterTransaksiByStatus($status)
    {
        $user = Session::get('user');
        $customer = Customer::find($user['id']);
        $orders = HInvoice::where('status', $status)
            ->where('customer_id', $user['id'])
            ->latest()
            ->get();

        return view('customer.transaksi.status', compact('orders', 'status'));
    }

    public function showMenungguPembayaran()
    {
        $user = Session::get('user');
        $customer = Customer::find($user['id']);
        $orders = HInvoice::where('status', 'Menunggu Pembayaran')
            ->where('customer_id', $user['id'])
            ->latest()->get();
        return view('menunggupembayaran', compact('orders'));
    }

    public function showDikemas()
    {
        $user = Session::get('user');
        $customer = Customer::find($user['id']);
        $orders = HInvoice::whereIn('status', ['dibayar', 'Dikemas', 'dikemas'])
            ->where('customer_id', $user['id'])
            ->latest()->get();
        return view('barangdikemas', compact('orders'));
    }

    public function showDikirim()
    {
        $user = Session::get('user');
        $customer = Customer::find($user['id']);
        $orders = HInvoice::whereIn('status', ['dikirim', 'sampai'])
            ->where('customer_id', $user['id'])
            ->latest()
            ->get();
        return view('barangdikirim', compact('orders'));
    }

    public function showBeriPenilaian()
    {
        $user = Session::get('user');
        $customer = Customer::find($user['id']);
        $orders = HInvoice::where('status', 'diterima')
            ->where('customer_id', $user['id'])
            ->latest()->get();
        return view('beripenilaian', compact('orders'));
    }

    public function showReturForm($id)
    {
        $transaction = HInvoice::findOrFail($id);
        return view('retur-form', compact('transaction'));
    }

    // Simpan retur
    public function submitRetur(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string',
            'media' => 'nullable|file|mimes:jpg,jpeg,png,mp4|max:10240',
        ]);

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('retur_media', 'public');
        }

        Returns::create([
            'invoice_id' => $id,
            'customer_id' => Session::get('user')['id'],
            'description' => $request->description,
            'media_path' => $mediaPath,
            'status' => 'diajukan',
        ]);

        // Update status hinvoice menjadi 'retur_diajukan' dan reset driver_id
        $invoice = HInvoice::find($id);
        if ($invoice) {
            $invoice->status = 'retur_diajukan';
            $invoice->driver_id = null; // Reset driver_id agar owner bisa assign kurir baru
            $invoice->save();
        }

        // Kirim notifikasi retur ke gudang
        $notificationService = app(NotificationService::class);
        $notificationService->notifyReturRequest(Returns::latest()->first()->id, [
            'customer_name' => Session::get('user')['name'],
            'order_id' => $invoice->code,
            'description' => $request->description
        ]);

        return redirect('/transaksi')->with('success', 'Retur berhasil diajukan.');
    }

    public function detailHutang()
    {
        $user = Session::get('user');
        $customerId = $user['id'];
        // Ambil semua invoice customer
        $invoices = \App\Models\HInvoice::where('customer_id', $customerId)
            ->with(['payments' => function($q) {
                $q->where('is_paid', 0);
            }])
            ->get();
        // Filter hanya yang payment method 'cod' atau 'hutang' dan is_paid=0, payment tidak null
        $filtered = $invoices->filter(function($inv) {
            $p = $inv->payments->first();
            return $p && in_array($p->method, ['cod', 'hutang']) && $p->is_paid == 0;
        });
        $totalHutang = $filtered->sum(function($inv) {
            return $inv->grand_total - ($inv->paid_amount ?? 0);
        });
        return view('hutang-detail', [
            'invoices' => $filtered,
            'totalHutang' => $totalHutang
        ]);
    }

    public function uploadPelunasanHutang(Request $request)
    {
        $user = Session::get('user');
        $customerId = $user['id'];
        $request->validate([
            'amount_paid' => 'required|numeric|min:1000',
            'payment_date' => 'required|date',
            'payment_proof' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'notes' => 'nullable|string',
        ]);
        $proofPath = $request->file('payment_proof')->store('debt_proofs', 'public');
        // Simpan ke debt_payments (tanpa relasi purchase_order, bisa tambahkan invoice_id jika perlu)
        \App\Models\DebtPayment::create([
            // 'purchase_order_id' => null, // jika ada relasi ke invoice, tambahkan di sini
            'payment_date' => $request->payment_date,
            'amount_paid' => $request->amount_paid,
            'notes' => $request->notes . ' | Bukti: ' . basename($proofPath),
        ]);
        return redirect()->route('profile.hutang')->with('success', 'Bukti pembayaran hutang berhasil diupload, menunggu verifikasi.');
    }

    // Method untuk mengecek status hutang customer
    public static function checkCustomerDebtStatus($customerId)
    {
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
        
        $limitHutang = 10000000; // 10 juta
        $melebihiLimit = $totalHutangAktif >= $limitHutang;
        
        $adaHutangTerlambat = $filteredHutang->contains(function($inv) {
            $p = $inv->payments->first();
            return $p && $p->method == 'hutang' && now()->gt($inv->created_at->addMonth()) && ($inv->grand_total - ($inv->paid_amount ?? 0)) > 0;
        });
        
        return [
            'totalHutangAktif' => $totalHutangAktif,
            'limitHutang' => $limitHutang,
            'melebihiLimit' => $melebihiLimit,
            'adaHutangTerlambat' => $adaHutangTerlambat,
            'sisaLimit' => $limitHutang - $totalHutangAktif,
            'disableCheckout' => $melebihiLimit || $adaHutangTerlambat
        ];
    }
}
