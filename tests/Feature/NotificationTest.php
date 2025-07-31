<?php

namespace Tests\Feature;

use App\Models\Notification;
use App\Models\Employee;
use App\Models\Customer;
use App\Services\NotificationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationTest extends TestCase
{
    use RefreshDatabase;

    protected $notificationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->notificationService = app(NotificationService::class);
    }

    /** @test */
    public function it_can_create_notification_for_employee()
    {
        $employee = Employee::factory()->create(['role' => 'admin']);
        
        $notification = $this->notificationService->sendToUser(
            'order_new',
            'Pesanan Baru',
            'Ada pesanan baru dari customer',
            'employee',
            $employee->id,
            'admin'
        );

        $this->assertDatabaseHas('notifications', [
            'type' => 'order_new',
            'title' => 'Pesanan Baru',
            'recipient_type' => 'employee',
            'recipient_id' => $employee->id,
            'recipient_role' => 'admin'
        ]);
    }

    /** @test */
    public function it_can_create_notification_for_customer()
    {
        $customer = Customer::factory()->create();
        
        $notification = $this->notificationService->sendToCustomer(
            'order_status',
            'Status Pesanan Diperbarui',
            'Pesanan Anda sedang diproses',
            $customer->id
        );

        $this->assertDatabaseHas('notifications', [
            'type' => 'order_status',
            'title' => 'Status Pesanan Diperbarui',
            'recipient_type' => 'customer',
            'recipient_id' => $customer->id,
            'recipient_role' => 'customer'
        ]);
    }

    /** @test */
    public function it_can_send_notification_to_role()
    {
        Employee::factory()->create(['role' => 'admin']);
        Employee::factory()->create(['role' => 'admin']);
        
        $this->notificationService->sendToRole(
            'payment_received',
            'Pembayaran Baru',
            'Ada pembayaran baru',
            'admin'
        );

        $this->assertDatabaseCount('notifications', 2);
        $this->assertDatabaseHas('notifications', [
            'type' => 'payment_received',
            'recipient_role' => 'admin'
        ]);
    }

    /** @test */
    public function it_can_get_unread_notifications()
    {
        $employee = Employee::factory()->create();
        
        // Create read and unread notifications
        Notification::factory()->create([
            'recipient_type' => 'employee',
            'recipient_id' => $employee->id,
            'is_read' => true
        ]);
        
        Notification::factory()->create([
            'recipient_type' => 'employee',
            'recipient_id' => $employee->id,
            'is_read' => false
        ]);

        $unreadCount = $this->notificationService->getUnreadCount('employee', $employee->id);
        
        $this->assertEquals(1, $unreadCount);
    }

    /** @test */
    public function it_can_mark_notification_as_read()
    {
        $notification = Notification::factory()->create(['is_read' => false]);
        
        $this->notificationService->markAsRead($notification->id);
        
        $notification->refresh();
        $this->assertTrue($notification->is_read);
        $this->assertNotNull($notification->read_at);
    }

    /** @test */
    public function it_can_clean_old_notifications()
    {
        // Create old read notification
        Notification::factory()->create([
            'is_read' => true,
            'created_at' => now()->subDays(31)
        ]);
        
        // Create recent unread notification
        Notification::factory()->create([
            'is_read' => false,
            'created_at' => now()->subDays(5)
        ]);

        $deletedCount = $this->notificationService->cleanOldNotifications();
        
        $this->assertEquals(1, $deletedCount);
        $this->assertDatabaseCount('notifications', 1);
    }
}
