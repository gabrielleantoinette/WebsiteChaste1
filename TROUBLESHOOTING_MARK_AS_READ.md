# Troubleshooting Mark As Read - Notifikasi Admin

## Masalah
Fungsi "Tandai Dibaca" tidak berfungsi - notifikasi tetap berwarna menandakan belum dibaca, dan badge merah tidak berkurang.

## Penyebab
Masalah terjadi karena validasi di NotificationController yang tidak tepat untuk admin. Admin login sebagai object Employee, bukan array, sehingga validasi `recipient_id` tidak cocok.

## Solusi yang Sudah Diterapkan

### 1. Perbaikan NotificationController
File: `app/Http/Controllers/NotificationController.php`

**Method `markAsRead`:**
```php
// Untuk admin, kita perlu mengecek berdasarkan role, bukan hanya recipient_id
if ($user instanceof \App\Models\Employee && $user->role === 'admin') {
    // Admin bisa menandai notifikasi untuk semua admin
    if ($notification->recipient_type === 'employee' && $notification->recipient_role === 'admin') {
        $this->notificationService->markAsRead($id);
        return response()->json(['success' => true]);
    }
}
```

**Method `getUnreadCount`:**
```php
// Untuk admin, ambil semua notifikasi admin yang belum dibaca
if ($user instanceof \App\Models\Employee && $user->role === 'admin') {
    $count = Notification::where('recipient_type', 'employee')
        ->where('recipient_role', 'admin')
        ->where('is_read', false)
        ->count();
}
```

**Method `latest`:**
```php
// Untuk admin, ambil semua notifikasi admin
if ($user instanceof \App\Models\Employee && $user->role === 'admin') {
    $notifications = Notification::where('recipient_type', 'employee')
        ->where('recipient_role', 'admin')
        ->orderBy('created_at', 'desc')
        ->limit(10)
        ->get();
}
```

### 2. Perbaikan JavaScript
File: `resources/views/layouts/admin.blade.php`

- Menambahkan debugging console.log
- Perbaikan error handling
- Menambahkan header Accept untuk JSON response

### 3. Testing Commands
```bash
# Test mark as read functionality
php artisan notifications:test-mark-read

# Buat notifikasi test
php artisan notifications:create-test

# Bersihkan notifikasi lama
php artisan notifications:clean
```

## Cara Testing

### 1. Login sebagai Admin
- Email: admin@gmail.com
- Password: 123

### 2. Buka Dashboard Admin
- Bell icon akan muncul di header
- Badge merah akan menunjukkan jumlah notifikasi belum dibaca

### 3. Klik Bell Icon
- Popover akan terbuka menampilkan daftar notifikasi
- Setiap notifikasi yang belum dibaca akan memiliki tombol "Tandai Dibaca"

### 4. Klik "Tandai Dibaca"
- Tombol akan berubah menjadi "Memproses..."
- Notifikasi akan kehilangan background teal
- Tombol "Tandai Dibaca" akan hilang
- Badge count akan berkurang

### 5. Cek Console Browser
- Buka Developer Tools (F12)
- Lihat tab Console untuk debugging info
- Lihat tab Network untuk request/response

## Debugging

### 1. Cek Log Laravel
```bash
tail -f storage/logs/laravel.log
```

### 2. Cek Database
```bash
php artisan tinker
>>> App\Models\Notification::where('recipient_role', 'admin')->get(['id', 'type', 'title', 'is_read'])
```

### 3. Cek Session User
```bash
php artisan tinker
>>> session('user')
```

## Troubleshooting Steps

### Jika Masih Tidak Berfungsi:

1. **Cek Console Browser**
   - Buka Developer Tools (F12)
   - Lihat error di Console
   - Cek Network tab untuk request/response

2. **Cek CSRF Token**
   - Pastikan meta tag CSRF ada di layout
   - Cek apakah token terkirim di request

3. **Cek Session**
   - Pastikan admin sudah login
   - Cek apakah session user ada

4. **Cek Database**
   - Pastikan notifikasi ada di database
   - Cek apakah `is_read` berubah setelah mark as read

5. **Cek Route**
   - Pastikan route `/notifications/{id}/mark-read` terdaftar
   - Cek apakah middleware LoggedIn berfungsi

## Expected Behavior

### Sebelum Mark As Read:
- Notifikasi memiliki background teal (`bg-teal-50`)
- Tombol "Tandai Dibaca" ada
- Badge count menunjukkan jumlah notifikasi belum dibaca

### Setelah Mark As Read:
- Notifikasi kehilangan background teal
- Tombol "Tandai Dibaca" hilang
- Badge count berkurang
- Di database, `is_read` = true dan `read_at` = timestamp

## Common Issues

### 1. CSRF Token Missing
**Error:** 419 Unprocessable Entity
**Solution:** Pastikan meta tag CSRF ada di layout

### 2. Unauthorized Error
**Error:** 401 Unauthorized
**Solution:** Pastikan user sudah login dan session valid

### 3. Notification Not Found
**Error:** 404 Not Found
**Solution:** Pastikan notification ID valid

### 4. JavaScript Error
**Error:** Console error
**Solution:** Cek browser console untuk detail error

## Performance Tips

1. **Debounce Mark As Read**
   - Jangan spam klik tombol "Tandai Dibaca"
   - Tunggu response sebelum klik lagi

2. **Optimize Database Queries**
   - Gunakan index pada kolom yang sering diquery
   - Limit jumlah notifikasi yang diambil

3. **Cache Badge Count**
   - Cache badge count untuk mengurangi database queries
   - Update cache saat mark as read

## Future Improvements

1. **Real-time Updates**
   - Gunakan WebSocket untuk real-time notification updates
   - Auto-update badge count tanpa refresh

2. **Bulk Actions**
   - "Mark All as Read" button
   - Select multiple notifications

3. **Notification Preferences**
   - User bisa set jenis notifikasi yang ingin diterima
   - Email notifications untuk notifikasi penting 