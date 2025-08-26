<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Customer;
use App\Services\NotificationService;

class SendPromoNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-promo {--message= : Pesan promo} {--url= : URL promo}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send promo notifications to all customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $message = $this->option('message') ?? 'Promo spesial untuk Anda! Dapatkan diskon menarik untuk pembelian terpal berkualitas.';
        $url = $this->option('url') ?? '/produk';
        
        $notificationService = app(NotificationService::class);
        $customers = Customer::all();
        
        $this->info("Sending promo notifications to {$customers->count()} customers...");
        
        $bar = $this->output->createProgressBar($customers->count());
        $bar->start();
        
        foreach ($customers as $customer) {
            $notificationService->notifyPromo($customer->id, [
                'id' => 'promo_' . time(),
                'message' => $message,
                'action_url' => $url
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Promo notifications sent successfully!');
    }
}
