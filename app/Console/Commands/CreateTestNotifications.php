<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Services\NotificationService;

class CreateTestNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:create-test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for admin';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationService = app(NotificationService::class);
        
        // Cari admin yang ada
        $admins = Employee::where('role', 'admin')->get();
        
        if ($admins->isEmpty()) {
            $this->error('Tidak ada admin yang ditemukan!');
            return;
        }
        
        $this->info('Membuat notifikasi test untuk admin...');
        
        // Buat notifikasi pesanan baru
        $notificationService->notifyNewOrder(1, [
            'customer_name' => 'Customer Test',
            'invoice_code' => 'INV-20250805-TEST',
            'total_amount' => 500000
        ]);
        
        // Buat notifikasi pembayaran
        $notificationService->notifyPayment(1, [
            'amount' => 500000,
            'customer_name' => 'Customer Test',
            'invoice_code' => 'INV-20250805-TEST'
        ]);
        
        // Buat notifikasi retur
        $notificationService->notifyReturRequest(1, [
            'customer_name' => 'Customer Test',
            'order_id' => 'INV-20250805-TEST',
            'description' => 'Barang rusak'
        ]);
        
        // Buat notifikasi stok rendah
        $notificationService->notifyLowStock(1, [
            'name' => 'Terpal Test',
            'stock' => 5,
            'min_stock' => 10
        ]);
        
        $this->info('Notifikasi test berhasil dibuat!');
        $this->info('Total admin: ' . $admins->count());
        
        foreach ($admins as $admin) {
            $this->line("- {$admin->name} (ID: {$admin->id})");
        }
    }
} 