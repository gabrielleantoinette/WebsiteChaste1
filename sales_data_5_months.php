<?php

return [
    // ===== NOVEMBER 2024 DATA =====
    
    // Transaksi 4 November 2024
    [
        'code' => 'INV-20241104-007',
        'customer_id' => 12, // RUDI
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 2975000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat RUDI',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 2975000,
        'due_date' => '2024-11-11',
        'receive_date' => '2024-11-04',
        'received_date' => '2024-11-04',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 1, // Biru Silver
                'price' => 17000,
                'quantity' => 5,
                'subtotal' => 2975000,
                'kebutuhan_custom' => 'LL A20 HPT UK (5x7)M2 - Ukuran: 35 m²',
                'warna_custom' => 'Biru Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '2975000',
            'snap_token' => null,
        ],
    ],
    
    [
        'code' => 'INV-20241104-008',
        'customer_id' => 13, // BP AHOK
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 1152000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat BP AHOK',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 1152000,
        'due_date' => '2024-11-11',
        'receive_date' => '2024-11-04',
        'received_date' => '2024-11-04',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 2, // Biru Polos
                'price' => 24000,
                'quantity' => 2,
                'subtotal' => 1152000,
                'kebutuhan_custom' => 'TRP ULIN ORCH HD UK (4x6)M2 - Ukuran: 24 m²',
                'warna_custom' => 'Biru Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '1152000',
            'snap_token' => null,
        ],
    ],
    
    [
        'code' => 'INV-20241104-009',
        'customer_id' => 14, // BP WALUYO
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 5365000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat BP WALUYO',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 5365000,
        'due_date' => '2024-11-11',
        'receive_date' => '2024-11-04',
        'received_date' => '2024-11-04',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 3, // Oranye Silver
                'price' => 1073000,
                'quantity' => 5,
                'subtotal' => 5365000,
                'kebutuhan_custom' => 'LL A15 SKR BS UK (7x13)M2 - Ukuran: 91 m²',
                'warna_custom' => 'Oranye Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '5365000',
            'snap_token' => null,
        ],
    ],
    
    // PT ABC JAYA - Multiple Items
    [
        'code' => 'INV-20241104-010',
        'customer_id' => 15, // PT ABC JAYA
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 2169600, // 864000 + 324000 + 982800
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat PT ABC JAYA',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 2169600,
        'due_date' => '2024-11-11',
        'receive_date' => '2024-11-04',
        'received_date' => '2024-11-04',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 4, // Oranye Polos
                'price' => 10800,
                'quantity' => 2,
                'subtotal' => 864000,
                'kebutuhan_custom' => 'LM A17 BS UK (5x8)M2 - Ukuran: 40 m²',
                'warna_custom' => 'Oranye Polos',
            ],
            [
                'product_id' => 2,
                'variant_id' => 5, // Hijau Polos
                'price' => 10800,
                'quantity' => 1,
                'subtotal' => 324000,
                'kebutuhan_custom' => 'LM A17 BS UK (5x6)M2 - Ukuran: 30 m²',
                'warna_custom' => 'Hijau Polos',
            ],
            [
                'product_id' => 2,
                'variant_id' => 6, // Hijau Silver
                'price' => 10800,
                'quantity' => 1,
                'subtotal' => 982800,
                'kebutuhan_custom' => 'LM A17 BS UK (7x13)M2 - Ukuran: 91 m²',
                'warna_custom' => 'Hijau Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '2169600',
            'snap_token' => null,
        ],
    ],
    
    // GRACIA - Update existing customer
    [
        'code' => 'INV-20241104-011',
        'customer_id' => 3, // GRACIA (existing)
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 600000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat GRACIA',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 600000,
        'due_date' => '2024-11-11',
        'receive_date' => '2024-11-04',
        'received_date' => '2024-11-04',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 7, // Coklat Silver
                'price' => 50000,
                'quantity' => 1,
                'subtotal' => 600000,
                'kebutuhan_custom' => 'GD A4 BS UK (3x4)M2 - Ukuran: 12 m²',
                'warna_custom' => 'Coklat Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '600000',
            'snap_token' => null,
        ],
    ],
    
    // TK NELAYAN (BP AMIN) - Multiple Items
    [
        'code' => 'INV-20241104-012',
        'customer_id' => 16, // TK NELAYAN (BP AMIN)
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 2050000, // 480000 + 384000 + 512000 + 512000 + 162000 + 162000
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat TK NELAYAN (BP AMIN)',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 2050000,
        'due_date' => '2024-11-11',
        'receive_date' => '2024-11-04',
        'received_date' => '2024-11-04',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 8, // Coklat Polos
                'price' => 4000,
                'quantity' => 5,
                'subtotal' => 480000,
                'kebutuhan_custom' => 'GD A3A BS UK (4x6)M2 - Ukuran: 24 m²',
                'warna_custom' => 'Coklat Polos',
            ],
            [
                'product_id' => 3,
                'variant_id' => 1, // Biru Silver
                'price' => 4000,
                'quantity' => 4,
                'subtotal' => 384000,
                'kebutuhan_custom' => 'GA A3A CS UK (4x6)M2 - Ukuran: 24 m²',
                'warna_custom' => 'Biru Silver',
            ],
            [
                'product_id' => 3,
                'variant_id' => 2, // Biru Polos
                'price' => 4000,
                'quantity' => 4,
                'subtotal' => 512000,
                'kebutuhan_custom' => 'GD A3A BS UK (4x8)M2 - Ukuran: 32 m²',
                'warna_custom' => 'Biru Polos',
            ],
            [
                'product_id' => 3,
                'variant_id' => 3, // Oranye Silver
                'price' => 4000,
                'quantity' => 4,
                'subtotal' => 512000,
                'kebutuhan_custom' => 'GD A3A CS UK (4x8)M2 - Ukuran: 32 m²',
                'warna_custom' => 'Oranye Silver',
            ],
            [
                'product_id' => 3,
                'variant_id' => 4, // Oranye Polos
                'price' => 3000,
                'quantity' => 6,
                'subtotal' => 162000,
                'kebutuhan_custom' => 'NAGA A2 BP UK (3x3)M2 - Ukuran: 9 m²',
                'warna_custom' => 'Oranye Polos',
            ],
            [
                'product_id' => 3,
                'variant_id' => 5, // Hijau Polos
                'price' => 3000,
                'quantity' => 6,
                'subtotal' => 162000,
                'kebutuhan_custom' => 'NAGA A2 CP UK (3x3)M2 - Ukuran: 9 m²',
                'warna_custom' => 'Hijau Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '2050000',
            'snap_token' => null,
        ],
    ],
    
    // Transaksi 5 November 2024
    [
        'code' => 'INV-20241105-001',
        'customer_id' => 17, // BUDIAMAN
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 319200, // 136800 + 182400
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat BUDIAMAN',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 319200,
        'due_date' => '2024-11-12',
        'receive_date' => '2024-11-05',
        'received_date' => '2024-11-05',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 6, // Hijau Silver
                'price' => 3800,
                'quantity' => 4,
                'subtotal' => 136800,
                'kebutuhan_custom' => 'GD A4 BS UK (3x3)M2 - Ukuran: 9 m²',
                'warna_custom' => 'Hijau Silver',
            ],
            [
                'product_id' => 3,
                'variant_id' => 7, // Coklat Silver
                'price' => 3800,
                'quantity' => 4,
                'subtotal' => 182400,
                'kebutuhan_custom' => 'GD A4 BS UK (3x4)M2 - Ukuran: 12 m²',
                'warna_custom' => 'Coklat Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '319200',
            'snap_token' => null,
        ],
    ],
    
    // EKO SUPRIYADI - Update existing customer
    [
        'code' => 'INV-20241105-002',
        'customer_id' => 7, // EKO SUPRIYADI (existing)
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 1050000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat EKO SUPRIYADI',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 1050000,
        'due_date' => '2024-11-12',
        'receive_date' => '2024-11-05',
        'received_date' => '2024-11-05',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 8, // Coklat Polos
                'price' => 6000,
                'quantity' => 5,
                'subtotal' => 1050000,
                'kebutuhan_custom' => 'TRONTON A8 BP UK (5x7)M2 - Ukuran: 35 m²',
                'warna_custom' => 'Coklat Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '1050000',
            'snap_token' => null,
        ],
    ],
    
    // KO CUNGHO - Multiple Items
    [
        'code' => 'INV-20241105-003',
        'customer_id' => 19, // KO CUNGHO
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 646000, // 408000 + 238000
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat KO CUNGHO',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 646000,
        'due_date' => '2024-11-12',
        'receive_date' => '2024-11-05',
        'received_date' => '2024-11-05',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 1, // Biru Silver
                'price' => 3400,
                'quantity' => 5,
                'subtotal' => 408000,
                'kebutuhan_custom' => 'GD A4 BS UK (4x6)M2 - Ukuran: 24 m²',
                'warna_custom' => 'Biru Silver',
            ],
            [
                'product_id' => 3,
                'variant_id' => 2, // Biru Polos
                'price' => 3400,
                'quantity' => 2,
                'subtotal' => 238000,
                'kebutuhan_custom' => 'GD A4 BS UK (5x7)M2 - Ukuran: 35 m²',
                'warna_custom' => 'Biru Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '646000',
            'snap_token' => null,
        ],
    ],
    
    // ATIK KENJERAN
    [
        'code' => 'INV-20241105-004',
        'customer_id' => 20, // ATIK KENJERAN
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 339300,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat ATIK KENJERAN',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 339300,
        'due_date' => '2024-11-12',
        'receive_date' => '2024-11-05',
        'received_date' => '2024-11-05',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 3, // Oranye Silver
                'price' => 8700,
                'quantity' => 1,
                'subtotal' => 339300,
                'kebutuhan_custom' => 'LM A12 BSM UK (6x6,5)M2 - Ukuran: 39 m²',
                'warna_custom' => 'Oranye Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '339300',
            'snap_token' => null,
        ],
    ],
    
    // UD ARTHA MAYJEND - Multiple Items
    [
        'code' => 'INV-20241105-005',
        'customer_id' => 21, // UD ARTHA MAYJEND
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 1180000, // 700000 + 480000
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat UD ARTHA MAYJEND',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 1180000,
        'due_date' => '2024-11-12',
        'receive_date' => '2024-11-05',
        'received_date' => '2024-11-05',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 4, // Oranye Polos
                'price' => 4000,
                'quantity' => 5,
                'subtotal' => 700000,
                'kebutuhan_custom' => 'GD A4 BS UK (5x7)M2 - Ukuran: 35 m²',
                'warna_custom' => 'Oranye Polos',
            ],
            [
                'product_id' => 3,
                'variant_id' => 5, // Hijau Polos
                'price' => 4000,
                'quantity' => 5,
                'subtotal' => 480000,
                'kebutuhan_custom' => 'GD A4 BS UK (4x6)M2 - Ukuran: 24 m²',
                'warna_custom' => 'Hijau Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '1180000',
            'snap_token' => null,
        ],
    ],
    
    // Transaksi 6 November 2024
    // EDI SIANG - Multiple Items
    [
        'code' => 'INV-20241106-001',
        'customer_id' => 22, // EDI SIANG
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 11000000, // 3300000 + 4400000 + 3300000
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat EDI SIANG',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 11000000,
        'due_date' => '2024-11-13',
        'receive_date' => '2024-11-06',
        'received_date' => '2024-11-06',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 6, // Hijau Silver
                'price' => 11000,
                'quantity' => 25,
                'subtotal' => 3300000,
                'kebutuhan_custom' => 'SKR 2 GJH A12 BS UK (3X4) M2 - Ukuran: 12 m²',
                'warna_custom' => 'Hijau Silver',
            ],
            [
                'product_id' => 2,
                'variant_id' => 7, // Coklat Silver
                'price' => 11000,
                'quantity' => 20,
                'subtotal' => 4400000,
                'kebutuhan_custom' => 'SKR 2 GJH A12 BS UK (4X5) M2 - Ukuran: 20 m²',
                'warna_custom' => 'Coklat Silver',
            ],
            [
                'product_id' => 2,
                'variant_id' => 8, // Coklat Polos
                'price' => 11000,
                'quantity' => 10,
                'subtotal' => 3300000,
                'kebutuhan_custom' => 'SKR 2 GJH A12 BS UK (5X6) M2 - Ukuran: 30 m²',
                'warna_custom' => 'Coklat Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '11000000',
            'snap_token' => null,
        ],
    ],
    
    // CIK ESTER
    [
        'code' => 'INV-20241106-002',
        'customer_id' => 23, // CIK ESTER
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 144000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat CIK ESTER',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 144000,
        'due_date' => '2024-11-13',
        'receive_date' => '2024-11-06',
        'received_date' => '2024-11-06',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 1, // Biru Silver
                'price' => 4000,
                'quantity' => 2,
                'subtotal' => 144000,
                'kebutuhan_custom' => 'GD A4 BS UK (2X9) M2 - Ukuran: 18 m²',
                'warna_custom' => 'Biru Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '144000',
            'snap_token' => null,
        ],
    ],
    
    // BP LAN
    [
        'code' => 'INV-20241106-003',
        'customer_id' => 24, // BP LAN
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 112500,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat BP LAN',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 112500,
        'due_date' => '2024-11-13',
        'receive_date' => '2024-11-06',
        'received_date' => '2024-11-06',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 2, // Biru Polos
                'price' => 12500,
                'quantity' => 1,
                'subtotal' => 112500,
                'kebutuhan_custom' => 'LL A12 SKR (3X3) M2 - Ukuran: 9 m²',
                'warna_custom' => 'Biru Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '112500',
            'snap_token' => null,
        ],
    ],
    
    // MOSES
    [
        'code' => 'INV-20241106-004',
        'customer_id' => 25, // MOSES
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 9400000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat MOSES',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 9400000,
        'due_date' => '2024-11-13',
        'receive_date' => '2024-11-06',
        'received_date' => '2024-11-06',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 3, // Oranye Silver
                'price' => 23500,
                'quantity' => 4,
                'subtotal' => 9400000,
                'kebutuhan_custom' => 'RING CP 1XP (4 KOLI) - Ukuran: 100 m²',
                'warna_custom' => 'Oranye Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '9400000',
            'snap_token' => null,
        ],
    ],
    
    // Transaksi 7 November 2024
    [
        'code' => 'INV-20241107-001',
        'customer_id' => 26, // BP SANTOSO
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 54000, // 7200 * 7.5 = 54000
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat BP SANTOSO',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 54000,
        'due_date' => '2024-11-14',
        'receive_date' => '2024-11-07',
        'received_date' => '2024-11-07',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 4, // Oranye Polos
                'price' => 7200,
                'quantity' => 1,
                'subtotal' => 54000,
                'kebutuhan_custom' => 'LL A5 BS UK (2,5X3) M2 - Ukuran: 7.5 m²',
                'warna_custom' => 'Oranye Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '54000',
            'snap_token' => null,
        ],
    ],
    
    // Transaksi 8 November 2024
    [
        'code' => 'INV-20241108-001',
        'customer_id' => 27, // EKA PS
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 3600000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat EKA PS',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 3600000,
        'due_date' => '2024-11-15',
        'receive_date' => '2024-11-08',
        'received_date' => '2024-11-08',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 5, // Hijau Polos
                'price' => 10000,
                'quantity' => 8,
                'subtotal' => 3600000,
                'kebutuhan_custom' => 'LM A10 SKR BS UK (5X9) M2 - Ukuran: 45 m²',
                'warna_custom' => 'Hijau Polos',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '3600000',
            'snap_token' => null,
        ],
    ],
    
    // Transaksi 11 November 2024
    [
        'code' => 'INV-20241111-001',
        'customer_id' => 28, // CV PELANGI (BP ALFRED)
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 864000,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat CV PELANGI (BP ALFRED)',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 864000,
        'due_date' => '2024-11-18',
        'receive_date' => '2024-11-11',
        'received_date' => '2024-11-11',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 6, // Hijau Silver
                'price' => 18000,
                'quantity' => 1,
                'subtotal' => 864000,
                'kebutuhan_custom' => 'LM A20 ORCH HPT UK (6X8) M2 - Ukuran: 48 m²',
                'warna_custom' => 'Hijau Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '864000',
            'snap_token' => null,
        ],
    ],
    
    [
        'code' => 'INV-20241111-002',
        'customer_id' => 29, // CIK RANI
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 2191200,
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat CIK RANI',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 2191200,
        'due_date' => '2024-11-18',
        'receive_date' => '2024-11-11',
        'received_date' => '2024-11-11',
        'items' => [
            [
                'product_id' => 2,
                'variant_id' => 7, // Coklat Silver
                'price' => 11000,
                'quantity' => 4,
                'subtotal' => 2191200,
                'kebutuhan_custom' => 'LM A10 SKR BS UK (6X8,3) M2 - Ukuran: 49.8 m²',
                'warna_custom' => 'Coklat Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '2191200',
            'snap_token' => null,
        ],
    ],
    
    // Transaksi 12 November 2024
    [
        'code' => 'INV-20241112-001',
        'customer_id' => 29, // CI VONY (ZZ)
        'employee_id' => 1,
        'driver_id' => 5,
        'gudang_id' => 4,
        'accountant_id' => 2,
        'grand_total' => 882000, // 495000 + 387000
        'shipping_cost' => 0,
        'status' => 'completed',
        'address' => 'Alamat CI VONY (ZZ)',
        'is_paid' => true,
        'is_dp' => false,
        'is_online' => false,
        'dp_amount' => null,
        'paid_amount' => 882000,
        'due_date' => '2024-11-19',
        'receive_date' => '2024-11-12',
        'received_date' => '2024-11-12',
        'items' => [
            [
                'product_id' => 3,
                'variant_id' => 8, // Coklat Polos
                'price' => 3300,
                'quantity' => 25,
                'subtotal' => 495000,
                'kebutuhan_custom' => 'GD A3A 15BP 10CP UK (2X3) M2 - Ukuran: 6 m²',
                'warna_custom' => 'Coklat Polos',
            ],
            [
                'product_id' => 3,
                'variant_id' => 1, // Biru Silver
                'price' => 4300,
                'quantity' => 15,
                'subtotal' => 387000,
                'kebutuhan_custom' => 'KAPAL LAYAR A5 10BP 5CP UK (2X3) M2 - Ukuran: 6 m²',
                'warna_custom' => 'Biru Silver',
            ],
        ],
        'payment' => [
            'method' => 'bank_transfer',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => '882000',
            'snap_token' => null,
        ],
    ],
    
    // ===== DESEMBER 2024 DATA (PLACEHOLDER) =====
    // Data Desember akan ditambahkan di sini
    
    // ===== JANUARI 2025 DATA (PLACEHOLDER) =====
    // Data Januari akan ditambahkan di sini
    
    // ===== FEBRUARI 2025 DATA (PLACEHOLDER) =====
    // Data Februari akan ditambahkan di sini
    
    // ===== MARET 2025 DATA (PLACEHOLDER) =====
    // Data Maret akan ditambahkan di sini
];
