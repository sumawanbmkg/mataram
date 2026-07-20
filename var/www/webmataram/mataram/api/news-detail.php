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

    $sql = "
        SELECT 
            b.*,
            COALESCE(k.nama_kategori, 'Umum') AS nama_kategori,
            'Admin' AS penulis_nama
        FROM berita b
        LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
        WHERE b.id_berita = :id
        LIMIT 1
    ";

    $stmt = $db->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $data = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Berita tidak ditemukan']);
        exit;
    }

    $data['isi'] = $data['isi_berita'] ?? '';
    $data['views'] = isset($data['views']) ? (int) $data['views'] : 0;

    echo json_encode(['success' => true, 'data' => $data]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
