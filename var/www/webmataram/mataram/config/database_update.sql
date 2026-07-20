-- Database Update untuk Admin Panel KHK
-- Jalankan script ini untuk menambahkan tabel yang diperlukan

USE db_berita;

-- Tabel Login Attempts untuk Rate Limiting
CREATE TABLE IF NOT EXISTS login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(100),
    success TINYINT(1) DEFAULT 0,
    attempt_time DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_ip_time (ip_address, attempt_time),
    INDEX idx_username (username)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel MFA Secrets
CREATE TABLE IF NOT EXISTS mfa_secrets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL UNIQUE,
    secret VARCHAR(32) NOT NULL,
    is_enabled TINYINT(1) DEFAULT 0,
    backup_codes JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES penulis(id_penulis) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Auto-save Drafts
CREATE TABLE IF NOT EXISTS autosave_drafts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    berita_id INT DEFAULT NULL,
    content JSON NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_berita (user_id, berita_id),
    FOREIGN KEY (user_id) REFERENCES penulis(id_penulis) ON DELETE CASCADE,
    FOREIGN KEY (berita_id) REFERENCES berita(id_berita) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Activity Logs untuk Audit Trail
CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    action VARCHAR(50) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INT,
    old_data JSON,
    new_data JSON,
    ip_address VARCHAR(45),
    user_agent TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_action (user_id, action),
    INDEX idx_entity (entity_type, entity_id),
    INDEX idx_created (created_at),
    FOREIGN KEY (user_id) REFERENCES penulis(id_penulis) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabel Statistics untuk Dashboard
CREATE TABLE IF NOT EXISTS page_views (
    id INT AUTO_INCREMENT PRIMARY KEY,
    berita_id INT,
    ip_address VARCHAR(45),
    user_agent TEXT,
    referrer TEXT,
    viewed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_berita (berita_id),
    INDEX idx_viewed (viewed_at),
    FOREIGN KEY (berita_id) REFERENCES berita(id_berita) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Update tabel penulis untuk MFA dan role yang lebih detail
ALTER TABLE penulis 
ADD COLUMN IF NOT EXISTS mfa_enabled TINYINT(1) DEFAULT 0 AFTER status,
ADD COLUMN IF NOT EXISTS last_login DATETIME AFTER mfa_enabled,
ADD COLUMN IF NOT EXISTS failed_attempts INT DEFAULT 0 AFTER last_login,
ADD COLUMN IF NOT EXISTS locked_until DATETIME AFTER failed_attempts;

-- Update role permissions dengan format yang lebih detail
UPDATE user_roles SET permissions = '{"news":["create","read","update","delete","publish"],"users":["create","read","update","delete"],"settings":["read","update"],"logs":["read"],"comments":["read","update","delete"]}' WHERE role_name = 'super_admin';
UPDATE user_roles SET permissions = '{"news":["create","read","update","delete","publish"],"users":["read"],"comments":["read","update","delete"]}' WHERE role_name = 'admin';
UPDATE user_roles SET permissions = '{"news":["create","read","update"],"comments":["read","update"]}' WHERE role_name = 'editor';
UPDATE user_roles SET permissions = '{"news":["read"]}' WHERE role_name = 'viewer';

-- Tambah role Author/Reporter
INSERT IGNORE INTO user_roles (role_name, permissions, description) VALUES
('author', '{"news":["create","read","update_own"],"comments":["read"]}', 'Author/Reporter - hanya bisa menulis dan melihat beritanya sendiri');

-- Cleanup old login attempts (run periodically)
-- DELETE FROM login_attempts WHERE attempt_time < DATE_SUB(NOW(), INTERVAL 24 HOUR);
