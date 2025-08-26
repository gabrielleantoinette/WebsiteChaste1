<?php

namespace Tests\Feature;

use App\Models\Customer;
use App\Models\Notification;
use App\Models\HInvoice;
use App\Models\Returns;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class CustomerNotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Buat customer test
        $this->customer = Customer::create([
            'name' => 'Test Customer',
            'email' => 'test@customer.com',
            'password' => 'password123',
            'phone' => '081234567890',
            'address' => 'Jl. Test No. 123',
            'city' => 'Jakarta',
            'province' => 'DKI Jakarta',
            'postal_code' => '12345',
            'gender' => 'male'
        ]);

        $this->notificationService = app(NotificationService::class);
        
        // Disable CSRF untuk testing
        $this->withoutMiddleware(\App\Http\Middleware\VerifyCsrfToken::class);
    }

    /** @test */
    public function customer_can_see_notification_badge()
    {
        // Buat notifikasi test
        $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan Berhasil Dibuat',
            'Pesanan Anda telah berhasil dibuat',
            $this->customer->id
        );

        // Login sebagai customer
        Session::put('user', [
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
        ]);
        Session::put('isLoggedIn', true);
        Session::put('customer_id', $this->customer->id);

        // Akses halaman produk
        $response = $this->get('/produk');
        $response->assertStatus(200);

        // Cek apakah badge counter ada
        $response->assertSee('customerNotificationBadge');
    }

    /** @test */
    public function customer_can_mark_notification_as_read()
    {
        // Buat notifikasi test
        $notification = $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan Berhasil Dibuat',
            'Pesanan Anda telah berhasil dibuat',
            $this->customer->id
        );

        // Login sebagai customer
        Session::put('user', [
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
        ]);
        Session::put('isLoggedIn', true);
        Session::put('customer_id', $this->customer->id);

        // Mark as read
        $response = $this->postJson("/notifications/{$notification->id}/mark-read");
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Cek apakah notifikasi sudah ditandai sebagai dibaca
        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'is_read' => true
        ]);
    }

    /** @test */
    public function customer_can_mark_all_notifications_as_read()
    {
        // Buat beberapa notifikasi test
        $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan 1',
            'Pesanan pertama',
            $this->customer->id
        );

        $this->notificationService->sendToCustomer(
            'payment_received',
            'Pembayaran 1',
            'Pembayaran pertama',
            $this->customer->id
        );

        // Login sebagai customer
        Session::put('user', [
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
        ]);
        Session::put('isLoggedIn', true);
        Session::put('customer_id', $this->customer->id);

        // Mark all as read
        $response = $this->postJson('/notifications/mark-all-read');
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        // Cek apakah semua notifikasi sudah ditandai sebagai dibaca
        $this->assertDatabaseMissing('notifications', [
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function customer_can_get_unread_count()
    {
        // Buat beberapa notifikasi test
        $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan 1',
            'Pesanan pertama',
            $this->customer->id
        );

        $this->notificationService->sendToCustomer(
            'payment_received',
            'Pembayaran 1',
            'Pembayaran pertama',
            $this->customer->id
        );

        // Login sebagai customer
        Session::put('user', [
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
        ]);
        Session::put('isLoggedIn', true);
        Session::put('customer_id', $this->customer->id);

        // Get unread count
        $response = $this->getJson('/notifications/unread-count');
        $response->assertStatus(200);
        $response->assertJson(['count' => 2]);
    }

    /** @test */
    public function customer_can_get_latest_notifications()
    {
        // Buat beberapa notifikasi test
        $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan 1',
            'Pesanan pertama',
            $this->customer->id
        );

        $this->notificationService->sendToCustomer(
            'payment_received',
            'Pembayaran 1',
            'Pembayaran pertama',
            $this->customer->id
        );

        // Login sebagai customer
        Session::put('user', [
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
        ]);
        Session::put('isLoggedIn', true);
        Session::put('customer_id', $this->customer->id);

        // Get latest notifications
        $response = $this->getJson('/notifications/latest');
        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertArrayHasKey('notifications', $data);
        $this->assertArrayHasKey('unread_count', $data);
        $this->assertEquals(2, $data['unread_count']);
        $this->assertCount(2, $data['notifications']);
    }

    /** @test */
    public function customer_cannot_access_other_customer_notifications()
    {
        // Buat customer lain
        $otherCustomer = Customer::create([
            'name' => 'Other Customer',
            'email' => 'other@customer.com',
            'password' => 'password123',
            'phone' => '081234567891',
            'address' => 'Jl. Other No. 456',
            'city' => 'Bandung',
            'province' => 'Jawa Barat',
            'postal_code' => '54321',
            'gender' => 'female'
        ]);

        // Buat notifikasi untuk customer lain
        $notification = $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan Lain',
            'Pesanan customer lain',
            $otherCustomer->id
        );

        // Login sebagai customer pertama
        Session::put('user', [
            'id' => $this->customer->id,
            'name' => $this->customer->name,
            'email' => $this->customer->email,
        ]);
        Session::put('isLoggedIn', true);
        Session::put('customer_id', $this->customer->id);

        // Coba akses notifikasi customer lain
        $response = $this->postJson("/notifications/{$notification->id}/mark-read");
        $response->assertStatus(401);
    }

    /** @test */
    public function notification_service_can_send_order_created_notification()
    {
        $orderData = [
            'total_amount' => 500000,
            'invoice_code' => 'INV-001'
        ];

        $notification = $this->notificationService->notifyOrderCreated(1, $this->customer->id, $orderData);

        $this->assertDatabaseHas('notifications', [
            'type' => 'order_created',
            'title' => 'Pesanan Berhasil Dibuat',
            'recipient_type' => 'customer',
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function notification_service_can_send_payment_received_notification()
    {
        $paymentData = [
            'amount' => 500000,
            'order_id' => 1
        ];

        $notification = $this->notificationService->notifyPaymentReceived(1, $this->customer->id, $paymentData);

        $this->assertDatabaseHas('notifications', [
            'type' => 'payment_received',
            'title' => 'Pembayaran Diterima',
            'recipient_type' => 'customer',
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function notification_service_can_send_order_status_notification()
    {
        $orderData = [
            'invoice_code' => 'INV-001'
        ];

        $notification = $this->notificationService->notifyOrderStatus(1, $this->customer->id, 'processing', $orderData);

        $this->assertDatabaseHas('notifications', [
            'type' => 'order_status',
            'title' => 'Status Pesanan Diperbarui',
            'message' => 'Pesanan Anda sedang diproses',
            'recipient_type' => 'customer',
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function notification_service_can_send_return_notification()
    {
        $returnData = [
            'order_id' => 'INV-001'
        ];

        $notification = $this->notificationService->notifyReturnProcessed(1, $this->customer->id, $returnData);

        $this->assertDatabaseHas('notifications', [
            'type' => 'return_processed',
            'title' => 'Retur Diproses',
            'recipient_type' => 'customer',
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function notification_service_can_send_promo_notification()
    {
        $promoData = [
            'id' => 'promo_001',
            'message' => 'Promo spesial 50% off!',
            'action_url' => '/produk'
        ];

        $notification = $this->notificationService->notifyPromo($this->customer->id, $promoData);

        $this->assertDatabaseHas('notifications', [
            'type' => 'promo',
            'title' => 'Promo Spesial',
            'message' => 'Promo spesial 50% off!',
            'recipient_type' => 'customer',
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function notification_service_can_send_stock_available_notification()
    {
        $productData = [
            'name' => 'Terpal Premium',
            'stock' => 10
        ];

        $notification = $this->notificationService->notifyStockAvailable(1, $this->customer->id, $productData);

        $this->assertDatabaseHas('notifications', [
            'type' => 'stock_available',
            'title' => 'Stok Tersedia',
            'message' => 'Produk Terpal Premium yang Anda tunggu sudah tersedia kembali!',
            'recipient_type' => 'customer',
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function notification_service_can_send_debt_due_date_notification()
    {
        $invoiceData = [
            'days_left' => 3,
            'remaining_amount' => 1000000
        ];

        $notification = $this->notificationService->notifyDebtDueDate(1, $this->customer->id, $invoiceData);

        $this->assertDatabaseHas('notifications', [
            'type' => 'debt_due_date',
            'title' => 'Hutang Jatuh Tempo',
            'recipient_type' => 'customer',
            'recipient_id' => $this->customer->id,
            'is_read' => false
        ]);
    }

    /** @test */
    public function unauthenticated_user_cannot_access_notifications()
    {
        // Tidak login
        $response = $this->getJson('/notifications/unread-count');
        $response->assertStatus(200); // Return count 0 untuk user tidak login
        $response->assertJson(['count' => 0]);

        $response = $this->getJson('/notifications/latest');
        $response->assertStatus(401);

        $response = $this->postJson('/notifications/1/mark-read');
        $response->assertStatus(401);
    }

    /** @test */
    public function notification_has_correct_icon_and_priority()
    {
        $notification = $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan Berhasil Dibuat',
            'Pesanan Anda telah berhasil dibuat',
            $this->customer->id,
            [
                'icon' => 'fas fa-shopping-bag',
                'priority' => 'high'
            ]
        );

        $this->assertDatabaseHas('notifications', [
            'id' => $notification->id,
            'icon' => 'fas fa-shopping-bag',
            'priority' => 'high'
        ]);
    }

    /** @test */
    public function notification_can_be_marked_as_read_and_unread()
    {
        $notification = $this->notificationService->sendToCustomer(
            'order_created',
            'Pesanan Berhasil Dibuat',
            'Pesanan Anda telah berhasil dibuat',
            $this->customer->id
        );

        // Mark as read
        $notification->markAsRead();
        $this->assertTrue($notification->fresh()->is_read);
        $this->assertNotNull($notification->fresh()->read_at);

        // Mark as unread
        $notification->markAsUnread();
        $this->assertFalse($notification->fresh()->is_read);
        $this->assertNull($notification->fresh()->read_at);
    }
}
