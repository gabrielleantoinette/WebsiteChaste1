<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notification;
use App\Services\NotificationService;

class NotificationController extends Controller
{
    protected $notificationService;

    public function __construct(NotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Tampilkan semua notifikasi untuk user yang sedang login
     */
    public function index(Request $request)
    {
        $user = session('user');
        
        if (!$user) {
            return redirect('/login');
        }

        // Handle both array and object user data
        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;

        $notifications = $this->notificationService->getNotificationsForUser($recipientType, $recipientId, 50);
        $unreadCount = $this->notificationService->getUnreadCount($recipientType, $recipientId);

        return view('notifications.index', compact('notifications', 'unreadCount'));
    }

    /**
     * Tampilkan notifikasi yang belum dibaca (untuk AJAX)
     */
    public function getUnread(Request $request)
    {
        $user = session('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // Handle both array and object user data
        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;

        $notifications = $this->notificationService->getUnreadNotificationsForUser($recipientType, $recipientId);
        $unreadCount = $this->notificationService->getUnreadCount($recipientType, $recipientId);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    public function markAsRead(Request $request, $id)
    {
        $user = session('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::find($id);
        
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        // Pastikan user hanya bisa menandai notifikasinya sendiri
        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;
        if ($notification->recipient_type !== $recipientType || $notification->recipient_id !== $recipientId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $this->notificationService->markAsRead($id);

        return response()->json(['success' => true]);
    }

    /**
     * Tandai semua notifikasi sebagai dibaca
     */
    public function markAllAsRead(Request $request)
    {
        $user = session('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;

        $this->notificationService->markAllAsRead($recipientType, $recipientId);

        return response()->json(['success' => true]);
    }

    /**
     * Hapus notifikasi
     */
    public function destroy(Request $request, $id)
    {
        $user = session('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification = Notification::find($id);
        
        if (!$notification) {
            return response()->json(['error' => 'Notification not found'], 404);
        }

        // Pastikan user hanya bisa menghapus notifikasinya sendiri
        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;
        if ($notification->recipient_type !== $recipientType || $notification->recipient_id !== $recipientId) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $notification->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Hapus semua notifikasi yang sudah dibaca
     */
    public function clearRead(Request $request)
    {
        $user = session('user');
        
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;

        Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('is_read', true)
            ->delete();

        return response()->json(['success' => true]);
    }

    /**
     * Dapatkan jumlah notifikasi yang belum dibaca (untuk badge)
     */
    public function getUnreadCount(Request $request)
    {
        $user = session('user');
        
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;

        $count = $this->notificationService->getUnreadCount($recipientType, $recipientId);

        return response()->json(['count' => $count]);
    }

    /**
     * Ambil notifikasi terbaru untuk popover (AJAX)
     */
    public function latest(Request $request)
    {
        $user = session('user');
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
        $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
        $recipientId = is_array($user) ? $user['id'] : $user->id;
        $notifications = $this->notificationService->getNotificationsForUser($recipientType, $recipientId, 10);
        $unreadCount = $this->notificationService->getUnreadCount($recipientType, $recipientId);
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
}
