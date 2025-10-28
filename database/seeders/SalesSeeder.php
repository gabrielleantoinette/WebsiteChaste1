<?php

namespace Database\Seeders;

use App\Models\HInvoice;
use App\Models\DInvoice;
use App\Models\PaymentModel;
use App\Models\Customer;
use App\Models\Employee;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pastikan ada data customer, employee, product, dan variant yang diperlukan
        $this->ensureRequiredData();
        
        // Contoh data penjualan - silakan ganti dengan data yang sebenarnya
        $this->createSampleSales();
    }

    private function ensureRequiredData()
    {
        // Pastikan ada customer
        if (!Customer::where('email', 'customer@gmail.com')->exists()) {
            Customer::create([
                'name' => 'Customer',
                'email' => 'customer@gmail.com',
                'phone' => '081234567890',
                'password' => '123',
                'address' => 'Jl. Admin',
            ]);
        }

        // Pastikan ada employee admin
        if (!Employee::where('email', 'admin@gmail.com')->exists()) {
            Employee::create([
                'name' => 'Admin',
                'email' => 'admin@gmail.com',
                'password' => '123',
                'role' => 'admin',
            ]);
        }

        // Pastikan ada driver
        if (!Employee::where('email', 'driver@gmail.com')->exists()) {
            Employee::create([
                'name' => 'Driver',
                'email' => 'driver@gmail.com',
                'password' => '123',
                'role' => 'driver',
            ]);
        }

        // Pastikan ada gudang
        if (!Employee::where('email', 'gudang@gmail.com')->exists()) {
            Employee::create([
                'name' => 'Gudang',
                'email' => 'gudang@gmail.com',
                'password' => '123',
                'role' => 'gudang',
            ]);
        }

        // Pastikan ada keuangan
        if (!Employee::where('email', 'keuangan@gmail.com')->exists()) {
            Employee::create([
                'name' => 'Keuangan',
                'email' => 'keuangan@gmail.com',
                'password' => '123',
                'role' => 'keuangan',
            ]);
        }
    }

    private function createSampleSales()
    {
        // Ambil data yang diperlukan
        $customer = Customer::where('email', 'customer@gmail.com')->first();
        $admin = Employee::where('email', 'admin@gmail.com')->first();
        $driver = Employee::where('email', 'driver@gmail.com')->first();
        $gudang = Employee::where('email', 'gudang@gmail.com')->first();
        $keuangan = Employee::where('email', 'keuangan@gmail.com')->first();

        // Pastikan ada produk dan variant
        $product = Product::first();
        $variant = ProductVariant::first();

        if (!$product || !$variant) {
            $this->command->warn('Tidak ada produk atau variant yang tersedia. Jalankan seeder produk terlebih dahulu.');
            return;
        }

        // Contoh data penjualan 1
        $invoice1 = HInvoice::create([
            'code' => 'INV-' . date('Ymd') . '-001',
            'customer_id' => $customer->id,
            'employee_id' => $admin->id,
            'driver_id' => $driver->id,
            'gudang_id' => $gudang->id,
            'accountant_id' => $keuangan->id,
            'grand_total' => 5000000,
            'shipping_cost' => 50000,
            'status' => 'completed',
            'address' => 'Jl. Contoh Alamat 123, Jakarta',
            'is_paid' => true,
            'is_dp' => false,
            'is_online' => true,
            'dp_amount' => null,
            'paid_amount' => 5050000,
            'due_date' => Carbon::now()->addDays(7),
            'receive_date' => Carbon::now()->subDays(2),
            'received_date' => Carbon::now()->subDays(1),
            'delivery_proof_photo' => null,
            'delivery_signature' => null,
            'transfer_proof' => null,
            'quality_proof_photo' => null,
        ]);

        // Detail invoice 1
        DInvoice::create([
            'hinvoice_id' => $invoice1->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'price' => 2500000,
            'quantity' => 2,
            'subtotal' => 5000000,
            'kebutuhan_custom' => null,
            'warna_custom' => null,
        ]);

        // Payment untuk invoice 1
        PaymentModel::create([
            'invoice_id' => $invoice1->id,
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '5050000',
            'snap_token' => null,
        ]);

        // Contoh data penjualan 2
        $invoice2 = HInvoice::create([
            'code' => 'INV-' . date('Ymd') . '-002',
            'customer_id' => $customer->id,
            'employee_id' => $admin->id,
            'driver_id' => $driver->id,
            'gudang_id' => $gudang->id,
            'accountant_id' => $keuangan->id,
            'grand_total' => 3000000,
            'shipping_cost' => 30000,
            'status' => 'pending',
            'address' => 'Jl. Contoh Alamat 456, Bandung',
            'is_paid' => false,
            'is_dp' => true,
            'is_online' => true,
            'dp_amount' => 1500000,
            'paid_amount' => 1500000,
            'due_date' => Carbon::now()->addDays(14),
            'receive_date' => null,
            'received_date' => null,
            'delivery_proof_photo' => null,
            'delivery_signature' => null,
            'transfer_proof' => null,
            'quality_proof_photo' => null,
        ]);

        // Detail invoice 2
        DInvoice::create([
            'hinvoice_id' => $invoice2->id,
            'product_id' => $product->id,
            'variant_id' => $variant->id,
            'price' => 1500000,
            'quantity' => 2,
            'subtotal' => 3000000,
            'kebutuhan_custom' => null,
            'warna_custom' => null,
        ]);

        // Payment untuk invoice 2 (DP)
        PaymentModel::create([
            'invoice_id' => $invoice2->id,
            'method' => 'bank_transfer',
            'type' => 'down_payment',
            'is_paid' => true,
            'amount' => '1500000',
            'snap_token' => null,
        ]);

        $this->command->info('Data penjualan berhasil dibuat!');
    }

    /**
     * Method untuk menambahkan data penjualan dari input user
     * Silakan panggil method ini dengan data yang sesuai
     */
    public function addSalesData($salesData)
    {
        foreach ($salesData as $sale) {
            // Validasi data yang diperlukan
            if (!isset($sale['customer_id']) || !isset($sale['employee_id']) || !isset($sale['items'])) {
                $this->command->warn('Data penjualan tidak lengkap, dilewati...');
                continue;
            }

            // $this->command->info('Processing invoice: ' . $sale['code']);

            // Buat header invoice
            try {
                $invoice = HInvoice::create([
                'code' => $sale['code'] ?? 'INV-' . date('Ymd') . '-' . rand(100, 999),
                'customer_id' => $sale['customer_id'],
                'employee_id' => $sale['employee_id'],
                'driver_id' => $sale['driver_id'] ?? null,
                'gudang_id' => $sale['gudang_id'] ?? null,
                'accountant_id' => $sale['accountant_id'] ?? null,
                'grand_total' => $sale['grand_total'],
                'shipping_cost' => $sale['shipping_cost'] ?? 0,
                'status' => $sale['status'] ?? 'pending',
                'address' => $sale['address'] ?? '',
                'is_paid' => $sale['is_paid'] ?? false,
                'is_dp' => $sale['is_dp'] ?? false,
                'is_online' => $sale['is_online'] ?? true,
                'dp_amount' => $sale['dp_amount'] ?? null,
                'paid_amount' => $sale['paid_amount'] ?? null,
                'due_date' => isset($sale['due_date']) ? Carbon::parse($sale['due_date']) : null,
                'receive_date' => isset($sale['receive_date']) ? Carbon::parse($sale['receive_date']) : null,
                'received_date' => isset($sale['received_date']) ? Carbon::parse($sale['received_date']) : null,
                'delivery_proof_photo' => $sale['delivery_proof_photo'] ?? null,
                'delivery_signature' => $sale['delivery_signature'] ?? null,
                'transfer_proof' => $sale['transfer_proof'] ?? null,
                'quality_proof_photo' => $sale['quality_proof_photo'] ?? null,
            ]);
            
            // $this->command->info('Invoice created with ID: ' . $invoice->id);

            // Buat detail invoice untuk setiap item
            foreach ($sale['items'] as $item) {
                DInvoice::create([
                    'hinvoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'] ?? null,
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'kebutuhan_custom' => $item['kebutuhan_custom'] ?? null,
                    'warna_custom' => $item['warna_custom'] ?? null,
                ]);
            }

            // Buat data payment jika ada
            if (isset($sale['payment'])) {
                PaymentModel::create([
                    'invoice_id' => $invoice->id,
                    'method' => $sale['payment']['method'],
                    'type' => $sale['payment']['type'],
                    'is_paid' => $sale['payment']['is_paid'],
                    'amount' => $sale['payment']['amount'],
                    'snap_token' => $sale['payment']['snap_token'] ?? null,
                ]);
                // $this->command->info('Payment created for invoice: ' . $invoice->code);
            }
            
            } catch (Exception $e) {
                // $this->command->error('Error creating invoice: ' . $e->getMessage());
                throw $e;
            }
        }
    }
}
