# Fitur Validasi Limit Hutang 10 Juta

## Overview
Fitur ini memastikan customer tidak dapat berhutang melebihi limit Rp 10.000.000 sesuai dengan alur bisnis yang diinginkan.

## Implementasi

### 1. Validasi di Checkout
- **File**: `app/Http/Controllers/InvoiceController.php`
- **Method**: `storeFromCheckout()`
- **Fitur**: 
  - Validasi total hutang customer saat ini
  - Cek apakah transaksi baru akan melebihi limit 10 juta
  - Block transaksi jika limit terlampaui

### 2. Validasi di Halaman Checkout
- **File**: `app/Http/Controllers/CheckoutController.php`
- **Method**: `index()`
- **Fitur**:
  - Disable radio button "Bayar Nanti (Hutang)" jika limit terlampaui
  - Tampilkan informasi hutang saat ini dan sisa limit
  - Tampilkan pesan error jika limit terlampaui

### 3. Notifikasi di Dashboard Customer
- **File**: `resources/views/welcome.blade.php`
- **Fitur**:
  - Banner merah jika hutang melebihi limit atau jatuh tempo
  - Link langsung ke halaman detail hutang
  - Tampil otomatis di halaman utama

### 4. Helper Method
- **File**: `app/Http/Controllers/CustomerController.php`
- **Method**: `checkCustomerDebtStatus($customerId)`
- **Fitur**:
  - Method static untuk mengecek status hutang customer
  - Return array dengan informasi lengkap hutang
  - Bisa digunakan di berbagai tempat

## Alur Validasi

### 1. Customer Langganan (Bukan First Order)
```
1. Cek total hutang aktif customer
2. Hitung total hutang setelah transaksi baru
3. Jika > 10 juta → Block transaksi
4. Jika ≤ 10 juta → Izinkan transaksi
```

### 2. Customer Perdana (First Order)
```
1. Tidak boleh hutang sama sekali
2. Harus bayar lunas atau DP 50%
```

### 3. Hutang Jatuh Tempo
```
1. Cek apakah ada hutang > 30 hari
2. Jika ada → Block semua transaksi
3. Customer harus lunasi dulu
```

## Pesan Error

### Limit Terlampaui
```
"Total hutang Anda akan melebihi limit Rp 10.000.000. 
Silakan lunasi hutang terlebih dahulu atau pilih metode pembayaran lain."
```

### Hutang Jatuh Tempo
```
"Checkout dinonaktifkan karena hutang melebihi Rp 10.000.000 
atau ada hutang jatuh tempo yang belum dilunasi."
```

## Informasi yang Ditampilkan

### Di Halaman Checkout
- Hutang saat ini: Rp X.XXX.XXX
- Limit hutang: Rp 10.000.000
- Sisa limit: Rp X.XXX.XXX

### Di Dashboard
- Banner merah dengan detail hutang
- Link ke halaman detail hutang
- Pesan yang jelas tentang masalah

## Testing

### Test Case 1: Customer Perdana
1. Customer baru register
2. Coba checkout dengan metode hutang
3. **Expected**: Error "Minimal 1x transaksi lunas"

### Test Case 2: Customer Langganan - Limit Normal
1. Customer sudah pernah transaksi
2. Hutang saat ini Rp 5.000.000
3. Coba checkout Rp 3.000.000 dengan hutang
4. **Expected**: Berhasil (total Rp 8.000.000)

### Test Case 3: Customer Langganan - Limit Terlampaui
1. Customer sudah pernah transaksi
2. Hutang saat ini Rp 8.000.000
3. Coba checkout Rp 3.000.000 dengan hutang
4. **Expected**: Error limit terlampaui

### Test Case 4: Hutang Jatuh Tempo
1. Customer punya hutang > 30 hari
2. Coba checkout apapun
3. **Expected**: Error hutang jatuh tempo

## Konfigurasi

### Limit Hutang
```php
$limitHutang = 10000000; // 10 juta
```

### Jatuh Tempo
```php
// 30 hari dari tanggal transaksi
now()->gt($inv->created_at->addMonth())
```

## File yang Dimodifikasi

1. `app/Http/Controllers/InvoiceController.php`
2. `app/Http/Controllers/CheckoutController.php`
3. `app/Http/Controllers/CustomerController.php`
4. `resources/views/checkout.blade.php`
5. `resources/views/welcome.blade.php`

## Status
✅ **SELESAI** - Fitur validasi limit hutang 10 juta sudah diimplementasikan dan siap digunakan.
