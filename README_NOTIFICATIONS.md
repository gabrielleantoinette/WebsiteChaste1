# Sistem Notifikasi Website Chaste

## Overview
Sistem notifikasi ini dirancang untuk memberikan informasi real-time kepada setiap role (admin, owner, driver, gudang, keuangan, customer) sesuai dengan tanggung jawab dan akses masing-masing.

## Fitur Utama

### 1. Jenis Notifikasi
- **Pesanan Baru** - Untuk admin saat ada pesanan baru
- **Status Pesanan** - Untuk customer saat status pesanan berubah
- **Pembayaran** - Untuk keuangan saat ada pembayaran baru
- **Retur** - Untuk gudang saat ada permintaan retur
- **Stok Rendah** - Untuk gudang saat stok produk menipis
- **Pengiriman** - Untuk driver saat ditugaskan pengiriman

### 2. Role-based Notifications
- **Admin**: Notifikasi pesanan baru, pembayaran, retur
- **Owner**: Semua notifikasi admin + laporan khusus
- **Driver**: Notifikasi tugas pengiriman, retur pickup
- **Gudang**: Notifikasi stok rendah, retur, pesanan siap dikemas
- **Keuangan**: Notifikasi pembayaran, hutang jatuh tempo
- **Customer**: Notifikasi status pesanan, konfirmasi pembayaran

## Struktur Database

### Tabel `notifications`
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

## Implementasi

### 1. NotificationService
Service utama untuk mengelola notifikasi:

```php
// Kirim notifikasi ke role tertentu
$notificationService->sendToRole('order_new', 'Pesanan Baru', 'Pesan...', 'admin');

// Kirim notifikasi ke user tertentu
$notificationService->sendToUser('order_status', 'Status Diperbarui', 'Pesan...', 'customer', $customerId);

// Kirim notifikasi ke customer
$notificationService->sendToCustomer('order_status', 'Status Diperbarui', 'Pesan...', $customerId);
```

### 2. Controller Integration
Contoh integrasi di controller:

```php
// Di InvoiceController - saat pesanan baru
$notificationService = app(NotificationService::class);
$notificationService->notifyNewOrder($orderId, [
    'customer_name' => $customer->name,
    'invoice_code' => $invoiceCode,
    'total_amount' => $grandTotal
]);

// Di GudangController - saat status berubah
$notificationService->notifyOrderStatus($invoice->id, $invoice->customer_id, 'processing', [
    'invoice_code' => $invoice->code
]);
```

### 3. Routes
```php
// Notification routes
Route::prefix('notifications')->middleware([LoggedIn::class])->group(function () {
    Route::get('/', [NotificationController::class, 'index']);
    Route::get('/unread', [NotificationController::class, 'getUnread']);
    Route::post('/{id}/mark-read', [NotificationController::class, 'markAsRead']);
    Route::post('/mark-all-read', [NotificationController::class, 'markAllAsRead']);
    Route::delete('/{id}', [NotificationController::class, 'destroy']);
    Route::post('/clear-read', [NotificationController::class, 'clearRead']);
    Route::get('/unread-count', [NotificationController::class, 'getUnreadCount']);
});
```

## Frontend Implementation

### 1. Notification Badge
Badge notifikasi di header akan otomatis update setiap 30 detik:

```javascript
function updateNotificationBadge() {
    fetch('/notifications/unread-count')
        .then(response => response.json())
        .then(data => {
            const badge = document.getElementById('notificationBadge');
            if (data.count > 0) {
                badge.style.display = 'flex';
                badge.querySelector('.count').textContent = data.count;
            } else {
                badge.style.display = 'none';
            }
        });
}
```

### 2. Notification Page
Halaman notifikasi dengan fitur:
- Daftar semua notifikasi
- Tandai sebagai dibaca
- Hapus notifikasi
- Filter berdasarkan status
- Auto-refresh setiap 30 detik

## Jenis Notifikasi per Role

### Admin
- Pesanan baru dari customer
- Pembayaran baru
- Retur yang diajukan
- Laporan transaksi

### Owner
- Semua notifikasi admin
- Laporan keuangan
- Laporan penjualan
- Laporan retur

### Driver
- Tugas pengiriman baru
- Retur pickup
- Status pengiriman

### Gudang
- Stok produk rendah
- Pesanan siap dikemas
- Retur yang perlu diproses
- Barang rusak

### Keuangan
- Pembayaran baru
- Konfirmasi pembayaran
- Hutang jatuh tempo
- Laporan keuangan

### Customer
- Status pesanan diperbarui
- Konfirmasi pembayaran
- Pengiriman selesai
- Retur diproses

## Maintenance

### Clean Old Notifications
Command untuk membersihkan notifikasi lama:

```bash
# Membersihkan notifikasi yang sudah dibaca lebih dari 30 hari
php artisan notifications:clean

# Membersihkan notifikasi lebih dari 7 hari
php artisan notifications:clean --days=7
```

### Schedule Cleanup
Tambahkan ke `app/Console/Kernel.php`:

```php
protected function schedule(Schedule $schedule)
{
    $schedule->command('notifications:clean')->daily();
}
```

## Customization

### Menambah Jenis Notifikasi Baru
1. Tambahkan method di `NotificationService`
2. Tambahkan icon di method `getDefaultIcon()`
3. Integrasikan di controller yang sesuai

### Mengubah Threshold Stok
Ubah nilai di `ProductController::checkLowStock()`:

```php
if ($product && $product->stock <= 10) { // Ubah 10 sesuai kebutuhan
```

### Mengubah Auto-refresh Interval
Ubah interval di JavaScript:

```javascript
setInterval(updateNotificationBadge, 30000); // 30 detik
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
```bash
# Test notification service
php artisan test --filter=NotificationTest

# Test notification controller
php artisan test --filter=NotificationControllerTest
``` 