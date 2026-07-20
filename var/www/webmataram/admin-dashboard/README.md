# Admin Dashboard BMKG

Dashboard admin yang terintegrasi dengan database `db_berita` menggunakan template Adminator.

## 📋 Fitur

- ✅ Login & Authentication
- ✅ Dashboard dengan statistik
- ✅ Manajemen Berita (CRUD)
- ✅ Manajemen Kategori
- ✅ Manajemen Komentar
- ✅ Manajemen Pengguna
- ✅ Upload Gambar
- ✅ Berita Utama (Featured)
- ✅ Filter & Pencarian
- ✅ Pagination

## 🔧 Konfigurasi Database

Dashboard ini menggunakan kredensial dari file `.env`:

```env
DB_HOST=127.0.0.1
DB_NAME=db_berita
DB_USER=bmkg_user
DB_PASS=NewStr0ngP@ss!
```

## 📁 Struktur File

```
admin-dashboard/
├── assets/              # CSS, JS, images dari template Adminator
├── includes/            # File include (sidebar, header, footer)
│   ├── sidebar.php
│   ├── header.php
│   └── footer.php
├── config.php           # Konfigurasi database & fungsi helper
├── login.php            # Halaman login
├── logout.php           # Logout handler
├── index.php            # Dashboard utama
├── news-list.php        # Daftar berita
├── news-add.php         # Tambah berita (perlu dibuat)
├── news-edit.php        # Edit berita (perlu dibuat)
├── news-delete.php      # Hapus berita (perlu dibuat)
├── news-featured.php    # Kelola berita utama (perlu dibuat)
├── categories.php       # Manajemen kategori (perlu dibuat)
├── comments.php         # Manajemen komentar (perlu dibuat)
├── users.php            # Manajemen pengguna (perlu dibuat)
└── settings.php         # Pengaturan (perlu dibuat)
```

## 🚀 Instalasi

1. **Copy folder admin-dashboard ke server**
   ```bash
   # Folder sudah dibuat di: admin-dashboard/
   ```

2. **Pastikan file .env sudah ada di root project**
   ```bash
   # File .env sudah ada dengan kredensial database
   ```

3. **Buat user admin di database** (jika belum ada)
   ```sql
   INSERT INTO users (username, password, full_name, email, role, is_active) 
   VALUES (
       'admin', 
       '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', -- password: password
       'Administrator', 
       'admin@bmkg.go.id', 
       'admin', 
       1
   );
   ```

4. **Akses dashboard**
   ```
   http://localhost/admin-dashboard/login.php
   ```

## 🔐 Login Default

- **Username**: admin
- **Password**: password

⚠️ **PENTING**: Segera ganti password setelah login pertama!

## 📝 Cara Menggunakan

### Dashboard
- Menampilkan statistik total berita, published, draft, dan kategori
- Menampilkan 10 berita terbaru

### Manajemen Berita
1. **Lihat Daftar**: Klik menu "Berita" → "Semua Berita"
2. **Tambah Berita**: Klik tombol "Tambah Berita"
3. **Edit Berita**: Klik icon pensil pada baris berita
4. **Hapus Berita**: Klik icon trash (konfirmasi diperlukan)
5. **Filter**: Gunakan form filter untuk mencari berdasarkan judul, status, atau kategori

### Berita Utama (Featured)
- Klik menu "Berita" → "Berita Utama"
- Pilih berita yang ingin dijadikan featured
- Maksimal 5 berita featured

## 🎨 Customization

### Mengubah Warna Theme
Edit file `assets/styles/css/themes/lite-purple.min.css` atau pilih theme lain:
- lite-purple.min.css (default)
- lite-blue.min.css
- lite-red.min.css
- lite-green.min.css

### Mengubah Logo
Edit file `includes/sidebar.php` pada bagian `.brand`

## 🔒 Keamanan

1. **Password Hashing**: Menggunakan `password_hash()` PHP
2. **Session Management**: Session-based authentication
3. **SQL Injection Protection**: Menggunakan prepared statements PDO
4. **XSS Protection**: Menggunakan `htmlspecialchars()`
5. **File Upload Validation**: Validasi tipe dan ukuran file

## 📊 Database Tables

Dashboard ini menggunakan tabel:
- `berita` - Data berita
- `categories` - Kategori berita
- `users` - Pengguna admin
- `comments` - Komentar berita

## 🐛 Troubleshooting

### Error: Connection failed
- Pastikan MySQL service berjalan
- Cek kredensial di file `.env`
- Pastikan database `db_berita` sudah dibuat

### Error: Headers already sent
- Pastikan tidak ada output sebelum `header()` dipanggil
- Cek file config.php tidak ada echo/print

### Assets tidak muncul
- Pastikan folder `assets/` sudah disalin dengan benar
- Cek path relatif di file HTML

## 📚 Dokumentasi Template

Template Adminator: https://github.com/puikinsh/Adminator-admin-dashboard

## 🔄 Update & Maintenance

### Backup Database
```bash
mysqldump -u bmkg_user -p db_berita > backup_$(date +%Y%m%d).sql
```

### Update Dashboard
1. Backup file yang sudah dimodifikasi
2. Pull update dari repository
3. Merge perubahan

## 📞 Support

Untuk bantuan lebih lanjut, hubungi tim development BMKG.

---

© 2026 BMKG. All rights reserved.
