<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Models\Notification;
use App\Services\NotificationService;

class CreateTestNotificationsCustomer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:test-customer {customer_id : ID customer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for customer';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $customerId = $this->argument('customer_id');
        $customer = Customer::find($customerId);
        
        if (!$customer) {
            $this->error("Customer with ID {$customerId} not found!");
            return 1;
        }
        
        $notificationService = app(NotificationService::class);
        
        $this->info("Creating test notifications for customer: {$customer->name}");
        
        // Buat beberapa notifikasi test
        $testNotifications = [
            [
                'type' => 'order_created',
                'title' => 'Pesanan Berhasil Dibuat',
                'message' => 'Pesanan Anda dengan ID #TEST001 telah berhasil dibuat. Total pembayaran: Rp 500.000',
                'icon' => 'fas fa-shopping-bag',
                'priority' => 'high'
            ],
            [
                'type' => 'payment_received',
                'title' => 'Pembayaran Diterima',
                'message' => 'Pembayaran Anda sebesar Rp 500.000 telah diterima dan diproses.',
                'icon' => 'fas fa-credit-card',
                'priority' => 'high'
            ],
            [
                'type' => 'order_status',
                'title' => 'Status Pesanan Diperbarui',
                'message' => 'Pesanan Anda sedang diproses',
                'icon' => 'fas fa-shipping-fast',
                'priority' => 'normal'
            ],
            [
                'type' => 'promo',
                'title' => 'Promo Spesial',
                'message' => 'Dapatkan diskon 20% untuk pembelian terpal berkualitas!',
                'icon' => 'fas fa-gift',
                'priority' => 'normal'
            ],
            [
                'type' => 'stock_available',
                'title' => 'Stok Tersedia',
                'message' => 'Produk Terpal Premium yang Anda tunggu sudah tersedia kembali!',
                'icon' => 'fas fa-box',
                'priority' => 'normal'
            ]
        ];
        
        foreach ($testNotifications as $index => $notification) {
            $notificationService->sendToCustomer(
                $notification['type'],
                $notification['title'],
                $notification['message'],
                $customer->id,
                [
                    'data_type' => 'test',
                    'data_id' => $index + 1,
                    'action_url' => '/produk',
                    'priority' => $notification['priority'],
                    'icon' => $notification['icon']
                ]
            );
            
            $this->info("Created notification: {$notification['title']}");
        }
        
        $this->info("Test notifications created successfully for {$customer->name}!");
        $this->info("Total notifications: " . count($testNotifications));
    }
}
