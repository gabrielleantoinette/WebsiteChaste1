<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Employee;
use App\Services\NotificationService;

class CreateTestNotificationsKeuangan extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:create-test-keuangan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create test notifications for keuangan';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationService = app(NotificationService::class);
        
        // Cek apakah ada employee dengan role keuangan
        $keuangan = Employee::where('role', 'keuangan')->get();
        
        if ($keuangan->isEmpty()) {
            $this->error('Tidak ada employee dengan role keuangan!');
            $this->info('Silakan buat employee dengan role keuangan terlebih dahulu.');
            return;
        }
        
        $this->info('Membuat notifikasi test untuk keuangan...');
        
        // Test notifikasi pembayaran baru
        $notificationService->notifyPayment(1, [
            'amount' => 750000,
            'customer_name' => 'Customer Test',
            'invoice_code' => 'INV-20250805-TEST'
        ]);
        
        // Test notifikasi pembayaran pending
        $notificationService->notifyPaymentPending(1, [
            'amount' => 500000,
            'customer_name' => 'Customer Pending',
            'invoice_code' => 'INV-20250805-PENDING'
        ]);
        
        // Test notifikasi invoice jatuh tempo dalam 2 hari
        $notificationService->notifyInvoiceDueDate(1, [
            'invoice_code' => 'INV-20250805-DUE',
            'customer_name' => 'Customer Due',
            'days_left' => 2,
            'remaining_amount' => 1000000
        ]);
        
        // Test notifikasi invoice jatuh tempo hari ini
        $notificationService->notifyInvoiceDueToday(1, [
            'invoice_code' => 'INV-20250805-TODAY',
            'customer_name' => 'Customer Today',
            'remaining_amount' => 2500000
        ]);
        
        $this->info('Notifikasi test berhasil dibuat!');
        
        // Tampilkan info keuangan
        $this->info('Total keuangan: ' . $keuangan->count());
        foreach ($keuangan as $k) {
            $this->info("- {$k->name} (ID: {$k->id})");
        }
        
        $this->info('');
        $this->info('Jenis notifikasi yang dibuat:');
        $this->info('1. Pembayaran Baru - Rp 750,000');
        $this->info('2. Pembayaran Pending - Rp 500,000');
        $this->info('3. Invoice Jatuh Tempo - 2 hari lagi');
        $this->info('4. Invoice Jatuh Tempo Hari Ini');
    }
} 