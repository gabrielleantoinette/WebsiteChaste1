# Fitur Surat Perintah Kerja (Work Orders) - Website Chaste

## Overview
Fitur Surat Perintah Kerja adalah sistem digital untuk mengelola surat perintah potong/produksi dari role admin ke role gudang. Fitur ini menggantikan sistem manual dengan surat kertas menjadi sistem digital yang terintegrasi dengan notifikasi real-time. Fitur ini terintegrasi dengan dashboard gudang yang sudah ada, sehingga staff gudang dapat melihat semua tugas dalam satu tempat.

## Fitur Utama

### 1. Pembuatan Surat Perintah Kerja (Admin)
- **Form Pembuatan**: Admin dapat membuat surat perintah kerja baru
- **Detail Item**: Menambahkan item dengan ukuran, bahan, warna, dan keterangan
- **Penugasan**: Menugaskan surat perintah ke staff gudang tertentu
- **Deadline**: Menetapkan tanggal deadline pengerjaan
- **Notifikasi**: Otomatis mengirim notifikasi ke staff gudang

### 2. Manajemen Surat Perintah (Admin)
- **Daftar Surat Perintah**: Melihat semua surat perintah kerja
- **Statistik**: Dashboard dengan statistik progress
- **Edit**: Mengedit informasi surat perintah (kecuali item)
- **Monitoring**: Memantau progress pengerjaan real-time

### 3. Pengerjaan Surat Perintah (Gudang)
- **Dashboard Gudang**: Melihat surat perintah yang ditugaskan di dashboard gudang yang sudah ada
- **Update Status**: Mengupdate status item satu per satu
- **Progress Tracking**: Melihat progress pengerjaan
- **Notifikasi**: Menerima notifikasi surat perintah baru

### 4. Format Surat Perintah
Format surat perintah mengikuti contoh manual dengan kolom:
- **No.**: Nomor urut item
- **UKURAN + BAHAN**: Ukuran dan bahan (Terpal)
- **WARNA**: Warna produk
- **KETERANGAN**: Catatan khusus (Dikoli, dll)

## Struktur Database

### Tabel `work_orders`
```sql
- id (primary key)
- code (kode surat perintah, format: SP-001)
- order_date (tanggal surat perintah)
- due_date (tanggal deadline, nullable)
- description (deskripsi, nullable)
- status (dibuat/dikerjakan/selesai/dibatalkan)
- created_by (admin yang membuat)
- assigned_to (staff gudang yang ditugaskan)
- started_at (waktu mulai dikerjakan, nullable)
- completed_at (waktu selesai, nullable)
- notes (catatan tambahan, nullable)
- created_at, updated_at
```

### Tabel `work_order_items`
```sql
- id (primary key)
- work_order_id (foreign key ke work_orders)
- size_material (ukuran + bahan)
- color (warna)
- quantity (jumlah)
- remarks (keterangan, nullable)
- status (pending/in_progress/completed)
- completed_quantity (jumlah yang sudah selesai)
- notes (catatan item, nullable)
- created_at, updated_at
```

## Alur Kerja

### 1. Admin Membuat Surat Perintah
1. Admin login dan akses menu "Surat Perintah Kerja"
2. Klik "Buat Surat Perintah"
3. Isi form dengan detail item yang akan dipotong
4. Pilih staff gudang yang ditugaskan
5. Set tanggal deadline (opsional)
6. Submit form
7. Sistem generate kode otomatis (SP-001, SP-002, dst)
8. Notifikasi dikirim ke staff gudang

### 2. Gudang Menerima dan Mengerjakan
1. Staff gudang login dan akses dashboard work order
2. Melihat surat perintah baru di daftar
3. Klik "Lihat Detail" untuk melihat surat perintah lengkap
4. Update status item satu per satu sesuai progress
5. Update status surat perintah secara keseluruhan
6. Admin mendapat notifikasi update status

### 3. Monitoring Progress
1. Admin dapat melihat progress real-time
2. Progress bar menunjukkan persentase penyelesaian
3. Timeline menampilkan waktu setiap status
4. Statistik dashboard menampilkan overview

## Status Pengerjaan

### Status Surat Perintah
- **Dibuat**: Surat perintah baru dibuat
- **Dikerjakan**: Sedang dalam proses pengerjaan
- **Selesai**: Semua item telah selesai
- **Dibatalkan**: Surat perintah dibatalkan

### Status Item
- **Pending**: Item belum dikerjakan
- **In Progress**: Item sedang dikerjakan
- **Completed**: Item telah selesai

## Notifikasi

### Notifikasi untuk Gudang
- **Work Order Baru**: Ketika admin membuat surat perintah baru
- **Icon**: `fas fa-clipboard-list`
- **Priority**: High

### Notifikasi untuk Admin
- **Status Update**: Ketika gudang mengupdate status
- **Icon**: `fas fa-clipboard-check`
- **Priority**: Normal

## Akses dan Permission

### Role Admin
- ✅ Membuat surat perintah kerja
- ✅ Melihat semua surat perintah
- ✅ Edit surat perintah (kecuali item)
- ✅ Monitoring progress
- ✅ Menerima notifikasi update

### Role Gudang
- ✅ Melihat surat perintah yang ditugaskan
- ✅ Update status item
- ✅ Update status surat perintah
- ✅ Menerima notifikasi surat perintah baru

### Role Owner
- ✅ Semua akses admin
- ✅ Monitoring semua surat perintah

## File yang Dibuat/Dimodifikasi

### File Baru
- `database/migrations/2025_01_15_000001_create_work_orders_table.php`
- `database/migrations/2025_01_15_000002_create_work_order_items_table.php`
- `app/Models/WorkOrder.php`
- `app/Models/WorkOrderItem.php`
- `app/Http/Controllers/WorkOrderController.php`
- `resources/views/admin/work-orders/index.blade.php`
- `resources/views/admin/work-orders/create.blade.php`
- `resources/views/admin/work-orders/show.blade.php`
- `resources/views/admin/work-orders/edit.blade.php`

### File Dimodifikasi
- `routes/web.php` - Menambah routes work order
- `resources/views/layouts/admin.blade.php` - Menambah menu work order
- `app/Services/NotificationService.php` - Menambah notifikasi work order
- `app/Http/Controllers/GudangController.php` - Mengintegrasikan work order ke dashboard gudang
- `resources/views/admin/dashboardgudang.blade.php` - Menambahkan section work order

## Cara Penggunaan

### Untuk Admin
1. Login sebagai admin
2. Akses menu "Surat Perintah Kerja" di sidebar
3. Klik "Buat Surat Perintah" untuk membuat baru
4. Isi form dengan detail item yang akan dipotong
5. Pilih staff gudang dan set deadline
6. Submit form
7. Monitor progress di halaman index

### Untuk Gudang
1. Login sebagai staff gudang
2. Akses "Dashboard Gudang" di sidebar
3. Lihat section "Surat Perintah Kerja" di dashboard
4. Klik "Detail" untuk melihat surat perintah
5. Update status item satu per satu
6. Update status surat perintah secara keseluruhan

## Keunggulan Sistem Digital

### Dibanding Sistem Manual
1. **Real-time Tracking**: Progress dapat dimonitor secara real-time
2. **Notifikasi Otomatis**: Tidak perlu komunikasi manual
3. **Data Terpusat**: Semua data tersimpan dengan aman
4. **Laporan Otomatis**: Statistik dan laporan otomatis
5. **Audit Trail**: Riwayat perubahan tersimpan lengkap
6. **Akses Multi-user**: Bisa diakses dari mana saja
7. **Backup Otomatis**: Data tersimpan dengan aman

### Integrasi dengan Sistem Existing
- Menggunakan sistem notifikasi yang sudah ada
- Terintegrasi dengan role dan permission existing
- Menggunakan layout dan styling yang konsisten
- Mengikuti pola desain aplikasi yang sudah ada

## Troubleshooting

### Masalah Umum
1. **Notifikasi tidak muncul**: Pastikan staff gudang sudah login
2. **Progress tidak update**: Refresh halaman atau cek koneksi
3. **Form tidak bisa submit**: Pastikan semua field required terisi
4. **Akses ditolak**: Pastikan role user sesuai

### Debug
- Cek log Laravel di `storage/logs/laravel.log`
- Cek database untuk memastikan data tersimpan
- Cek notifikasi di tabel `notifications`

## Update dan Maintenance

### Backup Data
- Backup tabel `work_orders` dan `work_order_items`
- Backup file migration untuk rollback jika diperlukan

### Update Fitur
- Tambahkan fitur export PDF surat perintah
- Tambahkan fitur foto bukti pengerjaan
- Tambahkan fitur komentar/chat antar role
- Tambahkan fitur template surat perintah

## Support
Untuk pertanyaan atau masalah terkait fitur Surat Perintah Kerja, silakan hubungi tim development.
