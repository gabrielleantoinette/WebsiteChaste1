<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Employee;
use App\Services\NotificationService;

class TestMarkAsRead extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test-mark-read';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mark as read functionality';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Testing mark as read functionality...');
        
        // Cari admin
        $admin = Employee::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('Tidak ada admin yang ditemukan!');
            return;
        }
        
        $this->info("Admin found: {$admin->name} (ID: {$admin->id})");
        
        // Cari notifikasi yang belum dibaca untuk admin
        $unreadNotifications = Notification::where('recipient_type', 'employee')
            ->where('recipient_role', 'admin')
            ->where('is_read', false)
            ->get();
            
        $this->info("Found {$unreadNotifications->count()} unread notifications for admin");
        
        if ($unreadNotifications->isEmpty()) {
            $this->info('No unread notifications found. Creating test notifications...');
            
            // Buat notifikasi test
            $notificationService = app(NotificationService::class);
            $notificationService->notifyNewOrder(999, [
                'customer_name' => 'Test Customer',
                'invoice_code' => 'INV-TEST-999',
                'total_amount' => 100000
            ]);
            
            $unreadNotifications = Notification::where('recipient_type', 'employee')
                ->where('recipient_role', 'admin')
                ->where('is_read', false)
                ->get();
        }
        
        foreach ($unreadNotifications as $notification) {
            $this->info("Testing notification ID: {$notification->id}");
            $this->info("  - Type: {$notification->type}");
            $this->info("  - Title: {$notification->title}");
            $this->info("  - Is Read: " . ($notification->is_read ? 'Yes' : 'No'));
            
            // Test mark as read
            $notificationService = app(NotificationService::class);
            $result = $notificationService->markAsRead($notification->id);
            
            if ($result) {
                $this->info("  ✓ Successfully marked as read");
            } else {
                $this->error("  ✗ Failed to mark as read");
            }
        }
        
        // Cek hasil
        $remainingUnread = Notification::where('recipient_type', 'employee')
            ->where('recipient_role', 'admin')
            ->where('is_read', false)
            ->count();
            
        $this->info("Remaining unread notifications: {$remainingUnread}");
        
        if ($remainingUnread === 0) {
            $this->info('✓ All notifications successfully marked as read!');
        } else {
            $this->error("✗ {$remainingUnread} notifications still unread");
        }
    }
} 