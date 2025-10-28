<?php

namespace Database\Seeders;

use App\Models\HInvoice;
use App\Models\DInvoice;
use App\Models\PaymentModel;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class SalesDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Data penjualan November 2024
        $salesData = [
            [
                'code' => 'INV-20241101-001',
                'customer_id' => 2, // BP GILANG
                'employee_id' => 1, // Admin
                'driver_id' => 5, // Driver
                'gudang_id' => 4, // Gudang
                'accountant_id' => 2, // Keuangan
                'grand_total' => 1680000,
                'shipping_cost' => 0,
                'status' => 'completed',
                'address' => 'Alamat BP GILANG',
                'is_paid' => true,
                'is_dp' => false,
                'is_online' => false,
                'dp_amount' => null,
                'paid_amount' => 1680000,
                'due_date' => '2024-11-08',
                'receive_date' => '2024-11-01',
                'received_date' => '2024-11-01',
                
                'items' => [
                    [
                        'product_id' => 1,
                        'variant_id' => 1, // Biru Silver
                        'price' => 15000,
                        'quantity' => 1,
                        'subtotal' => 1680000,
                        'kebutuhan_custom' => 'LM A20 BS UK (8x14)M2 - Ukuran: 112 m²',
                        'warna_custom' => 'Biru Silver',
                    ],
                ],
                
                'payment' => [
                    'method' => 'bank_transfer',
                    'type' => 'full_payment',
                    'is_paid' => true,
                    'amount' => '1680000',
                    'snap_token' => null,
                ],
            ],
            
            [
                'code' => 'INV-20241101-002',
                'customer_id' => 3, // GRACIA
                'employee_id' => 1, // Admin
                'driver_id' => 5, // Driver
                'gudang_id' => 4, // Gudang
                'accountant_id' => 2, // Keuangan
                'grand_total' => 360000,
                'shipping_cost' => 0,
                'status' => 'completed',
                'address' => 'Alamat GRACIA',
                'is_paid' => true,
                'is_dp' => false,
                'is_online' => false,
                'dp_amount' => null,
                'paid_amount' => 360000,
                'due_date' => '2024-11-08',
                'receive_date' => '2024-11-01',
                'received_date' => '2024-11-01',
                
                'items' => [
                    [
                        'product_id' => 2,
                        'variant_id' => 4, // Oranye Polos
                        'price' => 12000,
                        'quantity' => 1,
                        'subtotal' => 360000,
                        'kebutuhan_custom' => 'LM A17 BO UK (5x6)M2 - Ukuran: 30 m²',
                        'warna_custom' => 'Oranye Polos',
                    ],
                ],
                
                'payment' => [
                    'method' => 'bank_transfer',
                    'type' => 'full_payment',
                    'is_paid' => true,
                    'amount' => '360000',
                    'snap_token' => null,
                ],
            ],
            
            // Transaksi 3 - PT TAN (BP TEKKO)
            [
                'code' => 'INV-20241101-003',
                'customer_id' => 4, // PT TAN (BP TEKKO) - akan dibuat
                'employee_id' => 1, // Admin
                'driver_id' => 5, // Driver
                'gudang_id' => 4, // Gudang
                'accountant_id' => 2, // Keuangan
                'grand_total' => 2400000,
                'shipping_cost' => 0,
                'status' => 'completed',
                'address' => 'Alamat PT TAN (BP TEKKO)',
                'is_paid' => true,
                'is_dp' => false,
                'is_online' => false,
                'dp_amount' => null,
                'paid_amount' => 2400000,
                'due_date' => '2024-11-08',
                'receive_date' => '2024-11-01',
                'received_date' => '2024-11-01',
                
                'items' => [
                    [
                        'product_id' => 3,
                        'variant_id' => 7, // Coklat Silver
                        'price' => 25000,
                        'quantity' => 2,
                        'subtotal' => 2400000,
                        'kebutuhan_custom' => 'TPRP ULIN ORCH MSPR HD UK (4x12)M2 - Ukuran: 48 m²',
                        'warna_custom' => 'Coklat Silver',
                    ],
                ],
                
                'payment' => [
                    'method' => 'bank_transfer',
                    'type' => 'full_payment',
                    'is_paid' => true,
                    'amount' => '2400000',
                    'snap_token' => null,
                ],
            ],
            
            // Transaksi 4 - AZHAR JAYA
            [
                'code' => 'INV-20241101-004',
                'customer_id' => 5, // AZHAR JAYA - akan dibuat
                'employee_id' => 1, // Admin
                'driver_id' => 5, // Driver
                'gudang_id' => 4, // Gudang
                'accountant_id' => 2, // Keuangan
                'grand_total' => 966000,
                'shipping_cost' => 0,
                'status' => 'completed',
                'address' => 'Alamat AZHAR JAYA',
                'is_paid' => true,
                'is_dp' => false,
                'is_online' => false,
                'dp_amount' => null,
                'paid_amount' => 966000,
                'due_date' => '2024-11-08',
                'receive_date' => '2024-11-01',
                'received_date' => '2024-11-01',
                
                'items' => [
                    [
                        'product_id' => 2,
                        'variant_id' => 5, // Hijau Polos
                        'price' => 11500,
                        'quantity' => 1,
                        'subtotal' => 966000,
                        'kebutuhan_custom' => 'LM A17 BO UK (6x14)M2 - Ukuran: 84 m²',
                        'warna_custom' => 'Hijau Polos',
                    ],
                ],
                
                'payment' => [
                    'method' => 'bank_transfer',
                    'type' => 'full_payment',
                    'is_paid' => true,
                    'amount' => '966000',
                    'snap_token' => null,
                ],
            ],
            
            // Transaksi 5 - BP SOLEH
            [
                'code' => 'INV-20241104-001',
                'customer_id' => 6, // BP SOLEH - akan dibuat
                'employee_id' => 1, // Admin
                'driver_id' => 5, // Driver
                'gudang_id' => 4, // Gudang
                'accountant_id' => 2, // Keuangan
                'grand_total' => 925000,
                'shipping_cost' => 0,
                'status' => 'completed',
                'address' => 'Alamat BP SOLEH',
                'is_paid' => true,
                'is_dp' => false,
                'is_online' => false,
                'dp_amount' => null,
                'paid_amount' => 925000,
                'due_date' => '2024-11-11',
                'receive_date' => '2024-11-04',
                'received_date' => '2024-11-04',
                
                'items' => [
                    [
                        'product_id' => 2,
                        'variant_id' => 6, // Hijau Silver
                        'price' => 925000,
                        'quantity' => 1,
                        'subtotal' => 925000,
                        'kebutuhan_custom' => 'LM A17 BO UK (7x12)M2 - Ukuran: 84 m²',
                        'warna_custom' => 'Hijau Silver',
                    ],
                ],
                
                'payment' => [
                    'method' => 'bank_transfer',
                    'type' => 'full_payment',
                    'is_paid' => true,
                    'amount' => '925000',
                    'snap_token' => null,
                ],
            ],
        ];

        foreach ($salesData as $sale) {
            // Cek apakah invoice sudah ada
            if (HInvoice::where('code', $sale['code'])->exists()) {
                continue;
            }

            // Buat header invoice
            $invoice = HInvoice::create([
                'code' => $sale['code'],
                'customer_id' => $sale['customer_id'],
                'employee_id' => $sale['employee_id'],
                'driver_id' => $sale['driver_id'],
                'gudang_id' => $sale['gudang_id'],
                'accountant_id' => $sale['accountant_id'],
                'grand_total' => $sale['grand_total'],
                'shipping_cost' => $sale['shipping_cost'],
                'status' => $sale['status'],
                'address' => $sale['address'],
                'is_paid' => $sale['is_paid'],
                'is_dp' => $sale['is_dp'],
                'is_online' => $sale['is_online'],
                'dp_amount' => $sale['dp_amount'],
                'paid_amount' => $sale['paid_amount'],
                'due_date' => Carbon::parse($sale['due_date']),
                'receive_date' => Carbon::parse($sale['receive_date']),
                'received_date' => Carbon::parse($sale['received_date']),
                'delivery_proof_photo' => null,
                'delivery_signature' => null,
                'transfer_proof' => null,
                'quality_proof_photo' => null,
            ]);

            // Buat detail invoice
            foreach ($sale['items'] as $item) {
                DInvoice::create([
                    'hinvoice_id' => $invoice->id,
                    'product_id' => $item['product_id'],
                    'variant_id' => $item['variant_id'],
                    'price' => $item['price'],
                    'quantity' => $item['quantity'],
                    'subtotal' => $item['subtotal'],
                    'kebutuhan_custom' => $item['kebutuhan_custom'],
                    'warna_custom' => $item['warna_custom'],
                ]);
            }

            // Buat payment
            PaymentModel::create([
                'invoice_id' => $invoice->id,
                'method' => $sale['payment']['method'],
                'type' => $sale['payment']['type'],
                'is_paid' => $sale['payment']['is_paid'],
                'amount' => $sale['payment']['amount'],
                'snap_token' => $sale['payment']['snap_token'],
            ]);
        }

        $this->command->info('Sales data seeded successfully!');
    }
}