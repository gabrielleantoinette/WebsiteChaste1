<?php

/**
 * DATA PENJUALAN BERDASARKAN INPUT USER
 * Tanggal: 1/11/2024
 */

$salesData = [
    // Transaksi 1 - BP GILANG
    [
        'code' => 'INV-20241101-001',
        'customer_id' => 2, // BP GILANG (ID 2 setelah customer default)
        'employee_id' => 1, // Admin
        'driver_id' => 5, // Driver
        'gudang_id' => 4, // Gudang
        'accountant_id' => 2, // Keuangan
        'grand_total' => 1680000,
        'shipping_cost' => 0, // Tidak ada info ongkos kirim
        'status' => 'completed', // Asumsi sudah selesai
        'address' => 'Alamat BP GILANG', // Perlu alamat lengkap
        'is_paid' => true, // Asumsi sudah dibayar
        'is_dp' => false,
        'is_online' => false, // Asumsi transaksi offline
        'dp_amount' => null,
        'paid_amount' => 1680000,
        'due_date' => '2024-11-08', // 7 hari setelah tanggal transaksi
        'receive_date' => '2024-11-01',
        'received_date' => '2024-11-01',
        
        'items' => [
            [
                'product_id' => 1, // Perlu disesuaikan dengan ID produk yang ada
                'variant_id' => null, // Jika ada variant untuk LM A20 BS UK
                'price' => 15000, // Harga per m²
                'quantity' => 1, // Quantity = 1 (bukan 112)
                'subtotal' => 1680000, // 112 m² × Rp15,000 = Rp1,680,000
                'kebutuhan_custom' => 'LM A20 BS UK (8x14)M2 - Ukuran: 112 m²', // Info produk detail dengan ukuran
                'warna_custom' => null,
            ],
        ],
        
        'payment' => [
            'method' => 'bank_transfer', // Pembayaran transfer bank
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '1680000',
            'snap_token' => null,
        ],
    ],
    
    // Transaksi 2 - GRACIA
    [
        'code' => 'INV-20241101-002',
        'customer_id' => 3, // GRACIA (ID 3 setelah customer default)
        'employee_id' => 1, // Admin
        'driver_id' => 5, // Driver
        'gudang_id' => 4, // Gudang
        'accountant_id' => 2, // Keuangan
        'grand_total' => 360000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat GRACIA', // Perlu alamat lengkap
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
                'product_id' => 2, // Perlu disesuaikan dengan ID produk yang ada
                'variant_id' => null, // Jika ada variant untuk LM A17 BO UK
                'price' => 12000, // Harga per m²
                'quantity' => 1, // Quantity = 1 (bukan 30)
                'subtotal' => 360000, // 30 m² × Rp12,000 = Rp360,000
                'kebutuhan_custom' => 'LM A17 BO UK (5x6)M2 - Ukuran: 30 m²', // Info produk detail dengan ukuran
                'warna_custom' => null,
            ],
        ],
        
        'payment' => [
            'method' => 'bank_transfer', // Pembayaran transfer bank
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '360000',
            'snap_token' => null,
        ],
    ],
];

return $salesData;
