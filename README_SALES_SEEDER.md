# 📊 PANDUAN SEEDER DATA PENJUALAN

## ✅ Data Berhasil Dimasukkan!

Data penjualan untuk tanggal **1/11/2024** telah berhasil dimasukkan ke database dengan detail sebagai berikut:

### 📋 Transaksi yang Dimasukkan:

#### **Transaksi 1 - BP GILANG**
- **Kode Invoice**: INV-20241101-001
- **Customer**: BP GILANG (ID: 2)
- **Produk**: LM A20 BS UK (8x14)M2
- **Ukuran**: 8 × 14 = 112 m²
- **Quantity**: 1 (bukan 112)
- **Harga**: Rp15,000 per m²
- **Subtotal**: 112 m² × Rp15,000 = Rp1,680,000
- **Status**: Completed
- **Pembayaran**: Bank Transfer (Full Payment)

#### **Transaksi 2 - GRACIA**
- **Kode Invoice**: INV-20241101-002
- **Customer**: GRACIA (ID: 3)
- **Produk**: LM A17 BO UK (5x6)M2
- **Ukuran**: 5 × 6 = 30 m²
- **Quantity**: 1 (bukan 30)
- **Harga**: Rp12,000 per m²
- **Subtotal**: 30 m² × Rp12,000 = Rp360,000
- **Status**: Completed
- **Pembayaran**: Bank Transfer (Full Payment)

## 🚀 Cara Menjalankan Seeder

### **Opsi 1: Menggunakan Command Khusus**
```bash
php artisan sales:seed --data=sales_data_20241101.php
```

### **Opsi 2: Menggunakan Seeder Terpisah**
```bash
php artisan db:seed --class=SalesDataSeeder
```

### **Opsi 3: Menggunakan DatabaseSeeder Lengkap**
```bash
php artisan db:seed
```

## 📁 File yang Dibuat:

1. **`sales_data_20241101.php`** - File data penjualan untuk tanggal 1/11/2024
2. **`CustomerSeeder.php`** - Seeder untuk customer BP GILANG dan GRACIA
3. **`SalesDataSeeder.php`** - Seeder gabungan untuk customer dan sales data
4. **`SalesSeeder.php`** - Seeder utama untuk data penjualan
5. **`SeedSalesData.php`** - Command untuk menjalankan seeder

## 🔄 Untuk Data Penjualan Selanjutnya:

Jika Anda ingin menambahkan data penjualan lagi, ikuti langkah berikut:

1. **Buat file data baru** (contoh: `sales_data_20241102.php`)
2. **Gunakan template** dari `SALES_DATA_TEMPLATE.php`
3. **Jalankan seeder** dengan command:
   ```bash
   php artisan sales:seed --data=sales_data_20241102.php
   ```

## 📝 Format Data yang Dibutuhkan:

```php
$salesData = [
    [
        'code' => 'INV-YYYYMMDD-XXX',
        'customer_id' => X,
        'employee_id' => X,
        'grand_total' => XXXXXXX,
        'items' => [
            [
                'product_id' => X,
                'price' => XXXXX,
                'quantity' => XX,
                'subtotal' => XXXXXXX,
            ]
        ],
        'payment' => [
            'method' => 'cash',
            'type' => 'full_payment',
            'is_paid' => true,
            'amount' => 'XXXXXXX',
        ]
    ]
];
```

## ⚠️ Catatan Penting:

- Pastikan customer sudah ada di database sebelum menjalankan seeder
- Product ID harus sesuai dengan produk yang ada di database
- Format tanggal menggunakan Y-m-d (contoh: 2024-11-01)
- Harga menggunakan integer tanpa titik atau koma

## 🎯 Status: SELESAI ✅

Data penjualan untuk tanggal 1/11/2024 telah berhasil dimasukkan ke database!
