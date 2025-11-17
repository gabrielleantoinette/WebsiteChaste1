<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class FixUploadDirectories extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:upload-dirs';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create and fix permissions for upload directories';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking and creating upload directories...');

        $directories = [
            public_path('images'),
            public_path('images/products'),
            storage_path('app/public'),
            storage_path('app/public/bukti_transfer'),
            storage_path('app/public/quality_proofs'),
            storage_path('app/public/photos'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                try {
                    File::makeDirectory($directory, 0775, true);
                    $this->info("Created directory: {$directory}");
                } catch (\Exception $e) {
                    $this->error("Failed to create directory: {$directory}");
                    $this->error("Error: " . $e->getMessage());
                }
            } else {
                $this->info("Directory exists: {$directory}");
            }

            // Set permissions
            if (File::exists($directory)) {
                try {
                    chmod($directory, 0775);
                    $this->info("Set permissions for: {$directory}");
                } catch (\Exception $e) {
                    $this->warn("Could not set permissions for: {$directory}");
                    $this->warn("Error: " . $e->getMessage());
                }
            }
        }

        // Check if storage link exists or if we can create it
        $linkPath = public_path('storage');
        $storagePath = storage_path('app/public');
        
        if (!File::exists($linkPath)) {
            // Try to create symlink if function is available
            if (function_exists('symlink')) {
                try {
                    symlink($storagePath, $linkPath);
                    $this->info('Storage link created successfully');
                } catch (\Exception $e) {
                    $this->warn('Could not create storage symlink: ' . $e->getMessage());
                    $this->info('Using route-based file serving instead (/public/storage/{path})');
                }
            } else {
                $this->warn('symlink() function is not available on this server');
                $this->info('Using route-based file serving instead (/public/storage/{path})');
                $this->info('Files in storage/app/public will be accessible via: /public/storage/{path}');
            }
        } else {
            $this->info('Storage link exists');
        }

        $this->info('');
        $this->info('Upload directories are ready!');
        $this->info('Product images: public/images/products');
        $this->info('Storage files: storage/app/public (accessible via /public/storage/{path})');
        $this->info('Done!');
        return 0;
    }
}

