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

        // Warna: bisa ditambahkan jika kamu sudah punya kolom `color` atau relasi warna

        $products = $products->paginate(9);

        return view('produk', compact('products'));
    }


    public function detailProduct($id)
    {
        $product = Product::find($id);
        $variants = ProductVariant::where('product_id', $id)->get();
        return view('produk-detail', compact('product', 'variants'));
    }

    public function viewTransaction(Request $request)
    {
        $user = Session::get('user');
    
        $transactions = HInvoice::where('customer_id', $user['id'])
            ->when($request->filled('status'), function ($query) use ($request) {
                if ($request->status === 'dikirim') {
                    $query->whereIn('status', ['dikirim', 'sampai']);
                } else {
                    $query->where('status', $request->status);
                }
            })
            ->get();
    
        return view('transaction-list', compact('transactions'));
    }

    public function detailTransaction($id)
    {
        $transaction = HInvoice::find($id);
        return view('transaction-detail', compact('transaction'));
    }

    public function viewProfile()
    {
        $user = Session::get('user');
        $customer = Customer::find($user['id']);

        $menungguPembayaranCount = HInvoice::where('customer_id', $user['id'])->where('status', 'Menunggu Pembayaran')->count();
        $dikemasCount = HInvoice::where('customer_id', $user['id'])->where('status', 'Dikemas')->count();
        $dikirimCount = HInvoice::where('customer_id', $user['id'])->whereIn('status', ['dikirim', 'sampai'])->count();
        $reviewCount = HInvoice::where('customer_id', $user['id'])->where('status', 'diterima')->count();

        return view('profile', compact('customer', 'dikemasCount', 'dikirimCount', 'reviewCount', 'menungguPembayaranCount'));
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
        $orders = HInvoice::where('status', 'dikemas')
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

        return redirect('/transaksi')->with('success', 'Retur berhasil diajukan.');
    }
}
