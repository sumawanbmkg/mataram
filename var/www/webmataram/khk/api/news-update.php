<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    $id = $_POST['id_berita'];
    $judul = $_POST['judul'];
    $isi = $_POST['isi'];
    $id_kat = $_POST['id_kategori'];
    $status = $_POST['status'] ?? 'publish';
    $ringkasan = $_POST['ringkasan'] ?? '';

    $gambar = $_POST['gambar_utama'] ?? '';
    if ($gambar === 'undefined' || $gambar === 'null') $gambar = '';
    $alt_gambar = $_POST['alt_gambar'] ?? '';
    $meta_description = $_POST['meta_description'] ?? '';
    $tags = $_POST['tags'] ?? '';
    $featured = isset($_POST['featured']) ? intval($_POST['featured']) : 0;

    $sql = "UPDATE berita SET judul = ?, isi_berita = ?, id_kategori = ?, status = ?, ringkasan = ?, gambar_utama = ?";
    $params = [$judul, $isi, $id_kat, $status, $ringkasan, $gambar];
    $sql .= ", alt_gambar = ?, meta_description = ?, tags = ?, featured = ?";
    $params[] = $alt_gambar;
    $params[] = $meta_description;
    $params[] = $tags;
    $params[] = $featured;

    $sql .= " WHERE id_berita = ?";
    $params[] = $id;

    $stmt = $db->prepare($sql);
    $success = $stmt->execute($params);

    echo json_encode(['success' => $success]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
