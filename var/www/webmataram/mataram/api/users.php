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

    // Mendukung request profile.html (id=current)
    if (isset($_GET['id']) && $_GET['id'] === 'current') {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT id_user as id, username, nama_lengkap as nama, email, bio FROM users WHERE id_user = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['success' => true, 'data' => $user]);
        exit;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
