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

        // Filter ukuran - filter berdasarkan size_prices atau kolom size
        if ($request->has('ukuran') && !empty($request->input('ukuran'))) {
            $ukuranFilter = $request->input('ukuran');
            $products->where(function($query) use ($ukuranFilter) {
                $first = true;
                foreach ($ukuranFilter as $ukuran) {
                    if ($first) {
                        // Filter berdasarkan kolom size jika ada
                        $query->where(function($q) use ($ukuran) {
                            $q->where('size', $ukuran)
                              ->orWhereRaw("JSON_EXTRACT(size_prices, ?) IS NOT NULL AND JSON_EXTRACT(size_prices, ?) > 0", ["$.{$ukuran}", "$.{$ukuran}"]);
                        });
                        $first = false;
                    } else {
                        // Atau filter berdasarkan size_prices JSON yang memiliki ukuran tersebut
                        $query->orWhere(function($q) use ($ukuran) {
                            $q->where('size', $ukuran)
                              ->orWhereRaw("JSON_EXTRACT(size_prices, ?) IS NOT NULL AND JSON_EXTRACT(size_prices, ?) > 0", ["$.{$ukuran}", "$.{$ukuran}"]);
                        });
                    }
                }
            });
        }

        // Filter harga - filter berdasarkan base price dan size_prices
        // Pre-filter di query builder, filter lebih akurat dilakukan setelah transform
        if ($request->filled('harga_min')) {
            $hargaMin = $request->harga_min;
            // Filter produk yang memiliki setidaknya satu harga (base price atau size_prices) >= harga_min
            // Ini akan di-filter lebih akurat setelah transform berdasarkan price range
            $products->where(function($query) use ($hargaMin) {
                // Cek base price (harga untuk ukuran 2x3)
                $query->where('price', '>=', $hargaMin);
                
                // Atau cek jika ada size_prices yang >= harga_min
                $sizes = ['2x3', '3x4', '4x6', '6x8'];
                foreach ($sizes as $size) {
                    $query->orWhereRaw("JSON_EXTRACT(size_prices, ?) >= ?", ["$.{$size}", $hargaMin]);
                }
            });
        }

        if ($request->filled('harga_max')) {
            $hargaMax = $request->harga_max;
            // Filter produk yang memiliki setidaknya satu harga (base price atau size_prices) <= harga_max
            // Ini akan di-filter lebih akurat setelah transform berdasarkan price range
            $products->where(function($query) use ($hargaMax) {
                // Cek base price (harga untuk ukuran 2x3)
                $query->where('price', '<=', $hargaMax);
                
                // Atau cek jika ada size_prices yang <= harga_max
                $sizes = ['2x3', '3x4', '4x6', '6x8'];
                foreach ($sizes as $size) {
                    $query->orWhereRaw("JSON_EXTRACT(size_prices, ?) <= ?", ["$.{$size}", $hargaMax]);
                }
            });
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

        // Add price range to each product and apply accurate price filter
        $hargaMin = $request->filled('harga_min') ? $request->harga_min : null;
        $hargaMax = $request->filled('harga_max') ? $request->harga_max : null;
        
        $products->getCollection()->transform(function ($product) use ($hargaMin, $hargaMax) {
            $sizes = ['2x3', '3x4', '4x6', '6x8'];
            
            $prices = collect($sizes)->map(function ($size) use ($product) {
                return $product->getPriceForSize($size);
            });
            
            $minPrice = $prices->min();
            $maxPrice = $prices->max();
            $product->price_range = $minPrice == $maxPrice ? number_format($minPrice, 0, ',', '.') : number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.');
            
            // Store min and max price for filtering
            $product->min_price_calculated = $minPrice;
            $product->max_price_calculated = $maxPrice;
            
            return $product;
        });
        
        // Apply accurate price filter after calculating price ranges
        if ($hargaMin !== null || $hargaMax !== null) {
            $filtered = $products->getCollection()->filter(function ($product) use ($hargaMin, $hargaMax) {
                $minPrice = $product->min_price_calculated;
                $maxPrice = $product->max_price_calculated;
                
                // Product matches if its price range overlaps with filter range
                if ($hargaMin !== null && $maxPrice < $hargaMin) {
                    return false;
                }
                if ($hargaMax !== null && $minPrice > $hargaMax) {
                    return false;
                }
                return true;
            });
            
            // Replace collection with filtered results
            $products->setCollection($filtered);
        }

        return view('produk', compact('products'));
    }


    public function detailProduct($id)
    {
        $product = Product::find($id);
        $variants = ProductVariant::where('product_id', $id)->get();

        // Build size options with prices (using getPriceForSize method)
        $sizes = ['2x3', '3x4', '4x6', '6x8'];

        $sizeOptions = collect($sizes)->map(function ($size) use ($product) {
            return [
                'size' => $size,
                'price' => $product ? $product->getPriceForSize($size) : 0,
            ];
        });

        // Calculate price range for display
        $prices = $sizeOptions->pluck('price');
        $minPrice = $prices->min();
        $maxPrice = $prices->max();
        $priceRange = $minPrice == $maxPrice ? number_format($minPrice, 0, ',', '.') : number_format($minPrice, 0, ',', '.') . ' - ' . number_format($maxPrice, 0, ',', '.');
        
        // Ambil review untuk produk ini
        $reviews = \App\Models\ProductReview::with(['user', 'order'])
                                           ->where('product_id', $id)
                                           ->approved()
                                           ->orderBy('created_at', 'desc')
                                           ->get();
        
        $averageRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();
        
        return view('produk-detail', compact('product', 'variants', 'reviews', 'averageRating', 'totalReviews', 'sizeOptions', 'priceRange'));
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
            ->with([
                'details' => function($query) {
                    $query->orderBy('id', 'asc');
                },
                'details.product',
                'details.variant'
            ])
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
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = $request->search;
                $query->where(function ($q) use ($search) {
                    // Search by invoice code
                    $q->where('code', 'like', '%' . $search . '%')
                      // Search by product name in invoice details
                      ->orWhereHas('details.product', function ($productQuery) use ($search) {
                          $productQuery->where('name', 'like', '%' . $search . '%');
                      });
                });
            })
            ->orderBy('created_at', 'desc')
            ->get();
        return view('transaction-list', compact('transactions'));
    }

    public function detailTransaction($id)
    {
        $user = Session::get('user');
        
        // Ambil transaction dengan eager loading untuk details, product, dan variant
        $transaction = HInvoice::with(['details.product', 'details.variant'])->find($id);
        
        if (!$transaction) {
            return redirect()->route('transaksi')->with('error', 'Transaksi tidak ditemukan.');
        }
        
        // Validasi bahwa invoice milik customer yang login
        if ($transaction->customer_id != $user['id']) {
            return redirect()->route('transaksi')->with('error', 'Anda tidak memiliki akses untuk melihat transaksi ini.');
        }
        
        // Ambil semua detail invoice (dinvoice) - untuk backup jika diperlukan
        $dinvoices = \App\Models\DInvoice::where('hinvoice_id', $id)
            ->with('product', 'variant')
            ->get();
        
        // Untuk review, ambil product pertama (jika ada)
        $firstDinvoice = $transaction->details->first();
        $product = null;
        $productId = null;
        
        if ($firstDinvoice && $firstDinvoice->product) {
            $productId = $firstDinvoice->product_id;
            $product = $firstDinvoice->product;
        }
        
        // Cek apakah user sudah review untuk order ini
        $hasReviewed = false;
        if ($productId && $user) {
            $hasReviewed = \App\Models\ProductReview::where('user_id', $user['id'])
                ->where('order_id', $id)
                ->where('product_id', $productId)
                ->exists();
        }
        
        return view('transaction-detail', compact('transaction', 'product', 'productId', 'hasReviewed', 'dinvoices'));
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

    public function cancelOrder(Request $request, $id)
    {
        $user = Session::get('user');
        $invoice = HInvoice::findOrFail($id);
        
        // Validasi bahwa invoice milik customer yang login
        if ($invoice->customer_id != $user['id']) {
            return redirect()->back()->with('error', 'Anda tidak memiliki akses untuk membatalkan pesanan ini.');
        }
        
        // Validasi status - hanya bisa dibatalkan jika status "Menunggu Pembayaran" atau "Dikemas"
        $allowedStatuses = ['Menunggu Pembayaran', 'Menunggu Konfirmasi Pembayaran', 'Dikemas'];
        if (!in_array($invoice->status, $allowedStatuses)) {
            return redirect()->back()->with('error', 'Pesanan dengan status "' . $invoice->status . '" tidak dapat dibatalkan. Hanya pesanan dengan status "Menunggu Pembayaran" atau "Dikemas" yang dapat dibatalkan.');
        }
        
        // Validasi waktu - maksimal 15 menit setelah pembuatan pesanan
        $createdAt = \Carbon\Carbon::parse($invoice->created_at);
        $now = \Carbon\Carbon::now();
        $minutesDiff = $createdAt->diffInMinutes($now);
        
        if ($minutesDiff > 15) {
            return redirect()->back()->with('error', 'Pesanan hanya dapat dibatalkan maksimal 15 menit setelah pembuatan. Waktu pembatalan telah habis.');
        }
        
        // Validasi input
        $request->validate([
            'cancellation_reason' => 'required|string|min:10|max:500',
        ], [
            'cancellation_reason.required' => 'Alasan pembatalan wajib diisi.',
            'cancellation_reason.min' => 'Alasan pembatalan minimal 10 karakter.',
            'cancellation_reason.max' => 'Alasan pembatalan maksimal 500 karakter.',
        ]);
        
        // Update invoice
        $invoice->status = 'Dibatalkan';
        $invoice->cancelled_at = $now;
        $invoice->cancellation_reason = $request->cancellation_reason;
        $invoice->save();
        
        // Kembalikan stok jika sudah dikurangi
        $dinvoices = \App\Models\DInvoice::where('hinvoice_id', $invoice->id)->get();
        foreach ($dinvoices as $dinvoice) {
            if ($dinvoice->variant_id) {
                $variant = \App\Models\ProductVariant::find($dinvoice->variant_id);
                if ($variant) {
                    $variant->stock += $dinvoice->quantity;
                    $variant->save();
                }
            }
        }
        
        // Kirim notifikasi ke admin
        $notificationService = app(\App\Services\NotificationService::class);
        $notificationService->sendToRole(
            'order_cancelled',
            'Pesanan Dibatalkan',
            "Pesanan {$invoice->code} telah dibatalkan oleh customer. Alasan: " . $request->cancellation_reason,
            'admin',
            [
                'data_type' => 'order',
                'data_id' => $invoice->id,
                'action_url' => "/admin/invoices/detail/{$invoice->id}",
                'priority' => 'high',
                'icon' => 'fas fa-times-circle'
            ]
        );
        
        return redirect()->route('transaksi')->with('success', 'Pesanan berhasil dibatalkan.');
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
        
        // Ambil semua invoice customer yang belum lunas
        $invoices = \App\Models\HInvoice::where('customer_id', $customerId)
            ->whereIn('status', ['Menunggu Pembayaran', 'Menunggu Konfirmasi Pembayaran'])
            ->get();
            
        // Filter hanya yang memiliki grand_total > 0 (ada hutang)
        $filtered = $invoices->filter(function($inv) {
            return $inv->grand_total > 0;
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
