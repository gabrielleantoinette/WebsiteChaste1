<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HInvoice;
use App\Services\NotificationService;
use Carbon\Carbon;

class CheckInvoiceDueDates extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:check-due-dates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check invoice due dates and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationService = app(NotificationService::class);
        $today = Carbon::today();
        
        $this->info('Checking invoice due dates...');
        
        // Cek invoice yang jatuh tempo hari ini
        $dueToday = HInvoice::where('due_date', $today->format('Y-m-d'))
            ->where('status', '!=', 'Lunas')
            ->get();
            
        $this->info("Found {$dueToday->count()} invoices due today");
        
        foreach ($dueToday as $invoice) {
            $remainingAmount = $invoice->grand_total - ($invoice->paid_amount ?? 0);
            
            if ($remainingAmount > 0) {
                $notificationService->notifyInvoiceDueToday($invoice->id, [
                    'invoice_code' => $invoice->code,
                    'customer_name' => $invoice->customer->name ?? 'Unknown Customer',
                    'remaining_amount' => $remainingAmount
                ]);

                // Kirim notifikasi ke customer
                $notificationService->notifyDebtDueDate($invoice->id, $invoice->customer_id, [
                    'days_left' => 0,
                    'remaining_amount' => $remainingAmount
                ]);
                
                $this->info("Sent due today notification for invoice {$invoice->code}");
            }
        }
        
        // Cek invoice yang jatuh tempo dalam 1-3 hari
        $dueSoon = HInvoice::whereBetween('due_date', [
                $today->addDays(1)->format('Y-m-d'),
                $today->addDays(3)->format('Y-m-d')
            ])
            ->where('status', '!=', 'Lunas')
            ->get();
            
        $this->info("Found {$dueSoon->count()} invoices due soon (1-3 days)");
        
        foreach ($dueSoon as $invoice) {
            $remainingAmount = $invoice->grand_total - ($invoice->paid_amount ?? 0);
            $daysLeft = Carbon::parse($invoice->due_date)->diffInDays($today);
            
            if ($remainingAmount > 0) {
                $notificationService->notifyInvoiceDueDate($invoice->id, [
                    'invoice_code' => $invoice->code,
                    'customer_name' => $invoice->customer->name ?? 'Unknown Customer',
                    'days_left' => $daysLeft,
                    'remaining_amount' => $remainingAmount
                ]);

                // Kirim notifikasi ke customer
                $notificationService->notifyDebtDueDate($invoice->id, $invoice->customer_id, [
                    'days_left' => $daysLeft,
                    'remaining_amount' => $remainingAmount
                ]);
                
                $this->info("Sent due date notification for invoice {$invoice->code} (due in {$daysLeft} days)");
            }
        }
        
        $this->info('Invoice due date check completed!');
    }
} 