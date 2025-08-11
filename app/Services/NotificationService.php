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
            'sales_report' => 'fas fa-chart-line',
            'financial_report' => 'fas fa-chart-pie',
            'admin_action' => 'fas fa-user-cog',
            'finance_action' => 'fas fa-calculator',
            'warehouse_action' => 'fas fa-warehouse',
            'driver_action' => 'fas fa-truck',
            'customer_action' => 'fas fa-user',
        ];

        return $icons[$type] ?? 'fas fa-bell';
    }

    // Method khusus untuk setiap jenis notifikasi

    /**
     * Notifikasi pesanan baru untuk admin dan owner
     */
    public function notifyNewOrder($orderId, $orderData)
    {
        // Kirim ke admin
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

        // Kirim ke owner
        $this->sendToRole(
            'order_new',
            'Pesanan Baru',
            "Pesanan baru dengan ID #{$orderId} telah dibuat oleh {$orderData['customer_name']}",
            'owner',
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
     * Notifikasi pembayaran untuk keuangan dan admin
     */
    public function notifyPayment($paymentId, $paymentData)
    {
        // Kirim ke keuangan
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

        // Kirim ke admin
        $this->sendToRole(
            'payment_received',
            'Pembayaran Baru',
            "Pembayaran baru sebesar Rp " . number_format($paymentData['amount']) . " telah diterima",
            'admin',
            [
                'data_type' => 'payment',
                'data_id' => $paymentId,
                'action_url' => "/admin/payments/{$paymentId}",
                'priority' => 'high'
            ]
        );
    }

    /**
     * Notifikasi retur untuk gudang, admin, dan owner
     */
    public function notifyReturRequest($returId, $returData)
    {
        // Kirim ke gudang
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

        // Kirim ke admin
        $this->sendToRole(
            'retur_request',
            'Permintaan Retur Baru',
            "Permintaan retur baru dari {$returData['customer_name']} untuk pesanan #{$returData['order_id']}",
            'admin',
            [
                'data_type' => 'retur',
                'data_id' => $returId,
                'action_url' => "/admin/retur/{$returId}",
                'priority' => 'normal'
            ]
        );

        // Kirim ke owner
        $this->sendToRole(
            'retur_request',
            'Permintaan Retur Baru',
            "Permintaan retur baru dari {$returData['customer_name']} untuk pesanan #{$returData['order_id']}",
            'owner',
            [
                'data_type' => 'retur',
                'data_id' => $returId,
                'action_url' => "/admin/retur/{$returId}",
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Notifikasi stok rendah untuk gudang, admin, dan owner
     */
    public function notifyLowStock($productId, $productData)
    {
        // Kirim ke gudang
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

        // Kirim ke admin
        $this->sendToRole(
            'stock_low',
            'Stok Produk Rendah',
            "Stok produk {$productData['name']} tersisa {$productData['stock']} unit",
            'admin',
            [
                'data_type' => 'product',
                'data_id' => $productId,
                'action_url' => "/admin/products/{$productId}",
                'priority' => 'high'
            ]
        );

        // Kirim ke owner
        $this->sendToRole(
            'stock_low',
            'Stok Produk Rendah',
            "Stok produk {$productData['name']} tersisa {$productData['stock']} unit",
            'owner',
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

    /**
     * Notifikasi laporan penjualan untuk owner
     */
    public function notifySalesReport($reportData)
    {
        $this->sendToRole(
            'sales_report',
            'Laporan Penjualan',
            "Laporan penjualan periode {$reportData['period']} telah selesai. Total penjualan: Rp " . number_format($reportData['total_sales']),
            'owner',
            [
                'data_type' => 'report',
                'data_id' => $reportData['report_id'],
                'action_url' => "/admin/reports/sales/{$reportData['report_id']}",
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Notifikasi laporan keuangan untuk owner
     */
    public function notifyFinancialReport($reportData)
    {
        $this->sendToRole(
            'financial_report',
            'Laporan Keuangan',
            "Laporan keuangan periode {$reportData['period']} telah selesai. Profit: Rp " . number_format($reportData['profit']),
            'owner',
            [
                'data_type' => 'report',
                'data_id' => $reportData['report_id'],
                'action_url' => "/admin/reports/financial/{$reportData['report_id']}",
                'priority' => 'normal'
            ]
        );
    }

    /**
     * Notifikasi sistem untuk owner
     */
    public function notifySystemAlert($alertData)
    {
        $this->sendToRole(
            'system_alert',
            'Peringatan Sistem',
            $alertData['message'],
            'owner',
            [
                'data_type' => 'system',
                'data_id' => $alertData['alert_id'],
                'action_url' => $alertData['action_url'] ?? null,
                'priority' => $alertData['priority'] ?? 'normal'
            ]
        );
    }

    /**
     * Notifikasi ketika admin melakukan action
     */
    public function notifyAdminAction($actionData)
    {
        $this->sendToRole(
            'admin_action',
            'Aktivitas Admin',
            $actionData['message'],
            'owner',
            [
                'data_type' => 'admin_action',
                'data_id' => $actionData['action_id'],
                'action_url' => $actionData['action_url'] ?? null,
                'priority' => $actionData['priority'] ?? 'normal'
            ]
        );
    }

    /**
     * Notifikasi ketika keuangan melakukan action
     */
    public function notifyFinanceAction($actionData)
    {
        $this->sendToRole(
            'finance_action',
            'Aktivitas Keuangan',
            $actionData['message'],
            'owner',
            [
                'data_type' => 'finance_action',
                'data_id' => $actionData['action_id'],
                'action_url' => $actionData['action_url'] ?? null,
                'priority' => $actionData['priority'] ?? 'normal'
            ]
        );
    }

    /**
     * Notifikasi ketika gudang melakukan action
     */
    public function notifyWarehouseAction($actionData)
    {
        $this->sendToRole(
            'warehouse_action',
            'Aktivitas Gudang',
            $actionData['message'],
            'owner',
            [
                'data_type' => 'warehouse_action',
                'data_id' => $actionData['action_id'],
                'action_url' => $actionData['action_url'] ?? null,
                'priority' => $actionData['priority'] ?? 'normal'
            ]
        );
    }

    /**
     * Notifikasi ketika driver melakukan action
     */
    public function notifyDriverAction($actionData)
    {
        $this->sendToRole(
            'driver_action',
            'Aktivitas Driver',
            $actionData['message'],
            'owner',
            [
                'data_type' => 'driver_action',
                'data_id' => $actionData['action_id'],
                'action_url' => $actionData['action_url'] ?? null,
                'priority' => $actionData['priority'] ?? 'normal'
            ]
        );
    }

    /**
     * Notifikasi ketika customer melakukan action
     */
    public function notifyCustomerAction($actionData)
    {
        $this->sendToRole(
            'customer_action',
            'Aktivitas Customer',
            $actionData['message'],
            'owner',
            [
                'data_type' => 'customer_action',
                'data_id' => $actionData['action_id'],
                'action_url' => $actionData['action_url'] ?? null,
                'priority' => $actionData['priority'] ?? 'normal'
            ]
        );
    }
} 