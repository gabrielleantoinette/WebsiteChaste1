# Fitur Notifikasi Customer - Website CHASTE

## Overview
Fitur notifikasi untuk customer telah diimplementasikan dengan lengkap dan konsisten dengan role lain. Customer akan menerima notifikasi real-time terkait pesanan, pembayaran, pengiriman, retur, dan promo.

## Jenis Notifikasi untuk Customer

### 1. Pesanan Baru
- **Trigger**: Customer berhasil membuat pesanan
- **Icon**: `fas fa-shopping-bag`
- **Pesan**: "Pesanan Anda dengan ID #[ID] telah berhasil dibuat. Total pembayaran: Rp [Amount]"
- **Action URL**: `/transaksi/detail/[ID]`

### 2. Pembayaran Diterima
- **Trigger**: Pembayaran customer diterima dan dikonfirmasi
- **Icon**: `fas fa-credit-card`
- **Pesan**: "Pembayaran Anda sebesar Rp [Amount] telah diterima dan diproses."
- **Action URL**: `/transaksi/detail/[Order ID]`

### 3. Status Pesanan Diperbarui
- **Trigger**: Status pesanan berubah (diproses, dikemas, dikirim, diterima)
- **Icon**: `fas fa-shipping-fast`
- **Pesan**: "Pesanan Anda sedang diproses" / "Pesanan Anda telah dikirim" / dll
- **Action URL**: `/transaksi/detail/[ID]`

### 4. Pengiriman
- **Trigger**: Pesanan dikirim oleh driver
- **Icon**: `fas fa-truck`
- **Pesan**: "Pesanan Anda dengan ID #[ID] telah dikirim oleh kurir. Estimasi tiba dalam 1-3 hari kerja."
- **Action URL**: `/transaksi/detail/[ID]`

### 5. Pesanan Diterima
- **Trigger**: Pesanan berhasil diterima customer
- **Icon**: `fas fa-check-circle`
- **Pesan**: "Pesanan Anda dengan ID #[ID] telah diterima. Silakan berikan penilaian untuk pengalaman berbelanja Anda."
- **Action URL**: `/transaksi/beri-penilaian`

### 6. Retur Diproses
- **Trigger**: Permintaan retur customer diproses
- **Icon**: `fas fa-undo-alt`
- **Pesan**: "Permintaan retur Anda untuk pesanan #[Order ID] sedang diproses oleh tim kami."
- **Action URL**: `/retur/[ID]`

### 7. Retur Disetujui
- **Trigger**: Permintaan retur disetujui admin
- **Icon**: `fas fa-check`
- **Pesan**: "Permintaan retur Anda untuk pesanan #[Order ID] telah disetujui. Tim kami akan menghubungi Anda untuk pengambilan barang."
- **Action URL**: `/retur/[ID]`

### 8. Retur Ditolak
- **Trigger**: Permintaan retur ditolak admin
- **Icon**: `fas fa-times-circle`
- **Pesan**: "Permintaan retur Anda untuk pesanan #[Order ID] tidak dapat diproses. Alasan: [Reason]"
- **Action URL**: `/retur/[ID]`

### 9. Hutang Jatuh Tempo
- **Trigger**: Hutang customer mendekati atau sudah jatuh tempo
- **Icon**: `fas fa-exclamation-triangle`
- **Pesan**: "Hutang Anda sebesar Rp [Amount] jatuh tempo dalam [Days] hari."
- **Action URL**: `/profile/hutang`

### 10. Promo Spesial
- **Trigger**: Admin mengirim notifikasi promo
- **Icon**: `fas fa-gift`
- **Pesan**: Pesan promo yang dikirim admin
- **Action URL**: `/produk` atau URL custom

### 11. Stok Tersedia
- **Trigger**: Produk yang di-wishlist tersedia kembali
- **Icon**: `fas fa-box`
- **Pesan**: "Produk [Product Name] yang Anda tunggu sudah tersedia kembali!"
- **Action URL**: `/produk/[ID]`

## Fitur UI/UX

### 1. Badge Counter
- Badge merah dengan angka muncul di pojok kanan atas bell icon
- Auto-update setiap 30 detik
- Menampilkan jumlah notifikasi yang belum dibaca
- Animasi pulse untuk menarik perhatian

### 2. Popover Notifikasi
- Klik bell icon untuk membuka popover
- Menampilkan daftar notifikasi terbaru (maksimal 10)
- Notifikasi yang belum dibaca memiliki background teal
- Tombol "Tandai Dibaca" untuk setiap notifikasi
- Tombol "Tandai Semua Dibaca" untuk menandai semua sekaligus

### 3. Real-time Updates
- Counter berkurang secara real-time saat notifikasi ditandai dibaca
- Badge hilang otomatis ketika semua notifikasi sudah dibaca
- Auto-refresh setiap 30 detik untuk sinkronisasi

## Cara Kerja

### 1. Mark as Read Individual
```javascript
// Ketika tombol "Tandai Dibaca" diklik
- Counter berkurang 1 secara real-time
- Background notifikasi berubah dari teal ke normal
- Tombol "Tandai Dibaca" hilang
- Badge counter diupdate dari server
```

### 2. Mark All as Read
```javascript
// Ketika tombol "Tandai Semua Dibaca" diklik
- Semua notifikasi ditandai sebagai dibaca
- Badge counter hilang
- Semua tombol "Tandai Dibaca" hilang
- Background semua notifikasi berubah
```

### 3. Auto-refresh
```javascript
// Setiap 30 detik
- Fetch jumlah notifikasi yang belum dibaca
- Update badge counter
- Tambah animasi pulse jika ada notifikasi baru
```

## Implementasi Teknis

### 1. NotificationService
- Method `sendToCustomer()` untuk mengirim notifikasi ke customer
- Method `notifyOrderCreated()`, `notifyPaymentReceived()`, dll
- Support untuk icon, priority, dan action URL

### 2. NotificationController
- Method `markAsRead()` untuk menandai notifikasi sebagai dibaca
- Method `markAllAsRead()` untuk menandai semua sebagai dibaca
- Method `getUnreadCount()` untuk mendapatkan jumlah notifikasi yang belum dibaca
- Method `latest()` untuk mendapatkan notifikasi terbaru

### 3. Database
- Tabel `notifications` dengan kolom:
  - `recipient_type`: 'customer'
  - `recipient_id`: ID customer
  - `is_read`: status dibaca
  - `read_at`: waktu dibaca
  - `icon`: icon notifikasi
  - `priority`: prioritas (low/normal/high/urgent)

## Commands untuk Testing

### 1. Buat Notifikasi Test
```bash
# Buat 5 notifikasi test untuk customer
php artisan notifications:test-customer {customer_id}
```

### 2. Kirim Promo ke Semua Customer
```bash
# Kirim promo dengan pesan custom
php artisan notifications:send-promo --message="Promo spesial 50% off!" --url="/produk"
```

### 3. Notifikasi Stok Tersedia
```bash
# Kirim notifikasi stok tersedia untuk produk tertentu
php artisan notifications:stock-available {product_id}
```

### 4. Cek Hutang Jatuh Tempo
```bash
# Cek dan kirim notifikasi hutang jatuh tempo
php artisan invoices:check-due-dates
```

## Maintenance

### 1. Clean Old Notifications
```bash
# Bersihkan notifikasi yang sudah dibaca lebih dari 30 hari
php artisan notifications:clean
```

### 2. Schedule Cleanup
Tambahkan ke `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('notifications:clean')->daily();
    $schedule->command('invoices:check-due-dates')->daily();
}
```

## Troubleshooting

### 1. Counter Tidak Berkurang
- Pastikan CSRF token ada di meta tag
- Cek console browser untuk error JavaScript
- Pastikan route notifications bisa diakses

### 2. Notifikasi Tidak Muncul
- Cek apakah customer sudah login
- Cek apakah notifikasi tersimpan di database
- Cek apakah JavaScript berjalan dengan benar

### 3. Badge Tidak Update
- Cek apakah auto-refresh berjalan (30 detik)
- Cek apakah API `/notifications/unread-count` berfungsi
- Cek apakah session customer valid

## Security
- Customer hanya bisa melihat notifikasinya sendiri
- Validasi recipient_id untuk setiap aksi
- CSRF protection untuk semua request
- Session validation untuk setiap akses

## Performance
- Index pada kolom `recipient_type`, `recipient_id`, `is_read`
- Pagination untuk daftar notifikasi (maksimal 10)
- Auto-cleanup notifikasi lama
- Lazy loading untuk notifikasi
