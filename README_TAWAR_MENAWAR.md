# Perbaikan Sistem Tawar Menawar

## Overview
Sistem tawar menawar telah diperbaiki untuk memberikan pengalaman yang lebih realistis dan informatif bagi customer. Tawar menawar **tidak berlaku untuk produk custom** sesuai dengan alur bisnis.

## Perbaikan yang Dilakukan

### 1. Logika Counter Offer yang Lebih Realistis
- **File**: `app/Http/Controllers/NegotiationController.php`
- **Perubahan**: 
  - Logika counter offer berdasarkan persentase tawaran customer
  - Validasi harga minimal yang proper
  - Pesan error yang lebih informatif

### 2. Validasi Harga yang Lebih Fleksibel
- **Default**: 65% dari harga normal jika `min_price` tidak diisi
- **Custom**: Menggunakan `min_price` dari database jika ada
- **Validasi**: Block tawaran di bawah 50% dari harga normal (tidak reveal harga minimal)

### 3. Tips Negosiasi yang Lebih Baik
- **File**: `resources/views/produk-detail.blade.php`
- **Fitur**: Tips tawar 10-30% dari harga normal
- **File**: `resources/views/negosiasi.blade.php`
- **Fitur**: Tips negosiasi dan placeholder yang informatif

### 4. UI/UX yang Lebih Baik
- Placeholder input yang informatif
- Tampilan hasil negosiasi yang lebih rapi
- Tombol Deal dengan informasi harga final

## Alur Tawar Menawar

### 1. Customer Membuka Halaman Produk
```
- Lihat harga normal: Rp X.XXX.XXX
- Lihat harga minimal: Rp X.XXX.XXX (70% dari harga normal)
- Klik "Tawar Harga"
```

### 2. Customer Masuk ke Halaman Negosiasi
```
- Lihat informasi harga normal dan minimal
- Input tawaran (minimal sesuai harga minimal)
- Submit tawaran
```

### 3. Sistem Validasi
```
- Jika tawaran < harga minimal → Error
- Jika tawaran ≥ harga normal → Error (tidak perlu tawar)
- Jika valid → Proses counter offer
```

### 4. Logika Counter Offer yang Dinamis
```
Discount Percentage = ((Harga Normal - Tawaran Customer) / Harga Normal) × 100

Jika Discount ≥ 40% (Tawaran sangat agresif):
  Counter = Harga Normal - (15-20% random)
Jika Discount ≥ 25% (Tawaran agresif):
  Counter = Harga Normal - (10-15% random)
Jika Discount ≥ 15% (Tawaran sedang):
  Counter = Harga Normal - (5-10% random)
Jika Discount < 15% (Tawaran ringan):
  Counter = Harga Normal - (3-5% random)

Pastikan Counter > Tawaran Customer
Pastikan Counter ≥ Harga Minimal
```

### 5. Hasil Negosiasi
```
- Maksimal 3 kali tawar
- Tawaran ke-3 = Final Price
- Customer bisa Deal atau Reset
```

## Pesan Error

### Tawaran Terlalu Rendah
```
"Tawaran terlalu rendah. Silakan tawar minimal 50% dari harga normal."
```

### Tawaran Sudah Normal
```
"Tawaran sudah mencapai atau melebihi harga normal. Tidak perlu tawar lagi."
```

### Maksimal Tawar
```
"Sudah mencapai maksimal 3 kali tawar."
```

## Contoh Skenario

### Produk: Terpal A5 - Rp 1.000
- **Harga Normal**: Rp 1.000
- **Harga Minimal**: Rp 650 (65%)

### Skenario 1: Tawaran Rendah
- **Customer**: Rp 400 (diskon 60%)
- **Sistem**: Error "Tawaran terlalu rendah"

### Skenario 2: Tawaran Agresif
- **Customer**: Rp 600 (diskon 40%)
- **Sistem**: Counter Rp 800-850 (diskon 15-20% random)

### Skenario 3: Tawaran Sedang
- **Customer**: Rp 750 (diskon 25%)
- **Sistem**: Counter Rp 850-900 (diskon 10-15% random)

### Skenario 4: Tawaran Ringan
- **Customer**: Rp 900 (diskon 10%)
- **Sistem**: Counter Rp 950-970 (diskon 3-5% random)

## Produk Custom

### Tidak Ada Tawar Menawar
- Custom terpal menggunakan halaman terpisah (`custom.blade.php`)
- Tidak ada tombol "Tawar Harga"
- Harga dihitung otomatis berdasarkan ukuran dan add-ons
- Menggunakan harga normal tanpa diskon

## File yang Dimodifikasi

1. `app/Http/Controllers/NegotiationController.php`
   - Logika counter offer yang lebih realistis
   - Validasi harga minimal
   - Pesan error yang informatif

2. `resources/views/produk-detail.blade.php`
   - Tampilkan harga minimal

3. `resources/views/negosiasi.blade.php`
   - Informasi harga yang lebih jelas
   - UI yang lebih rapi
   - Placeholder yang informatif

## Testing

### Test Case 1: Tawaran Valid
1. Buka produk dengan harga Rp 5.000
2. Tawar Rp 4.000
3. **Expected**: Sistem counter dengan harga yang masuk akal

### Test Case 2: Tawaran Terlalu Rendah
1. Buka produk dengan harga Rp 5.000
2. Tawar Rp 2.000
3. **Expected**: Error "Tawaran terlalu rendah"

### Test Case 3: Tawaran Sudah Normal
1. Buka produk dengan harga Rp 5.000
2. Tawar Rp 5.000
3. **Expected**: Error "Tidak perlu tawar lagi"

### Test Case 4: Custom Terpal
1. Buka halaman custom terpal
2. **Expected**: Tidak ada tombol tawar menawar

## Konfigurasi

### Harga Minimal Default
```php
$min = $product->min_price ?? ($product->price * 0.65); // 65% dari harga normal
```

### Persentase Counter Offer (Random)
```php
// Tawaran sangat agresif (≥40%): Counter 15-20% random
// Tawaran agresif (≥25%): Counter 10-15% random
// Tawaran sedang (≥15%): Counter 5-10% random
// Tawaran ringan (<15%): Counter 3-5% random
```

## Status
✅ **SELESAI** - Sistem tawar menawar telah diperbaiki dan siap digunakan.
