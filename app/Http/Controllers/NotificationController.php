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
        try {
            $user = session('user');
            
            \Log::info('=== MARK AS READ REQUEST ===');
            \Log::info('Notification ID:', ['id' => $id]);
            \Log::info('User session:', [
                'user' => $user,
                'user_type' => gettype($user),
                'is_array' => is_array($user),
                'is_employee' => $user instanceof \App\Models\Employee,
                'user_id' => is_array($user) ? $user['id'] : $user->id,
                'user_role' => is_array($user) ? ($user['role'] ?? 'customer') : $user->role
            ]);
            
            if (!$user) {
                \Log::error('No user session found');
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $notification = Notification::find($id);
            
            if (!$notification) {
                \Log::error('Notification not found', ['id' => $id]);
                return response()->json(['error' => 'Notification not found'], 404);
            }

            // Debug: Log notification info
            \Log::info('Notification found:', [
                'notification_id' => $notification->id,
                'recipient_type' => $notification->recipient_type,
                'recipient_id' => $notification->recipient_id,
                'recipient_role' => $notification->recipient_role,
                'is_read' => $notification->is_read
            ]);

                    // Untuk admin dan keuangan, kita perlu mengecek berdasarkan role, bukan hanya recipient_id
        if ($user instanceof \App\Models\Employee && ($user->role === 'admin' || $user->role === 'keuangan')) {
            \Log::info('User is ' . $user->role . ', checking permissions');
            // Admin dan keuangan bisa menandai notifikasi untuk role mereka masing-masing
            if ($notification->recipient_type === 'employee' && $notification->recipient_role === $user->role) {
                \Log::info($user->role . ' can mark this notification as read');
                $result = $this->notificationService->markAsRead($id);
                \Log::info('Mark as read result:', ['result' => $result]);
                return response()->json(['success' => true]);
            } else {
                \Log::error($user->role . ' cannot mark this notification - wrong recipient type or role', [
                    'recipient_type' => $notification->recipient_type,
                    'recipient_role' => $notification->recipient_role
                ]);
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        }
            
            // Untuk user lain, cek berdasarkan recipient_id
            $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
            $recipientId = is_array($user) ? $user['id'] : $user->id;
            
            \Log::info('Checking user permissions:', [
                'recipient_type' => $recipientType,
                'recipient_id' => $recipientId,
                'notification_recipient_type' => $notification->recipient_type,
                'notification_recipient_id' => $notification->recipient_id
            ]);
            
            // Untuk customer, cek berdasarkan recipient_id saja
            if ($recipientType === 'customer') {
                if ($notification->recipient_type !== 'customer' || $notification->recipient_id != $recipientId) {
                    \Log::error('Customer cannot mark this notification - permission denied');
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            } else {
                // Untuk employee, cek berdasarkan recipient_id
                if ($notification->recipient_type !== $recipientType || $notification->recipient_id !== $recipientId) {
                    \Log::error('User cannot mark this notification - permission denied');
                    return response()->json(['error' => 'Unauthorized'], 401);
                }
            }

            $result = $this->notificationService->markAsRead($id);
            \Log::info('Mark as read result:', ['result' => $result]);
            
            // Debug: Log notification setelah diupdate
            $updatedNotification = Notification::find($id);
            \Log::info('Notification after update:', [
                'id' => $updatedNotification->id,
                'is_read' => $updatedNotification->is_read,
                'read_at' => $updatedNotification->read_at
            ]);
            
            return response()->json(['success' => true]);
            
        } catch (\Exception $e) {
            \Log::error('Exception in markAsRead:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
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

    public function getUnreadCount(Request $request)
    {
        $user = session('user');
        
        if (!$user) {
            return response()->json(['count' => 0]);
        }

        if ($user instanceof \App\Models\Employee && ($user->role === 'admin' || $user->role === 'keuangan')) {
            $count = Notification::where('recipient_type', 'employee')
                ->where('recipient_role', $user->role)
                ->where('is_read', false)
                ->count();
        } else {
            $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
            $recipientId = is_array($user) ? $user['id'] : $user->id;
            $count = $this->notificationService->getUnreadCount($recipientType, $recipientId);
        }

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
        
        // Untuk admin dan keuangan, ambil semua notifikasi role mereka
        if ($user instanceof \App\Models\Employee && ($user->role === 'admin' || $user->role === 'keuangan')) {
            $notifications = Notification::where('recipient_type', 'employee')
                ->where('recipient_role', $user->role)
                ->orderBy('created_at', 'desc')
                ->limit(10)
                ->get();
            $unreadCount = Notification::where('recipient_type', 'employee')
                ->where('recipient_role', $user->role)
                ->where('is_read', false)
                ->count();
        } else {
            $recipientType = is_array($user) ? 'customer' : ($user instanceof \App\Models\Employee ? 'employee' : 'customer');
            $recipientId = is_array($user) ? $user['id'] : $user->id;
            $notifications = $this->notificationService->getNotificationsForUser($recipientType, $recipientId, 10);
            $unreadCount = $this->notificationService->getUnreadCount($recipientType, $recipientId);
        }
        
        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $unreadCount
        ]);
    }
}
