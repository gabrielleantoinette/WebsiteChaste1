# Fitur Notifikasi Keuangan - Website Chaste

## Overview
Fitur notifikasi untuk role keuangan telah diimplementasikan dengan lengkap. Staf keuangan akan menerima notifikasi real-time terkait pembayaran, invoice jatuh tempo, dan aktivitas keuangan lainnya.

## Jenis Notifikasi untuk Keuangan

### 1. Pembayaran Baru
- **Trigger**: Ada pembayaran baru yang diterima
- **Icon**: `fas fa-credit-card`
- **Pesan**: "Pembayaran baru sebesar Rp [Amount] telah diterima"
- **Action URL**: `/admin/payments/[ID]`
- **Priority**: High

### 2. Pembayaran Pending
- **Trigger**: Ada pembayaran yang masih pending
- **Icon**: `fas fa-clock`
- **Pesan**: "Pembayaran pending sebesar Rp [Amount] dari [Customer Name] untuk invoice [Invoice Code]"
- **Action URL**: `/admin/keuangan/detail/[ID]`
- **Priority**: High

### 3. Invoice Jatuh Tempo
- **Trigger**: Invoice mendekati jatuh tempo (1-3 hari)
- **Icon**: `fas fa-calendar-times`
- **Pesan**: "Invoice [Code] dari [Customer Name] jatuh tempo dalam [Days] hari. Sisa hutang: Rp [Amount]"
- **Action URL**: `/admin/keuangan/detail/[ID]`
- **Priority**: Normal/High (berdasarkan hari tersisa)

### 4. Invoice Jatuh Tempo Hari Ini
- **Trigger**: Invoice jatuh tempo hari ini
- **Icon**: `fas fa-exclamation-triangle`
- **Pesan**: "Invoice [Code] dari [Customer Name] jatuh tempo hari ini! Sisa hutang: Rp [Amount]"
- **Action URL**: `/admin/keuangan/detail/[ID]`
- **Priority**: Urgent

## Implementasi Teknis

### 1. Layout Keuangan
File: `resources/views/layouts/keuangan.blade.php`
- Bell icon dengan badge notifikasi
- Popover untuk menampilkan daftar notifikasi
- Auto-refresh setiap 30 detik
- Sidebar dengan menu keuangan

### 2. NotificationService
File: `app/Services/NotificationService.php`
- Method `notifyPayment()` - Kirim ke keuangan dan admin
- Method `notifyPaymentPending()` - Kirim ke keuangan
- Method `notifyInvoiceDueDate()` - Kirim ke keuangan
- Method `notifyInvoiceDueToday()` - Kirim ke keuangan

### 3. NotificationController
File: `app/Http/Controllers/NotificationController.php`
- Support untuk role keuangan di semua method
- `getUnreadCount()` - Untuk badge count
- `latest()` - Untuk popover content
- `markAsRead()` - Tandai sebagai dibaca

### 4. Dashboard Keuangan
File: `resources/views/admin/dashboardkeuangan.blade.php`
- Menggunakan layout keuangan dengan notifikasi
- Menampilkan reminder hutang jatuh tempo

## Cara Kerja

### 1. Badge Notifikasi
- Badge merah dengan angka muncul di pojok kanan atas bell icon
- Auto-update setiap 30 detik
- Menampilkan jumlah notifikasi yang belum dibaca

### 2. Popover Notifikasi
- Klik bell icon untuk membuka popover
- Menampilkan 10 notifikasi terbaru
- Notifikasi belum dibaca memiliki background teal
- Tombol "Tandai Dibaca" untuk setiap notifikasi

### 3. Auto-Notification System
- Command `invoices:check-due-dates` untuk cek invoice jatuh tempo
- Bisa dijadwalkan dengan cron job
- Mengirim notifikasi otomatis untuk invoice jatuh tempo

## Commands untuk Testing

### 1. Buat Notifikasi Test
```bash
php artisan notifications:create-test-keuangan
```

### 2. Cek Invoice Jatuh Tempo
```bash
php artisan invoices:check-due-dates
```

### 3. Test Mark As Read
```bash
php artisan notifications:test-mark-read-direct [notification_id]
```

## Integrasi dengan Sistem

### 1. Pembayaran Baru
Ketika ada pembayaran baru, sistem akan otomatis mengirim notifikasi ke keuangan:
```php
$notificationService->notifyPayment($paymentId, [
    'amount' => $amount,
    'customer_name' => $customerName,
    'invoice_code' => $invoiceCode
]);
```

### 2. Pembayaran Pending
Ketika ada pembayaran yang pending:
```php
$notificationService->notifyPaymentPending($invoiceId, [
    'amount' => $amount,
    'customer_name' => $customerName,
    'invoice_code' => $invoiceCode
]);
```

### 3. Invoice Jatuh Tempo
Sistem akan mengecek invoice jatuh tempo secara otomatis:
```php
$notificationService->notifyInvoiceDueDate($invoiceId, [
    'invoice_code' => $invoiceCode,
    'customer_name' => $customerName,
    'days_left' => $daysLeft,
    'remaining_amount' => $remainingAmount
]);
```

## Scheduling

### 1. Cek Invoice Jatuh Tempo
Tambahkan ke `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    // Cek invoice jatuh tempo setiap hari jam 9 pagi
    $schedule->command('invoices:check-due-dates')->dailyAt('09:00');
}
```

### 2. Clean Old Notifications
```php
protected function schedule(Schedule $schedule)
{
    // Bersihkan notifikasi lama setiap hari
    $schedule->command('notifications:clean')->daily();
}
```

## Database Schema

### Tabel notifications
```sql
- id (primary key)
- type (jenis notifikasi)
- title (judul notifikasi)
- message (pesan notifikasi)
- recipient_type (employee/customer)
- recipient_id (ID penerima)
- recipient_role (admin/keuangan/driver/gudang/customer)
- data_type (tipe data terkait)
- data_id (ID data terkait)
- is_read (status dibaca)
- read_at (waktu dibaca)
- action_url (URL untuk aksi)
- icon (icon notifikasi)
- priority (low/normal/high/urgent)
- created_at, updated_at
```

## Security

- User hanya bisa melihat notifikasinya sendiri
- Validasi role untuk setiap aksi
- CSRF protection untuk semua request
- Rate limiting untuk API endpoints

## Performance

- Index pada kolom yang sering diquery
- Pagination untuk daftar notifikasi
- Auto-cleanup notifikasi lama
- Lazy loading untuk notifikasi

## Testing

### 1. Manual Testing
1. Login sebagai keuangan
2. Buka dashboard keuangan
3. Klik bell icon untuk melihat notifikasi
4. Test fungsi "Tandai Dibaca"

### 2. Automated Testing
```bash
# Buat notifikasi test
php artisan notifications:create-test-keuangan

# Cek invoice jatuh tempo
php artisan invoices:check-due-dates

# Test mark as read
php artisan notifications:test-mark-read-direct [ID]
```

## Troubleshooting

### 1. Notifikasi Tidak Muncul
- Cek apakah employee dengan role 'keuangan' ada
- Cek log Laravel untuk error
- Pastikan NotificationService berfungsi

### 2. Badge Count Tidak Update
- Cek JavaScript console untuk error
- Pastikan route `/notifications/unread-count` berfungsi
- Cek apakah CSRF token valid

### 3. Mark As Read Tidak Berfungsi
- Cek console browser untuk error
- Pastikan fungsi `window.markAsRead` terdefinisi
- Cek log Laravel untuk error backend

## Future Enhancements

1. **Email Notifications** - Kirim email untuk notifikasi urgent
2. **SMS Notifications** - Kirim SMS untuk invoice jatuh tempo
3. **Notification Preferences** - User bisa set jenis notifikasi yang ingin diterima
4. **Bulk Actions** - Mark all as read, delete multiple notifications
5. **Notification History** - Riwayat notifikasi yang sudah dibaca
6. **Advanced Filtering** - Filter berdasarkan jenis, priority, tanggal
7. **Export Notifications** - Export notifikasi ke PDF/Excel 