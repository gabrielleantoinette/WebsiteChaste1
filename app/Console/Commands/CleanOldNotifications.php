<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class CleanOldNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:clean {--days=30 : Number of days to keep notifications}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clean old notifications that have been read';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $days = $this->option('days');
        $notificationService = app(NotificationService::class);
        
        $deletedCount = $notificationService->cleanOldNotifications();
        
        $this->info("Successfully cleaned {$deletedCount} old notifications (older than {$days} days).");
        
        return 0;
    }
}
