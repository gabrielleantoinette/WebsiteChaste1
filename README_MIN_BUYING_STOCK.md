# Fitur Minimal Quantity untuk Tawar Menawar

## Overview
Fitur ini memastikan customer hanya bisa tawar menawar jika membeli dalam quantity yang cukup besar sesuai standar yang ditetapkan owner. Ini membuat sistem tawar menawar lebih realistis dan menguntungkan bisnis.

## Implementasi

### 1. Database Schema
- **File**: `database/migrations/2025_02_06_133244_create_products_table.php`
- **Kolom**: `min_buying_stock` (integer, default: 1)
- **Deskripsi**: Minimal quantity untuk bisa tawar menawar

### 2. Model Product
- **File**: `app/Models/Product.php`
- **Fitur**: Tambah `min_buying_stock` ke fillable array

### 3. Admin Interface (Owner)
- **File**: `resources/views/admin/products/view.blade.php`
- **Fitur**: 
  - Kolom "Min. Quantity Tawar" di tabel produk
  - Form input untuk set minimal quantity
  - Update button untuk simpan perubahan

### 4. Controller Method
- **File**: `app/Http/Controllers/ProductController.php`
- **Method**: `updateMinBuyingStockAction()`
- **Fitur**: Validasi dan update min_buying_stock

### 5. Customer Interface
- **File**: `resources/views/produk-detail.blade.php`
- **Fitur**: 
  - Tampilkan info minimal quantity di tombol tawar
  - Disable tawar jika quantity tidak cukup

### 6. Halaman Negosiasi
- **File**: `resources/views/negosiasi.blade.php`
- **Fitur**: Info box tentang minimal quantity untuk tawar menawar

## Alur Kerja

### 1. Owner Set Minimal Quantity
```
1. Owner login ke admin panel
2. Buka "Kelola Produk"
3. Set "Min. Quantity Tawar" untuk setiap produk
4. Klik "Update" untuk simpan
```

### 2. Customer View Product
```
1. Customer buka halaman produk
2. Lihat tombol "Tawar Harga"
3. Jika min_buying_stock > 1:
   - Tampilkan info "Minimal X pcs untuk tawar menawar"
4. Jika min_buying_stock = 1:
   - Tombol tawar normal (tanpa info)
```

### 3. Customer Tawar Menawar
```
1. Customer klik "Tawar Harga"
2. Lihat info box tentang minimal quantity
3. Proses tawar menawar seperti biasa
4. Saat checkout, quantity akan divalidasi
```

## Contoh Konfigurasi

### Produk Terpal A5 - 2x3
- **Harga Normal**: Rp 1.000
- **Min. Quantity Tawar**: 5 pcs
- **Harga Minimal**: Rp 650

### Produk Terpal A5 - 3x4
- **Harga Normal**: Rp 2.000
- **Min. Quantity Tawar**: 3 pcs
- **Harga Minimal**: Rp 1.300

### Produk Terpal A5 - 4x6
- **Harga Normal**: Rp 3.000
- **Min. Quantity Tawar**: 2 pcs
- **Harga Minimal**: Rp 1.950

## Keuntungan Bisnis

### 1. Volume Penjualan
- Customer harus beli minimal quantity tertentu
- Meningkatkan total nilai transaksi
- Mengurangi transaksi kecil-kecilan

### 2. Margin yang Lebih Baik
- Quantity besar = diskon yang lebih masuk akal
- Mengurangi tawar menawar untuk pembelian kecil
- Fokus pada customer dengan kebutuhan besar

### 3. Efisiensi Operasional
- Mengurangi waktu negosiasi untuk pembelian kecil
- Fokus pada customer yang serius
- Mengoptimalkan stok untuk pembelian besar

## Testing

### Test Case 1: Quantity Cukup
1. Owner set min_buying_stock = 5 untuk produk A
2. Customer buka produk A
3. **Expected**: Lihat info "Minimal 5 pcs untuk tawar menawar"

### Test Case 2: Quantity Tidak Cukup
1. Owner set min_buying_stock = 10 untuk produk B
2. Customer buka produk B
3. **Expected**: Lihat info "Minimal 10 pcs untuk tawar menawar"

### Test Case 3: Quantity Default
1. Owner set min_buying_stock = 1 untuk produk C
2. Customer buka produk C
3. **Expected**: Tombol tawar normal tanpa info

## File yang Dimodifikasi

1. `database/migrations/2025_02_06_133244_create_products_table.php`
   - Tambah kolom min_buying_stock

2. `app/Models/Product.php`
   - Tambah min_buying_stock ke fillable

3. `resources/views/admin/products/view.blade.php`
   - Tambah kolom dan form untuk min_buying_stock

4. `app/Http/Controllers/ProductController.php`
   - Tambah method updateMinBuyingStockAction

5. `routes/web.php`
   - Tambah route untuk update min_buying_stock

6. `resources/views/produk-detail.blade.php`
   - Tampilkan info minimal quantity

7. `resources/views/negosiasi.blade.php`
   - Info box tentang minimal quantity

8. `database/seeders/DatabaseSeeder.php`
   - Tambah test data untuk min_buying_stock

## Konfigurasi Default

### Migration
```php
$table->integer('min_buying_stock')->default(1)->comment('Minimal quantity untuk bisa tawar menawar');
```

### Seeder
```php
'min_buying_stock' => 5, // Terpal 2x3
'min_buying_stock' => 3, // Terpal 3x4
'min_buying_stock' => 2, // Terpal 4x6
```

## Status
✅ **SELESAI** - Fitur minimal quantity untuk tawar menawar sudah diimplementasikan dan siap digunakan.
