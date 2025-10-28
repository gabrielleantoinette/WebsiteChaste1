<?php

/**
 * TEMPLATE DATA PENJUALAN UNTUK SEEDER
 * 
 * Silakan copy template ini dan ganti dengan data penjualan yang sebenarnya.
 * Kemudian panggil method addSalesData() dengan data yang sudah disesuaikan.
 */

// Contoh format data penjualan yang bisa Anda gunakan:

$salesData = [
    [
        // Data Header Invoice
        'code' => 'INV-20250115-001', // Kode invoice (opsional, akan auto-generate jika kosong)
        'customer_id' => 1, // ID customer (wajib)
        'employee_id' => 1, // ID employee/admin (wajib)
        'driver_id' => 5, // ID driver (opsional)
        'gudang_id' => 4, // ID gudang (opsional)
        'accountant_id' => 2, // ID keuangan (opsional)
        'grand_total' => 5000000, // Total harga (wajib)
        'shipping_cost' => 50000, // Ongkos kirim (opsional, default 0)
        'status' => 'completed', // Status: pending, processing, completed, cancelled
        'address' => 'Jl. Contoh Alamat 123, Jakarta', // Alamat pengiriman
        'is_paid' => true, // Sudah dibayar atau belum
        'is_dp' => false, // Apakah pembayaran DP
        'is_online' => true, // Transaksi online atau offline
        'dp_amount' => null, // Jumlah DP (jika ada)
        'paid_amount' => 5050000, // Jumlah yang sudah dibayar
        'due_date' => '2025-01-22', // Tanggal jatuh tempo (format Y-m-d)
        'receive_date' => '2025-01-13', // Tanggal diterima (format Y-m-d)
        'received_date' => '2025-01-14', // Tanggal konfirmasi diterima (format Y-m-d)
        
        // Data Item yang dibeli
        'items' => [
            [
                'product_id' => 1, // ID produk (wajib)
                'variant_id' => 1, // ID variant (opsional)
                'price' => 2500000, // Harga per item
                'quantity' => 2, // Jumlah
                'subtotal' => 5000000, // Subtotal (price * quantity)
                'kebutuhan_custom' => null, // Kebutuhan custom (jika ada)
                'warna_custom' => null, // Warna custom (jika ada)
            ],
            // Tambahkan item lain jika ada
        ],
        
        // Data Pembayaran
        'payment' => [
            'method' => 'bank_transfer', // Metode pembayaran: bank_transfer, cash, credit_card, etc
            'type' => 'full_payment', // Tipe: full_payment, down_payment, installment
            'is_paid' => true, // Status pembayaran
            'amount' => '5050000', // Jumlah pembayaran (string)
            'snap_token' => null, // Token snap (jika menggunakan Midtrans)
        ],
    ],
    
    // Contoh transaksi kedua
    [
        'code' => 'INV-20250115-002',
        'customer_id' => 1,
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 3000000,
        'shipping_cost' => 30000,
        'status' => 'pending',
        'address' => 'Jl. Contoh Alamat 456, Bandung',
        'is_paid' => false,
        'is_dp' => true,
        'is_online' => true,
        'dp_amount' => 1500000,
        'paid_amount' => 1500000,
        'due_date' => '2025-01-29',
        'receive_date' => null,
        'received_date' => null,
        
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 4,
                'price' => 1500000,
                'quantity' => 2,
                'subtotal' => 3000000,
                'kebutuhan_custom' => null,
                'warna_custom' => null,
            ],
        ],
        
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'down_payment',
            'is_paid' => true,
            'amount' => '1500000',
            'snap_token' => null,
        ],
    ],
    
    // Tambahkan data penjualan lainnya sesuai kebutuhan...
];

/**
 * CARA PENGGUNAAN:
 * 
 * 1. Ganti data di atas dengan data penjualan yang sebenarnya
 * 2. Pastikan customer_id, employee_id, product_id, dan variant_id sudah ada di database
 * 3. Jalankan seeder dengan command: php artisan db:seed --class=SalesSeeder
 * 
 * ATAU
 * 
 * Jika ingin menambahkan data secara manual di SalesSeeder.php:
 * 1. Buka file database/seeders/SalesSeeder.php
 * 2. Ganti method createSampleSales() dengan data yang sebenarnya
 * 3. Atau tambahkan method baru yang memanggil addSalesData($salesData)
 */

/**
 * CATATAN PENTING:
 * 
 * - Pastikan customer, employee, product, dan variant sudah ada sebelum menjalankan seeder
 * - Format tanggal menggunakan Y-m-d (contoh: 2025-01-15)
 * - Harga menggunakan integer (tanpa titik atau koma)
 * - Status yang valid: pending, processing, completed, cancelled
 * - Method pembayaran: bank_transfer, cash, credit_card, e_wallet, etc
 * - Type pembayaran: full_payment, down_payment, installment
 */
