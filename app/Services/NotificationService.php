<?php

namespace App\Services;

use App\Models\Notification;
use App\Models\Employee;
use App\Models\Customer;

class NotificationService
{
    /**
     * Kirim notifikasi ke semua user dengan role tertentu
     */
    public function sendToRole($type, $title, $message, $role, $data = [])
    {
        $employees = Employee::where('role', $role)->get();
        
        foreach ($employees as $employee) {
            $this->createNotification($type, $title, $message, 'employee', $employee->id, $role, $data);
        }
    }

    /**
     * Kirim notifikasi ke user tertentu
     */
    public function sendToUser($type, $title, $message, $recipientType, $recipientId, $recipientRole = null, $data = [])
    {
        return $this->createNotification($type, $title, $message, $recipientType, $recipientId, $recipientRole, $data);
    }

    /**
     * Kirim notifikasi ke customer
     */
    public function sendToCustomer($type, $title, $message, $customerId, $data = [])
    {
        return $this->createNotification($type, $title, $message, 'customer', $customerId, 'customer', $data);
    }

    /**
     * Buat notifikasi baru
     */
    private function createNotification($type, $title, $message, $recipientType, $recipientId, $recipientRole = null, $data = [])
    {
        $notificationData = [
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'recipient_type' => $recipientType,
            'recipient_id' => $recipientId,
            'recipient_role' => $recipientRole,
            'priority' => $data['priority'] ?? 'normal',
            'icon' => $data['icon'] ?? $this->getDefaultIcon($type),
            'action_url' => $data['action_url'] ?? null,
            'data_type' => $data['data_type'] ?? null,
            'data_id' => $data['data_id'] ?? null,
        ];

        return Notification::create($notificationData);
    }

    /**
     * Dapatkan notifikasi untuk user tertentu
     */
    public function getNotificationsForUser($recipientType, $recipientId, $limit = 20)
    {
        return Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Dapatkan notifikasi yang belum dibaca untuk user tertentu
     */
    public function getUnreadNotificationsForUser($recipientType, $recipientId)
    {
        return Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('is_read', false)
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Hitung jumlah notifikasi yang belum dibaca
     */
    public function getUnreadCount($recipientType, $recipientId)
    {
        return Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('is_read', false)
            ->count();
    }

    /**
     * Tandai notifikasi sebagai dibaca
     */
    public function markAsRead($notificationId)
    {
        $notification = Notification::find($notificationId);
        if ($notification) {
            $notification->markAsRead();
        }
        return $notification;
    }

    /**
     * Tandai semua notifikasi user sebagai dibaca
     */
    public function markAllAsRead($recipientType, $recipientId)
    {
        return Notification::where('recipient_type', $recipientType)
            ->where('recipient_id', $recipientId)
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now()
            ]);
    }

    /**
     * Hapus notifikasi lama (lebih dari 30 hari)
     */
    public function cleanOldNotifications()
    {
        return Notification::where('created_at', '<', now()->subDays(30))
            ->where('is_read', true)
            ->delete();
    }

    /**
     * Dapatkan icon default berdasarkan tipe notifikasi
     */
    private function getDefaultIcon($type)
    {
        $icons = [
            'order_new' => 'fas fa-shopping-bag',
            'order_status' => 'fas fa-shipping-fast',
            'order_created' => 'fas fa-shopping-cart',
            'payment_received' => 'fas fa-credit-card',
            'payment_success' => 'fas fa-check-circle',
            'payment_confirmed' => 'fas fa-check-double',
            'payment_failed' => 'fas fa-exclamation-triangle',
            'retur_request' => 'fas fa-undo-alt',
            'retur_approved' => 'fas fa-check-circle',
            'retur_rejected' => 'fas fa-times-circle',
            'stock_low' => 'fas fa-exclamation-circle',
            'stock_out' => 'fas fa-times-circle',
            'delivery_scheduled' => 'fas fa-calendar-alt',
            'delivery_completed' => 'fas fa-check-double',
            'system_alert' => 'fas fa-bell',
            'custom_material' => 'fas fa-palette',
            'negotiation' => 'fas fa-comments',
            'invoice_created' => 'fas fa-file-invoice',
            'invoice_paid' => 'fas fa-file-invoice-dollar',
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    // Method khusus untuk setiap jenis notifikasi

    /**
     * Notifikasi pesanan baru untuk admin
     */
    public function notifyNewOrder($orderId, $orderData)
    {
        $this->sendToRole(
            'order_new',
            'Pesanan Baru',
            "Pesanan baru dengan ID #{$orderId} telah dibuat oleh {$orderData['customer_name']}",
            'admin',
            [
                'data_type' => 'order',
                'data_id' => $orderId,
                'action_url' => "/admin/orders/{$orderId}",
                'priority' => 'high'
            ]
        );
    }

    /**
     * Notifikasi status pesanan untuk customer
     */
    public function notifyOrderStatus($orderId, $customerId, $status, $orderData)
    {
        $statusMessages = [
            'processing' => 'Pesanan Anda sedang diproses',
            'shipped' => 'Pesanan Anda telah dikirim',
            'delivered' => 'Pesanan Anda telah diterima',
            'cancelled' => 'Pesanan Anda telah dibatalkan'
        ];

        $this->sendToCustomer(
            'order_status',
            'Status Pesanan Diperbarui',
            $statusMessages[$status] ?? "Status pesanan Anda telah berubah menjadi {$status}",
            $customerId,
            [
                'data_type' => 'order',
                'data_id' => $orderId,
                'action_url' => "/orders/{$orderId}",
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Notifikasi pembayaran untuk keuangan
     */
    public function notifyPayment($paymentId, $paymentData)
    {
        $this->sendToRole(
            'payment_received',
            'Pembayaran Baru',
            "Pembayaran baru sebesar Rp " . number_format($paymentData['amount']) . " telah diterima",
            'keuangan',
            [
                'data_type' => 'payment',
                'data_id' => $paymentId,
                'action_url' => "/admin/payments/{$paymentId}",
                'priority' => 'high'
            ]
        );
    }

    /**
     * Notifikasi retur untuk gudang
     */
    public function notifyReturRequest($returId, $returData)
    {
        $this->sendToRole(
            'retur_request',
            'Permintaan Retur Baru',
            "Permintaan retur baru dari {$returData['customer_name']} untuk pesanan #{$returData['order_id']}",
            'gudang',
            [
                'data_type' => 'retur',
                'data_id' => $returId,
                'action_url' => "/admin/retur/{$returId}",
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Notifikasi stok rendah untuk gudang
     */
    public function notifyLowStock($productId, $productData)
    {
        $this->sendToRole(
            'stock_low',
            'Stok Produk Rendah',
            "Stok produk {$productData['name']} tersisa {$productData['stock']} unit",
            'gudang',
            [
                'data_type' => 'product',
                'data_id' => $productId,
                'action_url' => "/admin/products/{$productId}",
                'priority' => 'high'
            ]
        );
    }

    /**
     * Notifikasi pengiriman untuk driver
     */
    public function notifyDeliveryAssignment($deliveryId, $deliveryData)
    {
        $this->sendToUser(
            'delivery_scheduled',
            'Tugas Pengiriman Baru',
            "Anda ditugaskan untuk mengirim pesanan #{$deliveryData['order_id']} ke {$deliveryData['customer_address']}",
            'employee',
            $deliveryData['driver_id'],
            'driver',
            [
                'data_type' => 'delivery',
                'data_id' => $deliveryId,
                'action_url' => "/driver/deliveries/{$deliveryId}",
                'priority' => 'high'
            ]
        );
    }
} 