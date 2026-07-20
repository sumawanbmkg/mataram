<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

if (!isset($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'ID tidak ditemukan']);
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    $id = $_GET['id'];
    
    // Hapus dari tabel berita
    $stmt = $db->prepare("DELETE FROM berita WHERE id_berita = ?");
    $success = $stmt->execute([$id]);

    echo json_encode(['success' => $success, 'message' => $success ? 'Terhapus' : 'Gagal']);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
