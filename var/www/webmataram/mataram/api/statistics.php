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
    $total_berita = $db->query("SELECT COUNT(*) FROM berita")->fetchColumn();
    $total_views = $db->query("SELECT SUM(views) FROM berita")->fetchColumn() ?: 0;
    
    echo json_encode([
        'success' => true,
        'data' => [
            'total_news' => (int)$total_berita,
            'total_views' => (int)$total_views,
            'total_categories' => 0
        ]
    ]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
