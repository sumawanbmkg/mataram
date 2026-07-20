<?php
// Set session cookie agar berlaku di seluruh folder web
session_set_cookie_params([
    'lifetime' => 86400,
    'path' => '/',
    'httponly' => true,
    'samesite' => 'Lax'
]);

if (session_status() === PHP_SESSION_NONE) session_start();

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/Auth.php';

$input = json_decode(file_get_contents('php://input'), true);
$username = $input['username'] ?? '';
$password = $input['password'] ?? '';
$otp_code = $input['otp_code'] ?? null;

try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->prepare("SELECT id_penulis, username, password, nama_lengkap FROM penulis WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user || !password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Username atau password salah']);
        exit;
    }

    // Cek MFA
    $stmtMfa = $db->prepare("SELECT is_enabled FROM mfa_secrets WHERE user_id = ?");
    $stmtMfa->execute([$user['id_penulis']]);
    $mfa = $stmtMfa->fetch(PDO::FETCH_ASSOC);

    if ($mfa && $mfa['is_enabled'] == 1 && !$otp_code) {
        echo json_encode(['success' => true, 'mfa_required' => true]);
        exit;
    }

    // RE-GENERATE SESSION ID UNTUK KEAMANAN
    session_regenerate_id(true);
    
    $_SESSION['user_id'] = $user['id_penulis'];
    $_SESSION['id_penulis'] = $user['id_penulis'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['login_time'] = time();
    $_SESSION['logged_in'] = true;

    echo json_encode(['success' => true, 'mfa_required' => false, 'redirect' => 'news.html']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
