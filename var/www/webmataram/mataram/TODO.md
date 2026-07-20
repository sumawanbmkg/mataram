# TODO - Rebuild Sistem Manajemen Berita (CRUD)

- [x] Copy baseline source dari `/var/www/webmataram/khk` ke `/var/www/webmataram/mataram`
- [x] Audit file target dan pastikan struktur siap
- [x] Perbaiki `news.html` agar sinkron ke API CRUD baru + pagination/footer aman (anti NaN)
- [x] Perbaiki `news-create.html` agar submit ke `api/news-create.php` via FormData + TinyMCE sync aman
- [x] Perbaiki `news-edit.html` agar load detail by `id`, sinkron TinyMCE, update via FormData
- [x] Hardening `api/news.php` (list, filter, pagination, prepared statements, JSON standar)
- [x] Hardening `api/news-create.php` (validasi + upload aman + rename uniq + default.jpg)
- [x] Hardening `api/news-detail.php` (validasi ID + prepared statement + JSON standar)
- [x] Hardening `api/news-update.php` (prepared statement penuh + update gambar opsional aman)
- [x] Hardening `api/news-delete.php` (validasi ID + prepared statement + JSON standar)
- [x] Jalankan validasi sintaks PHP (`php -l`) untuk file yang diubah
