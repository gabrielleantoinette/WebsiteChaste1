<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Models\Customer;
use App\Services\NotificationService;

class NotifyStockAvailable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:stock-available {product_id : ID produk yang stoknya tersedia}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send stock available notifications to customers';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $productId = $this->argument('product_id');
        $product = Product::find($productId);
        
        if (!$product) {
            $this->error("Product with ID {$productId} not found!");
            return 1;
        }
        
        if ($product->stock <= 0) {
            $this->error("Product {$product->name} is out of stock!");
            return 1;
        }
        
        $notificationService = app(NotificationService::class);
        $customers = Customer::all();
        
        $this->info("Sending stock available notifications for {$product->name} to {$customers->count()} customers...");
        
        $bar = $this->output->createProgressBar($customers->count());
        $bar->start();
        
        foreach ($customers as $customer) {
            $notificationService->notifyStockAvailable($product->id, $customer->id, [
                'name' => $product->name,
                'stock' => $product->stock
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info("Stock available notifications sent successfully for {$product->name}!");
    }
}
