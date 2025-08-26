<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\HInvoice;

class FixInvoiceDetails extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:fix-details';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix invoice details by creating dinvoice records for invoices that don\'t have them';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to fix invoice details...');

        // Ambil semua invoice
        $invoices = HInvoice::all();
        
        foreach ($invoices as $invoice) {
            $this->info("Processing invoice: {$invoice->code} (ID: {$invoice->id})");
            
            // Cek apakah sudah ada data dinvoice
            $existingDInvoice = DB::table('dinvoice')->where('hinvoice_id', $invoice->id)->first();
            
            if (!$existingDInvoice) {
                $this->warn("No dinvoice data found for invoice {$invoice->id}. Creating...");
                
                // Coba ambil data dari cart user
                $carts = DB::table('cart')->where('user_id', $invoice->customer_id)->get();
                
                if ($carts->isEmpty()) {
                    $this->error("No cart data found for user {$invoice->customer_id}. Creating dummy data...");
                    
                    // Buat data dummy untuk invoice ini
                    DB::table('dinvoice')->insert([
                        'hinvoice_id' => $invoice->id,
                        'product_id' => 1,
                        'variant_id' => 1,
                        'price' => $invoice->grand_total,
                        'quantity' => 1,
                        'subtotal' => $invoice->grand_total,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                    
                    $this->info("Created dummy dinvoice data for invoice {$invoice->id}");
                } else {
                    foreach ($carts as $cart) {
                        if ($cart->variant_id) {
                            // Produk biasa
                            $product = DB::table('product_variants')
                                ->join('products', 'product_variants.product_id', '=', 'products.id')
                                ->where('product_variants.id', $cart->variant_id)
                                ->select('products.id as product_id', 'products.price')
                                ->first();
                            
                            if ($product) {
                                DB::table('dinvoice')->insert([
                                    'hinvoice_id' => $invoice->id,
                                    'product_id' => $product->product_id,
                                    'variant_id' => $cart->variant_id,
                                    'price' => $product->price,
                                    'quantity' => $cart->quantity,
                                    'subtotal' => $product->price * $cart->quantity,
                                    'created_at' => now(),
                                    'updated_at' => now(),
                                ]);
                                
                                $this->info("Created dinvoice for product variant {$cart->variant_id}");
                            }
                        } elseif ($cart->kebutuhan_custom) {
                            // Produk custom
                            DB::table('dinvoice')->insert([
                                'hinvoice_id' => $invoice->id,
                                'product_id' => 0,
                                'variant_id' => null,
                                'price' => $cart->harga_custom,
                                'quantity' => $cart->quantity,
                                'subtotal' => $cart->harga_custom * $cart->quantity,
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                            
                            $this->info("Created dinvoice for custom product");
                        }
                    }
                }
            } else {
                $this->info("Invoice {$invoice->id} already has dinvoice data");
            }
        }

        $this->info('Invoice details fix completed!');
        
        // Tampilkan summary
        $totalInvoices = $invoices->count();
        $totalDInvoice = DB::table('dinvoice')->count();
        
        $this->info("Total invoices: {$totalInvoices}");
        $this->info("Total dinvoice records: {$totalDInvoice}");
    }
}
