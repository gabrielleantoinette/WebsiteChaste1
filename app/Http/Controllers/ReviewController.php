<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\ProductReview;
use App\Models\OrderModel;
use App\Models\Product;
use App\Models\Customer;
use App\Models\HInvoice;

class ReviewController extends Controller
{
    /**
     * Submit review untuk produk
     */
    public function submitReview(Request $request)
    {
        \Log::info('Review submit request received', $request->all());
        
        $user = Session::get('user');
        \Log::info('User from session', ['user' => $user]);
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'User tidak ditemukan']);
        }

        $request->validate([
            'order_id' => 'required|exists:hinvoice,id',
            'product_id' => 'required|exists:products,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        // Cek apakah order sudah diterima (status = 'diterima')
        $order = HInvoice::where('id', $request->order_id)
                        ->where('customer_id', $user['id'])
                        ->first();
        
        \Log::info('Order found', ['order' => $order, 'order_id' => $request->order_id, 'user_id' => $user['id']]);

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan']);
        }

        if ($order->status !== 'diterima') {
            return response()->json(['success' => false, 'message' => 'Hanya bisa review setelah barang diterima']);
        }

        // Cek apakah sudah pernah review untuk order ini
        $existingReview = ProductReview::where('user_id', $user['id'])
                                     ->where('order_id', $request->order_id)
                                     ->where('product_id', $request->product_id)
                                     ->first();

        if ($existingReview) {
            return response()->json(['success' => false, 'message' => 'Sudah pernah review untuk order ini']);
        }

        try {
            // Buat review baru
            $review = ProductReview::create([
                'user_id' => $user['id'],
                'product_id' => $request->product_id,
                'order_id' => $request->order_id,
                'rating' => $request->rating,
                'comment' => $request->comment,
                'status' => 'approved' // Auto approve untuk customer
            ]);

            \Log::info('Review created successfully', ['review_id' => $review->id]);

            return response()->json([
                'success' => true, 
                'message' => 'Review berhasil dikirim!',
                'review' => $review
            ]);
        } catch (\Exception $e) {
            \Log::error('Error creating review: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Terjadi kesalahan saat menyimpan review: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Ambil review untuk produk tertentu
     */
    public function getProductReviews($productId)
    {
        $reviews = ProductReview::with(['user', 'order'])
                               ->where('product_id', $productId)
                               ->approved()
                               ->orderBy('created_at', 'desc')
                               ->get();

        $averageRating = $reviews->avg('rating');
        $totalReviews = $reviews->count();

        return response()->json([
            'reviews' => $reviews,
            'averageRating' => round($averageRating, 1),
            'totalReviews' => $totalReviews
        ]);
    }

    /**
     * Cek apakah user sudah review order tertentu
     */
    public function checkUserReview($orderId, $productId)
    {
        $user = Session::get('user');
        if (!$user) {
            return response()->json(['hasReviewed' => false]);
        }

        $review = ProductReview::where('user_id', $user['id'])
                              ->where('order_id', $orderId)
                              ->where('product_id', $productId)
                              ->first();

        return response()->json([
            'hasReviewed' => $review ? true : false,
            'review' => $review
        ]);
    }
}
