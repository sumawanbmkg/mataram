# Admin Panel Berita BMKG (KHK)

Admin panel modern untuk manajemen berita BMKG dengan fitur keamanan tingkat tinggi.

## Fitur Utama

### 1. WYSIWYG Editor Modern
- TinyMCE 6 dengan drag-and-drop upload gambar
- Auto-save setiap 30 detik
- Recovery draft otomatis jika koneksi terputus

### 2. Role-Based Access Control (RBAC)
| Role | Akses |
|------|-------|
| Super Admin | Kontrol penuh (user, setting, logs) |
| Admin | Kelola berita, moderasi komentar |
| Editor | Tulis dan edit berita |
| Author | Hanya bisa menulis dan melihat beritanya sendiri |

### 3. Image Management
- Auto-resize ke max 1200px
- Konversi otomatis ke WebP
- Validasi MIME type (bukan hanya ekstensi)
- Rename otomatis dengan nama acak

### 4. Dashboard Statistik
- Grafik berita per bulan
- Grafik views per bulan
- Top 10 berita terpopuler
- Activity log terbaru

## Keamanan

### A. Proteksi Login
- Multi-Factor Authentication (MFA) dengan TOTP
- URL admin tersembunyi (`/khk/pintu-masuk-rahasia.html`)
- Rate limiting (5 percobaan per menit)
- Account lockout setelah gagal berulang

### B. Hardening PHP & Database
- Prepared Statements (PDO) untuk semua query
- Password hashing dengan ARGON2ID
- CSRF Protection pada semua form
- Session security (httponly, secure, samesite)

### C. Pengamanan File Upload
- Validasi MIME type dari konten file
- Rename otomatis ke nama acak
- Konversi ke format aman (WebP)

## Instalasi

### 1. Setup Database
```bash
# Import database utama (jika belum)
mysql -u root -p < database/db_berita.sql

# Import update untuk admin panel
mysql -u root -p db_berita < khk/config/database_update.sql
```

### 2. Konfigurasi Database
Buat file `.env` di root website (`/var/www/webmataram/.env`):
```bash
# Database Configuration
DB_HOST=localhost
DB_NAME=db_berita
DB_USER=root
DB_PASS='NewStr0ngP@ss!'
```

Sistem akan otomatis membaca kredensial dari file `.env`.

### 3. Konfigurasi Nginx
Tambahkan konfigurasi berikut ke `/etc/nginx/sites-available/webmataram`:
```nginx
# Protect sensitive directories
location ~ ^/khk/(config|includes|logs|setup)/ {
    deny all;
    return 404;
}

# Protect sensitive files
location ~ ^/khk/.*\.(sql|log|md|env)$ {
    deny all;
    return 404;
}

# PHP handling for KHK admin panel
location ~ ^/khk/.*\.php$ {
    include snippets/fastcgi-php.conf;
    fastcgi_pass unix:/var/run/php/php-fpm.sock;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
    
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-Frame-Options "DENY" always;
}
```

Lalu reload nginx:
```bash
sudo nginx -t && sudo systemctl reload nginx
```

### 3. Buat User Admin
```sql
INSERT INTO penulis (nama_lengkap, username, email, password, role_id, status) 
VALUES (
    'Admin',
    'admin',
    'admin@bmkg.go.id',
    '$argon2id$v=19$m=65536,t=4,p=3$...',  -- Hash dari password
    1,  -- super_admin
    'aktif'
);
```

Atau gunakan PHP untuk generate password:
```php
echo password_hash('your_password', PASSWORD_ARGON2ID);
```

### 4. Set Permissions
```bash
chmod 755 khk/
chmod 644 khk/*.html
chmod 600 khk/config/config.php
chmod 755 khk/api/
mkdir -p khk/logs
chmod 755 khk/logs
```

## Akses Admin Panel

URL: `https://yourdomain.com/khk/pintu-masuk-rahasia.html`

Default credentials (GANTI SEGERA):
- Username: `admin`
- Password: `password`

## Struktur File

```
khk/
├── api/                    # API endpoints
│   ├── autosave.php
│   ├── categories.php
│   ├── csrf.php
│   ├── login.php
│   ├── logout.php
│   ├── mfa.php
│   ├── news.php
│   ├── statistics.php
│   ├── upload.php
│   └── users.php
├── assets/
│   └── js/
│       └── common.js       # Shared JavaScript
├── config/
│   ├── config.php          # Konfigurasi utama
│   └── database_update.sql # SQL untuk update database
├── includes/
│   ├── Auth.php            # Authentication & MFA
│   ├── ImageProcessor.php  # Image processing
│   ├── NewsManager.php     # CRUD berita
│   ├── Statistics.php      # Dashboard stats
│   └── UserManager.php     # User management
├── logs/                   # Error logs
├── .htaccess               # Security rules
├── dashboard.html          # Dashboard
├── news.html               # Daftar berita
├── news-create.html        # Tulis berita
├── news-edit.html          # Edit berita
├── pintu-masuk-rahasia.html # Login page
├── profile.html            # Profil & MFA
├── users.html              # Kelola user
└── README.md
```

## Rekomendasi Keamanan Tambahan

1. **Cloudflare**: Aktifkan WAF dan DDoS protection
2. **SSL/TLS**: Pastikan HTTPS aktif
3. **Backup**: Setup cron job untuk backup harian
4. **Monitoring**: Integrasikan dengan monitoring tools
5. **Audit**: Review activity logs secara berkala

## TinyMCE API Key

Untuk production, daftarkan API key gratis di:
https://www.tiny.cloud/auth/signup/

Ganti `no-api-key` di file HTML dengan API key Anda.

## Troubleshooting

### Login tidak berfungsi
1. Cek koneksi database di `config/config.php`
2. Pastikan tabel `login_attempts` sudah dibuat
3. Cek error log di `logs/error.log`

### MFA tidak berfungsi
1. Pastikan waktu server sinkron (NTP)
2. Cek tabel `mfa_secrets` sudah dibuat

### Upload gambar gagal
1. Cek permission folder `images/news/`
2. Pastikan GD library terinstall di PHP
3. Cek `upload_max_filesize` di php.ini

## License

Internal use only - BMKG Stasiun Geofisika Mataram
