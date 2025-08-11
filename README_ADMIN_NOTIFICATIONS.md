# Fitur Notifikasi Admin - Website Chaste

## Overview
Fitur notifikasi untuk role admin telah diimplementasikan dengan lengkap. Admin akan menerima notifikasi real-time terkait pesanan, stok, retur, dan pembayaran.

## Jenis Notifikasi untuk Admin

### 1. Pesanan Baru
- **Trigger**: Customer membuat pesanan baru
- **Icon**: `fas fa-shopping-bag`
- **Pesan**: "Pesanan baru dengan ID #[ID] telah dibuat oleh [Customer Name]"
- **Action URL**: `/admin/orders/[ID]`

### 2. Pembayaran Baru
- **Trigger**: Ada pembayaran baru yang diterima
- **Icon**: `fas fa-credit-card`
- **Pesan**: "Pembayaran baru sebesar Rp [Amount] telah diterima"
- **Action URL**: `/admin/payments/[ID]`

### 3. Permintaan Retur
- **Trigger**: Customer mengajukan retur
- **Icon**: `fas fa-undo-alt`
- **Pesan**: "Permintaan retur baru dari [Customer Name] untuk pesanan #[Order ID]"
- **Action URL**: `/admin/retur/[ID]`

### 4. Stok Rendah
- **Trigger**: Stok produk <= 10 unit
- **Icon**: `fas fa-exclamation-circle`
- **Pesan**: "Stok produk [Product Name] tersisa [Stock] unit"
- **Action URL**: `/admin/products/[ID]`

## Implementasi Teknis

### 1. Layout Admin
File: `resources/views/layouts/admin.blade.php`
- Bell icon dengan badge notifikasi
- Popover untuk menampilkan daftar notifikasi
- Auto-refresh setiap 30 detik

### 2. NotificationService
File: `app/Services/NotificationService.php`
- Method `notifyNewOrder()` - Kirim ke admin dan owner
- Method `notifyPayment()` - Kirim ke admin dan keuangan
- Method `notifyReturRequest()` - Kirim ke admin, gudang, dan owner
- Method `notifyLowStock()` - Kirim ke admin, gudang, dan owner

### 3. NotificationController
File: `app/Http/Controllers/NotificationController.php`
- `getUnreadCount()` - Untuk badge count
- `latest()` - Untuk popover content
- `markAsRead()` - Tandai sebagai dibaca

## Cara Kerja

### 1. Badge Notifikasi
- Badge merah dengan angka muncul di pojok kanan atas bell icon
- Auto-update setiap 30 detik
- Menampilkan jumlah notifikasi yang belum dibaca

### 2. Popover Notifikasi
- Klik bell icon untuk membuka popover
- Menampilkan 10 notifikasi terbaru
- Tombol "Tandai Dibaca" untuk setiap notifikasi
- Auto-close saat klik di luar popover

### 3. Real-time Updates
- JavaScript fetch API untuk update badge
- AJAX untuk mark as read
- Error handling untuk network issues

## CSS Styling

### Badge Styling
```css
.notification-badge {
    position: absolute !important;
    top: -8px !important;
    right: -8px !important;
    background-color: #ef4444 !important;
    color: white !important;
    border-radius: 50% !important;
    width: 24px !important;
    height: 24px !important;
    animation: pulse 2s infinite !important;
}
```

### Bell Icon Styling
```css
.fas.fa-bell {
    color: #14b8a6 !important;
    font-size: 1.25rem !important;
}
```

## Testing

### Command Test
```bash
php artisan notifications:create-test
```
Command ini akan membuat notifikasi test untuk semua admin yang ada.

### Manual Testing
1. Login sebagai admin
2. Buka dashboard admin
3. Cek apakah bell icon muncul
4. Cek apakah badge notifikasi muncul (jika ada notifikasi)
5. Klik bell icon untuk membuka popover
6. Test mark as read functionality

## Troubleshooting

### Masalah Umum

1. **Bell icon tidak muncul**
   - Pastikan Font Awesome sudah di-include
   - Cek console browser untuk error

2. **Badge tidak muncul**
   - Cek apakah ada notifikasi di database
   - Cek JavaScript console untuk error
   - Pastikan admin sudah login

3. **Popover tidak terbuka**
   - Cek network tab untuk error fetch
   - Pastikan route `/notifications/latest` berfungsi
   - Cek CSRF token

4. **Notifikasi tidak terkirim**
   - Pastikan NotificationService sudah di-inject
   - Cek log Laravel untuk error
   - Pastikan admin dengan role 'admin' ada di database

### Debug Commands
```bash
# Cek notifikasi di database
php artisan tinker
>>> App\Models\Notification::where('recipient_role', 'admin')->get()

# Cek admin yang ada
>>> App\Models\Employee::where('role', 'admin')->get()
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
- recipient_role (admin/driver/gudang/keuangan/customer)
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

## Maintenance

### Clean Old Notifications
```bash
php artisan notifications:clean
```

### Schedule Cleanup
Tambahkan ke `app/Console/Kernel.php`:
```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('notifications:clean')->daily();
}
```

## Future Enhancements

1. **Push Notifications** - Menggunakan WebSocket atau Service Worker
2. **Email Notifications** - Kirim email untuk notifikasi penting
3. **Sound Notifications** - Audio alert untuk notifikasi baru
4. **Notification Preferences** - User bisa set jenis notifikasi yang ingin diterima
5. **Bulk Actions** - Mark all as read, delete multiple notifications 