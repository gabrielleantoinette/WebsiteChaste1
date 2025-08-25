# Fitur Pengurangan Stok Otomatis

## Overview
Fitur ini memastikan bahwa stok produk berkurang secara otomatis ketika customer melakukan transaksi, baik melalui pembayaran langsung maupun midtrans.

## Masalah yang Diperbaiki

### ❌ **Sebelumnya:**
- Stok tidak berkurang saat transaksi dibuat
- Customer bisa checkout meskipun stok tidak mencukupi
- Tidak ada validasi stok saat menambahkan ke cart

### ✅ **Sekarang:**
- Stok berkurang otomatis saat transaksi berhasil
- Validasi stok sebelum checkout
- Validasi stok saat menambahkan ke cart
- Logging untuk tracking pengurangan stok

## Fitur yang Ditambahkan

### 1. **Validasi Stok Saat Menambah ke Cart**
- **Lokasi**: `CartController@addItem`
- **Fungsi**: 
  - Cek stok tersedia sebelum menambah ke cart
  - Hitung total quantity (cart existing + quantity baru)
  - Tampilkan error jika stok tidak mencukupi

### 2. **Validasi Stok Sebelum Checkout**
- **Lokasi**: `InvoiceController@storeFromCheckout`
- **Fungsi**:
  - Validasi semua item di cart sebelum membuat invoice
  - Tampilkan error detail jika ada produk yang stoknya kurang
  - Mencegah checkout jika stok tidak mencukupi

### 3. **Pengurangan Stok Otomatis**
- **Lokasi**: `InvoiceController@reduceStockFromCart`
- **Fungsi**:
  - Kurangi stok di tabel `product_variants`
  - Log pengurangan stok untuk tracking
  - Handle produk custom (tidak mengurangi stok)

### 4. **Validasi Stok untuk Midtrans**
- **Lokasi**: `InvoiceController@midtransPaymentAction`
- **Fungsi**:
  - Validasi stok sebelum mengurangi (setelah pembayaran berhasil)
  - Handle kasus stok berubah selama proses pembayaran

## Alur Pengurangan Stok

### **Transaksi Non-Midtrans (COD, Transfer, Hutang)**
```
1. Customer checkout
2. Validasi stok ✅
3. Buat invoice
4. Kurangi stok ✅
5. Hapus cart
6. Kirim notifikasi
```

### **Transaksi Midtrans**
```
1. Customer checkout
2. Validasi stok ✅
3. Buat invoice
4. Redirect ke Midtrans
5. Customer bayar
6. Payment callback
7. Validasi stok lagi ✅
8. Kurangi stok ✅
9. Hapus cart
10. Kirim notifikasi
```

## Method yang Ditambahkan

### **InvoiceController**
```php
// Validasi stok sebelum checkout
private function validateStockAvailability($cartIds)

// Kurangi stok setelah transaksi berhasil
private function reduceStockFromCart($cartIds)
```

### **CartController**
```php
// Validasi stok saat menambah ke cart
public function addItem(Request $request)
```

## Logging

### **Log Pengurangan Stok**
```php
\Log::info("Stok berkurang: Product Variant ID {$cart->variant_id}, Qty: {$cart->quantity}, Stok baru: " . ($productVariant->stock - $cart->quantity));
```

### **Log Error Stok Tidak Cukup**
```php
\Log::error("Stok tidak cukup: Product Variant ID {$cart->variant_id}, Stok tersedia: {$productVariant->stock}, Qty diminta: {$cart->quantity}");
```

## Pesan Error

### **Stok Tidak Cukup di Cart**
```
"Stok tidak mencukupi. Stok tersedia: 5, Total yang diminta: 10"
```

### **Stok Tidak Cukup saat Checkout**
```
"Stok tidak mencukupi untuk produk berikut: Terpal A2 - Merah (Stok: 3, Diminta: 5), Terpal B1 - Biru (Stok: 0, Diminta: 2)"
```

## Keamanan

### **Race Condition Protection**
- Validasi stok sebelum dan sesudah pembayaran
- Logging untuk tracking perubahan stok
- Handle kasus stok berubah selama proses pembayaran

### **Data Integrity**
- Validasi stok >= quantity sebelum mengurangi
- Rollback jika terjadi error
- Logging untuk audit trail

## Testing

### **Test Cases**
1. **Stok Cukup**: Customer checkout dengan stok tersedia
2. **Stok Tidak Cukup**: Customer checkout dengan stok kurang
3. **Midtrans Success**: Pembayaran midtrans berhasil
4. **Midtrans Failed**: Pembayaran midtrans gagal
5. **Race Condition**: Multiple checkout bersamaan

### **Manual Testing**
```bash
# 1. Tambah produk ke cart
# 2. Cek stok di database
# 3. Lakukan checkout
# 4. Cek stok berkurang
# 5. Cek log pengurangan stok
```

## Monitoring

### **Log Files**
- `storage/logs/laravel.log` - Log pengurangan stok
- Monitor error stok tidak cukup
- Track perubahan stok per transaksi

### **Database Monitoring**
```sql
-- Cek stok produk
SELECT p.name, pv.color, pv.stock 
FROM products p 
JOIN product_variants pv ON p.id = pv.product_id 
WHERE pv.stock < 10;

-- Cek transaksi hari ini
SELECT COUNT(*) as total_transaksi 
FROM hinvoice 
WHERE DATE(created_at) = CURDATE();
```

## Troubleshooting

### **Stok Tidak Berkurang**
1. Cek log error di `storage/logs/laravel.log`
2. Pastikan transaksi berhasil dibuat
3. Cek apakah method `reduceStockFromCart` dipanggil
4. Verifikasi data di tabel `product_variants`

### **Error "Stok Tidak Cukup"**
1. Cek stok tersedia di database
2. Pastikan tidak ada transaksi bersamaan
3. Cek log untuk detail error
4. Verifikasi quantity di cart

### **Midtrans Stok Error**
1. Cek log setelah payment callback
2. Pastikan validasi stok kedua berhasil
3. Handle kasus stok berubah selama pembayaran
4. Implementasi refund jika diperlukan

## Future Improvements

### **Fitur yang Bisa Ditambahkan**
1. **Reservation System**: Reserve stok saat checkout
2. **Stock Alert**: Notifikasi stok rendah
3. **Stock History**: Track perubahan stok
4. **Auto Restock**: Otomatis restock saat stok rendah
5. **Stock Lock**: Lock stok selama proses pembayaran

### **Performance Optimization**
1. **Database Index**: Index pada kolom stock
2. **Caching**: Cache stok untuk performa
3. **Batch Update**: Update stok dalam batch
4. **Queue**: Async stok update untuk performa

## File yang Dimodifikasi

### **Controllers**
- `app/Http/Controllers/InvoiceController.php`
- `app/Http/Controllers/CartController.php`

### **Routes**
- Tidak ada perubahan (menggunakan route existing)

### **Views**
- Tidak ada perubahan (menggunakan view existing)

### **Database**
- Tidak ada perubahan struktur
- Hanya update data di tabel `product_variants`
