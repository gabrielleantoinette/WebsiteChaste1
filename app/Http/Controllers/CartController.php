<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Product;
use App\Models\ProductVariant;

class CartController extends Controller
{
    public function view()
    {
        $user = Session::get('user');
        $cartItems = Cart::where('user_id', $user['id'])
                        ->with(['variant.product'])
                        ->get();

        return view('cart', compact('cartItems'));
    }

    public function addItem(Request $request, $id)
    {
        $user = Session::get('user');
        $quantity = $request->quantity ?? 1;
        $variantId = $request->variant_id;
        $selectedSize = $request->selected_size;
        $negotiatedPrice = $request->negotiated_price ?? null;

        $product = Product::find($id);
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }
        
        if ($negotiatedPrice && $product->min_buying_stock && $quantity < $product->min_buying_stock) {
            return redirect()->back()->with('error', "Minimal {$product->min_buying_stock} pcs untuk menggunakan harga hasil tawar.");
        }

        $variant = null;
        if ($variantId) {
            $variant = ProductVariant::where('id', $variantId)
                                   ->where('product_id', $id)
                                   ->first();
        }
        
        if (!$variant) {
            $variant = ProductVariant::where('product_id', $id)->first();
            if (!$variant) {
                $variant = new ProductVariant();
                $variant->product_id = $id;
                $variant->color = 'default';
                $variant->stock = 999;
                $variant->save();
            }
        }
        $cartExist = Cart::where('user_id', $user['id'])
                        ->where('variant_id', $variant->id)
                        ->where('selected_size', $selectedSize)
                        ->whereNull('kebutuhan_custom')
                        ->first();

        if ($cartExist) {
            $cartExist->quantity += $quantity;
            $cartExist->save();
        } else {
            $cart = new Cart();
            $cart->user_id = $user['id'];
            $cart->variant_id = $variant->id;
            $cart->selected_size = $selectedSize;
            $cart->quantity = $quantity;
            
            if ($negotiatedPrice) {
                $cart->harga_custom = $negotiatedPrice;
                $cart->kebutuhan_custom = "Hasil negosiasi - Original Qty: {$quantity} - Harga final: Rp " . number_format($negotiatedPrice, 0, ',', '.');
            }
            
            $cart->save();
        }

        $message = $negotiatedPrice 
            ? "Produk hasil negosiasi berhasil ditambahkan ke keranjang dengan harga Rp " . number_format($negotiatedPrice, 0, ',', '.')
            : "Produk berhasil ditambahkan ke keranjang.";

        return redirect()->route('keranjang')->with('success', $message);
    }

    public function addItemFromCart(Request $request)
    {
        $user = Session::get('user');
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        // Cari produk
        $product = Product::find($productId);
        if (!$product) {
            return redirect()->back()->with('error', 'Produk tidak ditemukan.');
        }

        $variant = ProductVariant::where('product_id', $productId)->first();
        if (!$variant) {
            $variant = new ProductVariant();
            $variant->product_id = $productId;
            $variant->color = 'default';
            $variant->stock = 999; // Default stock tinggi
            $variant->save();
        }

        $cartExist = Cart::where('user_id', $user['id'])
                        ->where('variant_id', $variant->id)
                        ->whereNull('kebutuhan_custom')
                        ->first();

        if ($cartExist) {
            $cartExist->quantity += $quantity;
            $cartExist->save();
        } else {
            $cart = new Cart();
            $cart->user_id = $user['id'];
            $cart->variant_id = $variant->id;
            $cart->quantity = $quantity;
            $cart->save();
        }

        return redirect()->route('keranjang')->with('success', 'Produk berhasil ditambahkan ke keranjang.');
    }

    public function addCustomItem(Request $request)
    {
        $user = Session::get('user');

        $validated = $request->validate([
            'bahan' => 'required|exists:custom_materials,id',
            'harga_custom' => 'required|numeric',
            'kebutuhan_custom' => 'nullable|string',
            'bahan_custom' => 'nullable|string',
            'ukuran_custom' => 'nullable|string',
            'warna_custom' => 'nullable|string',
            'jumlah_ring_custom' => 'nullable|string',
            'pakai_tali_custom' => 'nullable|string',
            'catatan_custom' => 'nullable|string',
            'quantity' => 'required|integer|min:1',
            'panjang' => 'required|numeric|min:0.1|max:90',
            'lebar' => 'required|numeric|min:0.1|max:90',
            'tinggi' => 'nullable|numeric|min:0|max:5',
        ]);

        // Cek stok bahan
        $material = \App\Models\CustomMaterial::find($validated['bahan']);
        if (!$material) {
            return back()->with('error', 'Bahan tidak ditemukan.');
        }
        if ($material->stock !== null && $material->stock > 0 && $validated['quantity'] > $material->stock) {
            return back()->with('error', 'Jumlah melebihi stok bahan yang tersedia (stok: ' . $material->stock . ').')->withInput();
        }

        $cart = new Cart();
        $cart->user_id = $user['id'];
        $cart->variant_id = null; // Custom items don't have a variant
        $cart->quantity = $validated['quantity'];
        $cart->harga_custom = $validated['harga_custom'];
        $cart->kebutuhan_custom = $validated['kebutuhan_custom'];
        $cart->bahan_custom = $validated['bahan_custom'] ?? null;
        $cart->ukuran_custom = $validated['ukuran_custom'];
        $cart->warna_custom = $validated['warna_custom'];
        $cart->jumlah_ring_custom = $validated['jumlah_ring_custom'];
        $cart->pakai_tali_custom = $validated['pakai_tali_custom'];
        $cart->catatan_custom = $validated['catatan_custom'];
        $cart->save();

        return redirect()->route('keranjang')->with('success', 'Custom Terpal berhasil ditambahkan ke keranjang.');
    }



    public function updateQuantity(Request $request, $id)
    {
        try {
            $user = Session::get('user');
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'User tidak terautentikasi.'
                ], 401);
            }

            $cart = Cart::where('id', $id)
                        ->where('user_id', $user['id'])
                        ->with(['variant.product'])
                        ->first();

            if (!$cart) {
                return response()->json([
                    'success' => false,
                    'message' => 'Item tidak ditemukan.'
                ], 404);
            }

            $quantity = $request->input('quantity', 1);
            
            $isNegotiated = $cart->harga_custom && $cart->kebutuhan_custom && str_contains($cart->kebutuhan_custom, 'Hasil negosiasi');
            
            if ($isNegotiated) {
                // Extract original quantity from kebutuhan_custom
                $originalQuantity = $cart->quantity;
                if (preg_match('/Original Qty: (\d+)/', $cart->kebutuhan_custom, $matches)) {
                    $originalQuantity = (int)$matches[1];
                }
                
                if ($quantity < $originalQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Quantity barang hasil negosiasi tidak dapat dikurangi. Harga negosiasi berlaku untuk quantity yang sudah disepakati.'
                    ], 400);
                }
            }
            
            if ($quantity < 1) {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantity minimal 1.'
                ], 400);
            }
            if ($cart->variant_id && $cart->variant) {
                if ($quantity > $cart->variant->stock) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $cart->variant->stock
                    ], 400);
                }
            }

            $cart->quantity = $quantity;
            $cart->save();

            $subtotal = 0;
            if ($cart->harga_custom && $cart->kebutuhan_custom && str_contains($cart->kebutuhan_custom, 'Hasil negosiasi')) {
                $subtotal = $cart->harga_custom * $quantity;
            } elseif ($cart->variant_id && $cart->variant && $cart->variant->product) {
                $selectedSize = $cart->selected_size ?? '2x3';
                $calculatedPrice = $cart->variant->product->getPriceForSize($selectedSize);
                $subtotal = $calculatedPrice * $quantity;
            } else {
                $subtotal = ($cart->harga_custom ?? 0) * $quantity;
            }

            return response()->json([
                'success' => true,
                'quantity' => $quantity,
                'subtotal' => $subtotal,
                'message' => 'Quantity berhasil diupdate.'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating cart quantity: ' . $e->getMessage(), [
                'cart_id' => $id,
                'user_id' => $user['id'] ?? null,
                'quantity' => $request->input('quantity'),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteItem($id)
    {
        $cart = Cart::find($id);
        $cart->delete();

        return redirect()->route('keranjang');
    }
}
