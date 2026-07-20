<?php
/**
 * Authentication Class dengan MFA Support
 * Keamanan: Rate Limiting, ARGON2ID, Session Management
 */

define('KHK_ADMIN', true);
require_once __DIR__ . '/../config/config.php';

class Auth {
    private $pdo;
    private $user = null;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->initSession();
    }

    private function initSession() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // Regenerate session ID periodically
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        } elseif (time() - $_SESSION['last_regeneration'] > 300) {
            session_regenerate_id(true);
            $_SESSION['last_regeneration'] = time();
        }
    }

    /**
     * Login dengan rate limiting dan MFA
     */
    public function login($username, $password, $mfaCode = null) {
        $ip = Security::getClientIP();

        // Check rate limiting
        if (!Security::checkRateLimit($ip, $this->pdo)) {
            $this->logActivity(null, 'login_blocked', 'user', null, null, ['reason' => 'rate_limit']);
            return ['success' => false, 'message' => 'Terlalu banyak percobaan login. Coba lagi dalam 1 menit.'];
        }

        // Get user
        $stmt = $this->pdo->prepare("
            SELECT p.*, ur.role_name, ur.permissions 
            FROM penulis p 
            LEFT JOIN user_roles ur ON p.role_id = ur.id 
            WHERE p.username = :username AND p.status = 'aktif'
        ");
        $stmt->execute(['username' => $username]);
        $user = $stmt->fetch();

        if (!$user) {
            Security::recordLoginAttempt($ip, $username, false, $this->pdo);
            return ['success' => false, 'message' => 'Username atau password salah.'];
        }

        // Check if account is locked
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return ['success' => false, 'message' => 'Akun terkunci. Coba lagi nanti.'];
        }

        // Verify password
        if (!Security::verifyPassword($password, $user['password'])) {
            Security::recordLoginAttempt($ip, $username, false, $this->pdo);
            $this->incrementFailedAttempts($user['id_penulis']);
            return ['success' => false, 'message' => 'Username atau password salah.'];
        }

        // Check MFA if enabled
        if ($user['mfa_enabled'] && MFA_ENABLED) {
            if (empty($mfaCode)) {
                return ['success' => false, 'require_mfa' => true, 'message' => 'Masukkan kode MFA.'];
            }

            $mfaSecret = $this->getMFASecret($user['id_penulis']);
            if (!$mfaSecret || !TOTP::verifyCode($mfaSecret, $mfaCode)) {
                Security::recordLoginAttempt($ip, $username, false, $this->pdo);
                return ['success' => false, 'require_mfa' => true, 'message' => 'Kode MFA tidak valid.'];
            }
        }

        // Login successful
        Security::recordLoginAttempt($ip, $username, true, $this->pdo);
        $this->resetFailedAttempts($user['id_penulis']);
        $this->updateLastLogin($user['id_penulis']);

        // Set session
        $_SESSION['user_id'] = $user['id_penulis'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['role'] = $user['role_name'];
        $_SESSION['permissions'] = json_decode($user['permissions'], true);
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        session_regenerate_id(true);

        $this->logActivity($user['id_penulis'], 'login', 'user', $user['id_penulis']);

        return ['success' => true, 'message' => 'Login berhasil.', 'user' => [
            'id' => $user['id_penulis'],
            'username' => $user['username'],
            'nama' => $user['nama_lengkap'],
            'role' => $user['role_name']
        ]];
    }

    /**
     * Logout
     */
    public function logout() {
        if (isset($_SESSION['user_id'])) {
            $this->logActivity($_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id']);
        }
        
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        return ['success' => true];
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        if (!isset($_SESSION['logged_in']) || !$_SESSION['logged_in']) {
            return false;
        }

        // Check session timeout
        if (time() - $_SESSION['login_time'] > SESSION_LIFETIME) {
            $this->logout();
            return false;
        }

        return true;
    }

    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        if ($this->user === null) {
            $stmt = $this->pdo->prepare("
                SELECT p.*, ur.role_name, ur.permissions 
                FROM penulis p 
                LEFT JOIN user_roles ur ON p.role_id = ur.id 
                WHERE p.id_penulis = :id
            ");
            $stmt->execute(['id' => $_SESSION['user_id']]);
            $this->user = $stmt->fetch();
        }

        return $this->user;
    }

    /**
     * Check permission
     */
    public function hasPermission($module, $action) {
        if (!$this->isLoggedIn()) {
            return false;
        }

        $permissions = $_SESSION['permissions'] ?? [];
        
        if (isset($permissions[$module])) {
            return in_array($action, $permissions[$module]);
        }

        return false;
    }

    /**
     * Check if user is admin or super_admin
     */
    public function isAdmin() {
        return in_array($_SESSION['role'] ?? '', ['admin', 'super_admin']);
    }

    /**
     * Setup MFA for user
     */
    public function setupMFA($userId) {
        $secret = TOTP::generateSecret();
        
        $stmt = $this->pdo->prepare("
            INSERT INTO mfa_secrets (user_id, secret, is_enabled) 
            VALUES (:user_id, :secret, 0)
            ON DUPLICATE KEY UPDATE secret = :secret, is_enabled = 0
        ");
        $stmt->execute(['user_id' => $userId, 'secret' => $secret]);

        $user = $this->getCurrentUser();
        $qrUrl = TOTP::getQRCodeUrl($user['username'], $secret);

        return [
            'secret' => $secret,
            'qr_url' => $qrUrl
        ];
    }

    /**
     * Enable MFA after verification
     */
    public function enableMFA($userId, $code) {
        $secret = $this->getMFASecret($userId);
        
        if (!$secret || !TOTP::verifyCode($secret, $code)) {
            return ['success' => false, 'message' => 'Kode verifikasi tidak valid.'];
        }

        // Generate backup codes
        $backupCodes = [];
        for ($i = 0; $i < 10; $i++) {
            $backupCodes[] = strtoupper(bin2hex(random_bytes(4)));
        }

        $stmt = $this->pdo->prepare("
            UPDATE mfa_secrets SET is_enabled = 1, backup_codes = :codes WHERE user_id = :user_id
        ");
        $stmt->execute(['user_id' => $userId, 'codes' => json_encode($backupCodes)]);

        $stmt = $this->pdo->prepare("UPDATE penulis SET mfa_enabled = 1 WHERE id_penulis = :id");
        $stmt->execute(['id' => $userId]);

        $this->logActivity($userId, 'mfa_enabled', 'user', $userId);

        return ['success' => true, 'backup_codes' => $backupCodes];
    }

    /**
     * Disable MFA
     */
    public function disableMFA($userId) {
        $stmt = $this->pdo->prepare("DELETE FROM mfa_secrets WHERE user_id = :user_id");
        $stmt->execute(['user_id' => $userId]);

        $stmt = $this->pdo->prepare("UPDATE penulis SET mfa_enabled = 0 WHERE id_penulis = :id");
        $stmt->execute(['id' => $userId]);

        $this->logActivity($userId, 'mfa_disabled', 'user', $userId);

        return ['success' => true];
    }

    private function getMFASecret($userId) {
        $stmt = $this->pdo->prepare("SELECT secret FROM mfa_secrets WHERE user_id = :user_id AND is_enabled = 1");
        $stmt->execute(['user_id' => $userId]);
        $result = $stmt->fetch();
        return $result ? $result['secret'] : null;
    }

    private function incrementFailedAttempts($userId) {
        $stmt = $this->pdo->prepare("
            UPDATE penulis 
            SET failed_attempts = failed_attempts + 1,
                locked_until = IF(failed_attempts >= :max, DATE_ADD(NOW(), INTERVAL 15 MINUTE), locked_until)
            WHERE id_penulis = :id
        ");
        $stmt->execute(['id' => $userId, 'max' => MAX_LOGIN_ATTEMPTS]);
    }

    private function resetFailedAttempts($userId) {
        $stmt = $this->pdo->prepare("UPDATE penulis SET failed_attempts = 0, locked_until = NULL WHERE id_penulis = :id");
        $stmt->execute(['id' => $userId]);
    }

    private function updateLastLogin($userId) {
        $stmt = $this->pdo->prepare("UPDATE penulis SET last_login = NOW() WHERE id_penulis = :id");
        $stmt->execute(['id' => $userId]);
    }

    private function logActivity($userId, $action, $entityType = null, $entityId = null, $oldData = null, $newData = null) {
        $stmt = $this->pdo->prepare("
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id, old_data, new_data, ip_address, user_agent)
            VALUES (:user_id, :action, :entity_type, :entity_id, :old_data, :new_data, :ip, :ua)
        ");
        $stmt->execute([
            'user_id' => $userId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'old_data' => $oldData ? json_encode($oldData) : null,
            'new_data' => $newData ? json_encode($newData) : null,
            'ip' => Security::getClientIP(),
            'ua' => $_SERVER['HTTP_USER_AGENT'] ?? ''
        ]);
    }
}
