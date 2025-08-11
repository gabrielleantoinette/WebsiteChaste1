<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Notification;
use App\Models\Employee;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use App\Http\Controllers\NotificationController;

class TestMarkAsReadDirect extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test-mark-read-direct {notification_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test mark as read functionality directly';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationId = $this->argument('notification_id');
        
        $this->info("Testing mark as read for notification ID: {$notificationId}");
        
        // Cari admin
        $admin = Employee::where('role', 'admin')->first();
        if (!$admin) {
            $this->error('Tidak ada admin yang ditemukan!');
            return;
        }
        
        $this->info("Admin found: {$admin->name} (ID: {$admin->id})");
        
        // Set session untuk admin
        session(['user' => $admin]);
        
        // Cari notifikasi
        $notification = Notification::find($notificationId);
        if (!$notification) {
            $this->error("Notification ID {$notificationId} not found!");
            return;
        }
        
        $this->info("Notification found:");
        $this->info("  - ID: {$notification->id}");
        $this->info("  - Type: {$notification->type}");
        $this->info("  - Title: {$notification->title}");
        $this->info("  - Recipient Type: {$notification->recipient_type}");
        $this->info("  - Recipient ID: {$notification->recipient_id}");
        $this->info("  - Recipient Role: {$notification->recipient_role}");
        $this->info("  - Is Read: " . ($notification->is_read ? 'Yes' : 'No'));
        
        // Test mark as read menggunakan NotificationService
        $this->info("\nTesting NotificationService markAsRead...");
        $notificationService = app(NotificationService::class);
        $result = $notificationService->markAsRead($notificationId);
        
        if ($result) {
            $this->info("✓ NotificationService markAsRead successful");
        } else {
            $this->error("✗ NotificationService markAsRead failed");
        }
        
        // Refresh notification dari database
        $notification->refresh();
        $this->info("  - Is Read after markAsRead: " . ($notification->is_read ? 'Yes' : 'No'));
        $this->info("  - Read At: " . ($notification->read_at ?? 'null'));
        
        // Test menggunakan NotificationController
        $this->info("\nTesting NotificationController markAsRead...");
        $controller = app(NotificationController::class);
        $request = new Request();
        $request->merge(['id' => $notificationId]);
        
        try {
            $response = $controller->markAsRead($request, $notificationId);
            $this->info("✓ NotificationController markAsRead successful");
            $this->info("Response: " . json_encode($response->getData()));
        } catch (\Exception $e) {
            $this->error("✗ NotificationController markAsRead failed: " . $e->getMessage());
        }
        
        // Refresh notification lagi
        $notification->refresh();
        $this->info("  - Is Read after controller: " . ($notification->is_read ? 'Yes' : 'No'));
        $this->info("  - Read At: " . ($notification->read_at ?? 'null'));
        
        // Cek jumlah notifikasi yang belum dibaca
        $unreadCount = Notification::where('recipient_type', 'employee')
            ->where('recipient_role', 'admin')
            ->where('is_read', false)
            ->count();
            
        $this->info("\nRemaining unread notifications: {$unreadCount}");
        
        if ($unreadCount === 0) {
            $this->info('✓ All notifications marked as read!');
        } else {
            $this->info("✗ {$unreadCount} notifications still unread");
        }
    }
} 