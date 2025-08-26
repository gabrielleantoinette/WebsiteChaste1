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
            'payment_pending' => 'fas fa-clock',
            'payment_success' => 'fas fa-check-circle',
            'payment_confirmed' => 'fas fa-check-double',
            'payment_failed' => 'fas fa-exclamation-triangle',
            'invoice_due_date' => 'fas fa-calendar-times',
            'invoice_due_today' => 'fas fa-exclamation-triangle',
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
     * Notifikasi pesanan baru untuk customer
     */
    public function notifyOrderCreated($orderId, $customerId, $orderData)
    {
        $this->sendToCustomer(
            'order_created',
            'Pesanan Berhasil Dibuat',
            "Pesanan Anda dengan ID #{$orderId} telah berhasil dibuat. Total pembayaran: Rp " . number_format($orderData['total_amount'], 0, ',', '.'),
            $customerId,
            [
                'data_type' => 'order',
                'data_id' => $orderId,
                'action_url' => "/transaksi/detail/{$orderId}",
                'priority' => 'high',
                'icon' => 'fas fa-shopping-bag'
            ]
        );
    }

    /**
     * Notifikasi pembayaran diterima untuk customer
     */
    public function notifyPaymentReceived($paymentId, $customerId, $paymentData)
    {
        $this->sendToCustomer(
            'payment_received',
            'Pembayaran Diterima',
            "Pembayaran Anda sebesar Rp " . number_format($paymentData['amount'], 0, ',', '.') . " telah diterima dan diproses.",
            $customerId,
            [
                'data_type' => 'payment',
                'data_id' => $paymentId,
                'action_url' => "/transaksi/detail/{$paymentData['order_id']}",
                'priority' => 'high',
                'icon' => 'fas fa-credit-card'
            ]
        );
    }

    /**
     * Notifikasi pembayaran pending untuk customer
     */
    public function notifyPaymentPending($invoiceId, $customerId, $invoiceData)
    {
        $this->sendToCustomer(
            'payment_pending',
            'Pembayaran Pending',
            "Pembayaran Anda sebesar Rp " . number_format($invoiceData['amount'], 0, ',', '.') . " sedang menunggu konfirmasi.",
            $customerId,
            [
                'data_type' => 'invoice',
                'data_id' => $invoiceId,
                'action_url' => "/transaksi/menunggu-pembayaran",
                'priority' => 'normal',
                'icon' => 'fas fa-clock'
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
     * Notifikasi pengiriman untuk customer
     */
    public function notifyOrderShipped($orderId, $customerId, $orderData)
    {
        $this->sendToCustomer(
            'order_shipped',
            'Pesanan Dikirim',
            "Pesanan Anda dengan ID #{$orderId} telah dikirim oleh kurir. Estimasi tiba dalam 1-3 hari kerja.",
            $customerId,
            [
                'data_type' => 'order',
                'data_id' => $orderId,
                'action_url' => "/transaksi/detail/{$orderId}",
                'priority' => 'high',
                'icon' => 'fas fa-truck'
            ]
        );
    }

    /**
     * Notifikasi pesanan diterima untuk customer
     */
    public function notifyOrderDelivered($orderId, $customerId, $orderData)
    {
        $this->sendToCustomer(
            'order_delivered',
            'Pesanan Diterima',
            "Pesanan Anda dengan ID #{$orderId} telah diterima. Silakan berikan penilaian untuk pengalaman berbelanja Anda.",
            $customerId,
            [
                'data_type' => 'order',
                'data_id' => $orderId,
                'action_url' => "/transaksi/beri-penilaian",
                'priority' => 'normal',
                'icon' => 'fas fa-check-circle'
            ]
        );
    }

    /**
     * Notifikasi retur diproses untuk customer
     */
    public function notifyReturnProcessed($returnId, $customerId, $returnData)
    {
        $this->sendToCustomer(
            'return_processed',
            'Retur Diproses',
            "Permintaan retur Anda untuk pesanan #{$returnData['order_id']} sedang diproses oleh tim kami.",
            $customerId,
            [
                'data_type' => 'return',
                'data_id' => $returnId,
                'action_url' => "/retur/{$returnId}",
                'priority' => 'normal',
                'icon' => 'fas fa-undo-alt'
            ]
        );
    }

    /**
     * Notifikasi retur disetujui untuk customer
     */
    public function notifyReturnApproved($returnId, $customerId, $returnData)
    {
        $this->sendToCustomer(
            'return_approved',
            'Retur Disetujui',
            "Permintaan retur Anda untuk pesanan #{$returnData['order_id']} telah disetujui. Tim kami akan menghubungi Anda untuk pengambilan barang.",
            $customerId,
            [
                'data_type' => 'return',
                'data_id' => $returnId,
                'action_url' => "/retur/{$returnId}",
                'priority' => 'high',
                'icon' => 'fas fa-check'
            ]
        );
    }

    /**
     * Notifikasi retur ditolak untuk customer
     */
    public function notifyReturnRejected($returnId, $customerId, $returnData)
    {
        $this->sendToCustomer(
            'return_rejected',
            'Retur Ditolak',
            "Permintaan retur Anda untuk pesanan #{$returnData['order_id']} tidak dapat diproses. Alasan: {$returnData['reason']}",
            $customerId,
            [
                'data_type' => 'return',
                'data_id' => $returnId,
                'action_url' => "/retur/{$returnId}",
                'priority' => 'high',
                'icon' => 'fas fa-times-circle'
            ]
        );
    }

    /**
     * Notifikasi hutang jatuh tempo untuk customer
     */
    public function notifyDebtDueDate($invoiceId, $customerId, $invoiceData)
    {
        $daysLeft = $invoiceData['days_left'];
        $priority = $daysLeft <= 1 ? 'urgent' : ($daysLeft <= 3 ? 'high' : 'normal');
        
        $this->sendToCustomer(
            'debt_due_date',
            'Hutang Jatuh Tempo',
            "Hutang Anda sebesar Rp " . number_format($invoiceData['remaining_amount'], 0, ',', '.') . " jatuh tempo dalam {$daysLeft} hari.",
            $customerId,
            [
                'data_type' => 'invoice',
                'data_id' => $invoiceId,
                'action_url' => "/profile/hutang",
                'priority' => $priority,
                'icon' => 'fas fa-exclamation-triangle'
            ]
        );
    }

    /**
     * Notifikasi pembayaran pending untuk keuangan
     */
    public function notifyPaymentPendingForFinance($invoiceId, $invoiceData)
    {
        $this->sendToRole(
            'payment_pending',
            'Pembayaran Pending',
            "Pembayaran pending sebesar Rp " . number_format($invoiceData['amount']) . " dari {$invoiceData['customer_name']} untuk invoice {$invoiceData['invoice_code']}",
            'keuangan',
            [
                'data_type' => 'invoice',
                'data_id' => $invoiceId,
                'action_url' => "/admin/keuangan/detail/{$invoiceId}",
                'priority' => 'high'
            ]
        );
    }

    /**
     * Notifikasi promo untuk customer
     */
    public function notifyPromo($customerId, $promoData)
    {
        $this->sendToCustomer(
            'promo',
            'Promo Spesial',
            $promoData['message'],
            $customerId,
            [
                'data_type' => 'promo',
                'data_id' => $promoData['id'],
                'action_url' => $promoData['action_url'] ?? "/produk",
                'priority' => 'normal',
                'icon' => 'fas fa-gift'
            ]
        );
    }

    /**
     * Notifikasi stok tersedia untuk customer (untuk produk yang di-wishlist)
     */
    public function notifyStockAvailable($productId, $customerId, $productData)
    {
        $this->sendToCustomer(
            'stock_available',
            'Stok Tersedia',
            "Produk {$productData['name']} yang Anda tunggu sudah tersedia kembali!",
            $customerId,
            [
                'data_type' => 'product',
                'data_id' => $productId,
                'action_url' => "/produk/{$productId}",
                'priority' => 'normal',
                'icon' => 'fas fa-box'
            ]
        );
    }

    /**
     * Notifikasi invoice jatuh tempo untuk keuangan
     */
    public function notifyInvoiceDueDate($invoiceId, $invoiceData)
    {
        $daysLeft = $invoiceData['days_left'];
        $priority = $daysLeft <= 1 ? 'urgent' : ($daysLeft <= 3 ? 'high' : 'normal');
        
        $this->sendToRole(
            'invoice_due_date',
            'Invoice Jatuh Tempo',
            "Invoice {$invoiceData['invoice_code']} dari {$invoiceData['customer_name']} jatuh tempo dalam {$daysLeft} hari. Sisa hutang: Rp " . number_format($invoiceData['remaining_amount']),
            'keuangan',
            [
                'data_type' => 'invoice',
                'data_id' => $invoiceId,
                'action_url' => "/admin/keuangan/detail/{$invoiceId}",
                'priority' => $priority
            ]
        );
    }

    /**
     * Notifikasi invoice jatuh tempo hari ini untuk keuangan
     */
    public function notifyInvoiceDueToday($invoiceId, $invoiceData)
    {
        $this->sendToRole(
            'invoice_due_today',
            'Invoice Jatuh Tempo Hari Ini',
            "Invoice {$invoiceData['invoice_code']} dari {$invoiceData['customer_name']} jatuh tempo hari ini! Sisa hutang: Rp " . number_format($invoiceData['remaining_amount']),
            'keuangan',
            [
                'data_type' => 'invoice',
                'data_id' => $invoiceId,
                'action_url' => "/admin/keuangan/detail/{$invoiceId}",
                'priority' => 'urgent'
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

    /**
     * Notifikasi work order baru untuk gudang
     */
    public function notifyWorkOrderNew($workOrderId, $workOrderData)
    {
        $this->sendToUser(
            'work_order_new',
            'Surat Perintah Kerja Baru',
            "Surat perintah kerja {$workOrderData['code']} telah dibuat untuk Anda",
            'employee',
            $workOrderData['assigned_to'],
            'gudang',
            [
                'data_type' => 'work_order',
                'data_id' => $workOrderId,
                'action_url' => "/admin/work-orders/{$workOrderId}",
                'priority' => 'high',
                'icon' => 'fas fa-clipboard-list'
            ]
        );
    }

    /**
     * Notifikasi update status work order untuk admin
     */
    public function notifyWorkOrderStatusUpdate($workOrderId, $workOrderData)
    {
        $this->sendToRole(
            'work_order_update',
            'Update Status Surat Perintah Kerja',
            "Surat perintah kerja {$workOrderData['code']} telah diupdate statusnya menjadi {$workOrderData['status']}",
            'admin',
            [
                'data_type' => 'work_order',
                'data_id' => $workOrderId,
                'action_url' => "/admin/work-orders/{$workOrderId}",
                'priority' => 'normal',
                'icon' => 'fas fa-clipboard-check'
            ]
        );
    }

    /**
     * Notifikasi pesanan siap kirim untuk driver
     */
    public function notifyOrderReadyForDelivery($orderData)
    {
        $this->sendToRole(
            'order_ready_delivery',
            'Pesanan Siap Kirim',
            "Pesanan {$orderData['code']} siap untuk dikirim ke {$orderData['customer_name']}",
            'driver',
            [
                'data_type' => 'order',
                'data_id' => $orderData['id'],
                'action_url' => "/admin/driver-transaksi/detail/{$orderData['id']}",
                'priority' => 'high',
                'icon' => 'fas fa-truck'
            ]
        );
    }

    /**
     * Notifikasi retur siap ambil untuk driver
     */
    public function notifyReturnReadyForPickup($returnData)
    {
        $this->sendToRole(
            'return_ready_pickup',
            'Retur Siap Ambil',
            "Retur #{$returnData['id']} dari {$returnData['customer_name']} siap untuk diambil",
            'driver',
            [
                'data_type' => 'return',
                'data_id' => $returnData['id'],
                'action_url' => "/admin/driver-retur/detail/{$returnData['id']}",
                'priority' => 'high',
                'icon' => 'fas fa-undo'
            ]
        );
    }
} 