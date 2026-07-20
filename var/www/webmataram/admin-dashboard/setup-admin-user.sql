-- Setup Admin User untuk Dashboard BMKG
-- Database: db_berita

-- Pastikan tabel users sudah ada
-- Jika belum, buat tabel users terlebih dahulu

-- Buat user admin default
-- Password: password (hash menggunakan PASSWORD_DEFAULT PHP)
INSERT INTO users (username, password, full_name, email, role, is_active, created_at) 
VALUES (
    'admin', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Administrator BMKG', 
    'admin@bmkg.go.id', 
    'admin', 
    1,
    NOW()
)
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    is_active = 1;

-- Buat user editor untuk testing
INSERT INTO users (username, password, full_name, email, role, is_active, created_at) 
VALUES (
    'editor', 
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    'Editor BMKG', 
    'editor@bmkg.go.id', 
    'editor', 
    1,
    NOW()
)
ON DUPLICATE KEY UPDATE 
    password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
    is_active = 1;

-- Tampilkan user yang sudah dibuat
SELECT id, username, full_name, email, role, is_active, created_at 
FROM users 
WHERE username IN ('admin', 'editor');

-- CATATAN:
-- Password default untuk kedua user: password
-- Segera ganti password setelah login pertama!
-- 
-- Untuk generate password baru, gunakan PHP:
-- php -r "echo password_hash('password_baru', PASSWORD_DEFAULT);"
