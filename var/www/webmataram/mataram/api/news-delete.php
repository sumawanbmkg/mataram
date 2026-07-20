<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID berita tidak valid']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();

    $checkStmt = $db->prepare("SELECT id_berita FROM berita WHERE id_berita = :id LIMIT 1");
    $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $exists = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$exists) {
        echo json_encode(['success' => false, 'message' => 'Berita tidak ditemukan']);
        exit;
    }

    $stmt = $db->prepare("DELETE FROM berita WHERE id_berita = :id");
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $success = $stmt->execute();

    echo json_encode([
        'success' => (bool) $success,
        'message' => $success ? 'Berita berhasil dihapus' : 'Gagal menghapus berita'
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
