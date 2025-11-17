# Fix Upload Foto di Hosting (Tanpa Symlink)

## Masalah
Setelah `php artisan migrate:fresh --seed`, upload foto tidak bisa karena fungsi `symlink()` tidak tersedia di hosting.

## Solusi

### 1. Jalankan Command untuk Membuat Folder
```bash
php artisan fix:upload-dirs
```

Command ini akan:
- Membuat folder `public/images/products` jika belum ada
- Membuat folder `storage/app/public` dan subfoldernya
- Mengatur permission folder menjadi 775
- Memberitahu jika symlink tidak tersedia (tidak masalah, kita pakai route)

### 2. Set Permission Manual (jika perlu)
Via cPanel File Manager atau SSH:
```bash
chmod -R 775 public/images
chmod -R 775 storage/app/public
```

### 3. Cara Kerja Tanpa Symlink

**Upload Produk:**
- File diupload langsung ke `public/images/products/`
- Tidak perlu symlink, langsung bisa diakses via URL

**File di Storage:**
- File di `storage/app/public/` bisa diakses via route: `/public/storage/{path}`
- Route sudah dibuat di `routes/web.php`
- Contoh: `/public/storage/bukti_transfer/file.jpg`

### 4. Test Upload
1. Buka halaman edit produk
2. Upload foto baru
3. Cek apakah foto muncul
4. Jika error, cek log: `storage/logs/laravel.log`

### 5. Troubleshooting

**Error: Permission denied**
```bash
chmod -R 775 public/images
chmod -R 775 storage/app/public
```

**Error: Folder tidak ada**
```bash
php artisan fix:upload-dirs
```

**Foto tidak muncul setelah upload**
- Cek log: `tail -f storage/logs/laravel.log`
- Pastikan folder `public/images/products` ada dan writable
- Cek permission: `ls -la public/images/products`

## Catatan Penting

- **Tidak perlu** menjalankan `php artisan storage:link` jika symlink tidak tersedia
- File produk langsung di `public/images/products/` (tidak di storage)
- File lain (bukti transfer, dll) di `storage/app/public/` dan diakses via route `/public/storage/{path}`

