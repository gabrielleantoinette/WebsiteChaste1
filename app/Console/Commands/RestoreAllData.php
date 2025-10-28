<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\CustomerSeeder;
use Database\Seeders\SalesSeeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\CustomMaterialSeeder;

class RestoreAllData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data:restore {--force : Force restore even if data exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Restore all application data using seeders';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔄 Memulai restore data...');
        
        // Check if data already exists
        $hasData = \App\Models\Product::count() > 0 || 
                   \App\Models\Customer::count() > 0 || 
                   \App\Models\HInvoice::count() > 0;
        
        if ($hasData && !$this->option('force')) {
            if (!$this->confirm('Data sudah ada. Apakah Anda yakin ingin melanjutkan?')) {
                $this->info('❌ Restore dibatalkan.');
                return 0;
            }
        }

        try {
            // Run CustomerSeeder
            $this->info('📊 Menjalankan CustomerSeeder...');
            $this->call('db:seed', ['--class' => CustomerSeeder::class]);
            
            // Run SalesSeeder
            $this->info('💰 Menjalankan SalesSeeder...');
            $this->call('sales:seed', ['--data' => 'sales_data_20241101.php']);
            
            // Run CategorySeeder
            $this->info('📦 Menjalankan CategorySeeder...');
            $this->call('db:seed', ['--class' => CategorySeeder::class]);
            
            // Run CustomMaterialSeeder
            $this->info('🏭 Menjalankan CustomMaterialSeeder...');
            $this->call('db:seed', ['--class' => CustomMaterialSeeder::class]);
            
            // Show summary
            $this->info('✅ Data berhasil direstore!');
            $this->newLine();
            $this->info('📈 Data yang tersedia:');
            $this->line('Products: ' . \App\Models\Product::count());
            $this->line('Customers: ' . \App\Models\Customer::count());
            $this->line('Employees: ' . \App\Models\Employee::count());
            $this->line('Invoices: ' . \App\Models\HInvoice::count());
            $this->line('Categories: ' . \App\Models\Categories::count());
            
            return 0;
            
        } catch (\Exception $e) {
            $this->error('❌ Error saat restore data: ' . $e->getMessage());
            return 1;
        }
    }
}