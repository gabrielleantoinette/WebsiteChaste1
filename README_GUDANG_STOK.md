# Fitur Stok Barang untuk Role Gudang

## Overview
Fitur ini memberikan akses kepada staf gudang untuk melihat dan mengelola stok barang yang tersedia di gudang, serta membuat laporan stok harian/mingguan/bulanan/tahunan.

## Fitur yang Tersedia

### 1. Halaman Stok Barang (`/gudang/stok-barang`)
- **Deskripsi**: Menampilkan stok barang yang tersedia di gudang
- **Akses**: Hanya untuk role `gudang`
- **Fitur**:
  - Ringkasan total produk, custom materials, dan stok tersedia
  - Daftar produk regular dengan detail stok per warna
  - Daftar custom materials dengan detail stok per warna
  - Indikator status stok (Rendah ≤10, Menengah ≤50, Aman >50)
  - Link ke laporan stok dan export PDF

### 2. Laporan Stok (`/gudang/laporan-stok`)
- **Deskripsi**: Laporan pergerakan stok masuk dan keluar
- **Akses**: Hanya untuk role `gudang`
- **Fitur**:
  - Filter periode: Harian, Mingguan, Bulanan, Tahunan
  - Ringkasan stok saat ini, masuk, keluar, dan sisa
  - Detail stok saat ini per produk/material
  - Detail stok masuk (dari produksi, retur)
  - Detail stok keluar (penjualan, bahan baku, barang rusak)
  - Export ke PDF

### 3. Export PDF Laporan Stok (`/gudang/laporan-stok/export-pdf`)
- **Deskripsi**: Export laporan stok ke format PDF
- **Akses**: Hanya untuk role `gudang`
- **Fitur**:
  - Laporan dalam format PDF yang rapi
  - Ringkasan stok di halaman pertama
  - Detail stok saat ini, masuk, dan keluar
  - Nama file: `laporan-stok-{periode}-{tanggal}.pdf`

## Sumber Data Stok

### Stok Masuk
1. **Work Orders Selesai**: Produk yang selesai diproduksi
2. **Retur Customer**: Barang yang dikembalikan customer

### Stok Keluar
1. **Penjualan**: Barang yang terjual ke customer
2. **Bahan Baku**: Bahan yang digunakan untuk work orders
3. **Barang Rusak**: Produk yang rusak dan tidak bisa dijual

## Cara Akses

### Melalui Dashboard Gudang
1. Login sebagai user dengan role `gudang`
2. Akses dashboard gudang
3. Klik tombol "📦 Stok Barang" atau "📊 Laporan Stok"

### Melalui Sidebar
1. Login sebagai user dengan role `gudang`
2. Di sidebar kiri, klik menu:
   - "📦 Stok Barang" untuk melihat stok
   - "📊 Laporan Stok" untuk melihat laporan

## Keamanan

### Middleware
- `LoggedIn`: Memastikan user sudah login
- `GudangRole`: Memastikan hanya role `gudang` yang bisa akses

### Akses Terbatas
- Hanya user dengan role `gudang` yang bisa mengakses fitur ini
- User lain akan diarahkan ke dashboard dengan pesan error

## File yang Dibuat/Dimodifikasi

### Controllers
- `app/Http/Controllers/GudangController.php` - Menambahkan method untuk stok

### Views
- `resources/views/admin/gudang/stok-barang.blade.php` - Halaman stok barang
- `resources/views/admin/gudang/laporan-stok.blade.php` - Halaman laporan stok
- `resources/views/exports/laporan_stok_pdf.blade.php` - Template PDF

### Routes
- `routes/web.php` - Menambahkan routes untuk fitur stok

### Middleware
- `app/Http/Middleware/GudangRole.php` - Middleware untuk role gudang
- `bootstrap/app.php` - Mendaftarkan middleware

### Layout
- `resources/views/layouts/admin.blade.php` - Menambahkan menu di sidebar
- `resources/views/admin/dashboardgudang.blade.php` - Menambahkan link di dashboard

## Penggunaan

### Melihat Stok Barang
1. Akses halaman stok barang
2. Lihat ringkasan total stok
3. Scroll untuk melihat detail per produk
4. Perhatikan indikator warna untuk status stok

### Membuat Laporan Stok
1. Akses halaman laporan stok
2. Pilih periode (harian/mingguan/bulanan/tahunan)
3. Pilih tanggal jika diperlukan
4. Klik "Filter" untuk melihat laporan
5. Klik "📄 Export PDF" untuk download laporan

## Troubleshooting

### Error "Akses ditolak"
- Pastikan user memiliki role `gudang`
- Logout dan login kembali jika diperlukan

### Error PDF tidak terdownload
- Pastikan package `barryvdh/laravel-dompdf` sudah terinstall
- Cek permission folder storage

### Data stok tidak muncul
- Pastikan ada data produk di database
- Cek relasi antara tabel products, product_variants, dll

### Error "Column not found: nama_custom"
- **Penyebab**: Query menggunakan kolom yang tidak ada di tabel cart
- **Solusi**: Kolom yang benar adalah `kebutuhan_custom` (bukan `nama_custom`)
- **Status**: ✅ Sudah diperbaiki di GudangController.php

### Error "Unknown column in field list"
- **Penyebab**: Query mencoba mengakses kolom yang tidak ada di database
- **Solusi**: Pastikan semua kolom yang direferensikan ada di tabel yang sesuai
- **Kolom yang benar di tabel cart**:
  - `kebutuhan_custom` (untuk nama produk custom)
  - `warna_custom` (untuk warna produk custom)
  - `harga_custom` (untuk harga produk custom)

### Data Stok Keluar Tidak Muncul
- **Penyebab**: Query menggunakan tabel `cart` yang sudah dihapus setelah transaksi
- **Solusi**: Menggunakan tabel `dinvoice` untuk data transaksi yang sudah berhasil
- **Status**: ✅ Sudah diperbaiki di GudangController.php
- **Perubahan**: Query stok keluar sekarang menggunakan `dinvoice` join dengan `hinvoice`

## Catatan Teknis

### Database
- Menggunakan tabel `products`, `product_variants`, `custom_materials`, `custom_material_variants`
- Menggunakan tabel `work_orders`, `work_order_items` untuk stok masuk
- Menggunakan tabel `cart`, `hinvoice` untuk stok keluar
- Menggunakan tabel `damaged_products` untuk barang rusak

### Performance
- Query dioptimasi dengan eager loading
- Data di-cache untuk performa yang lebih baik
- Pagination untuk data yang besar (jika diperlukan)

### Maintenance
- Laporan disimpan dalam format PDF untuk arsip
- Data stok real-time dari database
- Backup otomatis melalui sistem database
