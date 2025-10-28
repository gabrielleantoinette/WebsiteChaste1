<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Database\Seeders\SalesSeeder;

class SeedSalesData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sales:seed {--data= : Path to sales data file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed sales data from file or use sample data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting sales data seeding...');
        
        $seeder = new SalesSeeder();
        
        // Jika ada file data yang diberikan
        if ($this->option('data')) {
            $dataFile = $this->option('data');
            if (file_exists($dataFile)) {
                $this->info("Loading sales data from: {$dataFile}");
                $salesData = include $dataFile;
                $seeder->addSalesData($salesData);
                $this->info('Sales data loaded successfully!');
            } else {
                $this->error("File not found: {$dataFile}");
                return 1;
            }
        } else {
            // Gunakan sample data
            $this->info('Using sample sales data...');
            $seeder->run();
        }
        
        $this->info('Sales data seeding completed!');
        return 0;
    }
}
