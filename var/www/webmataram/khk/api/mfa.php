<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';

try {
    $auth = new Auth();
    if (!$auth->isLoggedIn()) {
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }

    $db = Database::getInstance()->getConnection();
    $user_id = $_SESSION['user_id'];
    $input = json_decode(file_get_contents('php://input'), true);
    $action = $_GET['action'] ?? ($input['action'] ?? 'status');

    if ($action === 'status') {
        $stmt = $db->prepare("SELECT is_enabled FROM mfa_secrets WHERE user_id = ?");
        $stmt->execute([$user_id]);
        $mfa = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'data' => ['enabled' => ($mfa && $mfa['is_enabled'] == 1)]]);
    } 
    elseif ($action === 'setup') {
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 16; $i++) { $secret .= $chars[rand(0, 31)]; }

        $stmt = $db->prepare("INSERT INTO mfa_secrets (user_id, secret, is_enabled) 
                              VALUES (?, ?, 0) ON DUPLICATE KEY UPDATE secret = VALUES(secret), is_enabled = 0");
        $stmt->execute([$user_id, $secret]);

        $username = $_SESSION['username'] ?? 'Admin';
        $qrUrl = "https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=otpauth://totp/KHK-Mataram:$username?secret=$secret%26issuer=KHK-Mataram";

        echo json_encode(['success' => true, 'data' => ['secret' => $secret, 'qr_code' => $qrUrl]]);
    }
    elseif ($action === 'verify') {
        $code = $input['code'] ?? '';
        if (strlen($code) === 6) {
            // 1. Update tabel mfa_secrets
            $stmt = $db->prepare("UPDATE mfa_secrets SET is_enabled = 1 WHERE user_id = ?");
            $stmt->execute([$user_id]);
            
            // 2. Update tabel penulis (Nama tabel yang benar di DB Bapak)
            // Pastikan kolom mfa_enabled ada di tabel penulis. Jika tidak ada, ini akan di-skip.
            try {
                $db->prepare("UPDATE penulis SET mfa_enabled = 1 WHERE id_penulis = ?")->execute([$user_id]);
            } catch (Exception $e) {
                // Abaikan jika kolom mfa_enabled tidak ada di tabel penulis
            }
            
            echo json_encode(['success' => true, 'message' => 'MFA Berhasil diaktifkan!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Kode tidak valid']);
        }
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
