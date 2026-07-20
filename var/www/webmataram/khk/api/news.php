<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    
    // Query ke tabel 'berita' yang sudah terbukti ada di db_berita
    $query = "SELECT * FROM berita ORDER BY id_berita DESC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($list as &$item) {
        $item['penulis_nama'] = $item['penulis_nama'] ?? 'Admin';
        $item['nama_kategori'] = $item['nama_kategori'] ?? 'Umum';
        $item['isi'] = $item['isi_berita'] ?? '';
    }

    echo json_encode(['success' => true, 'data' => $list]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
