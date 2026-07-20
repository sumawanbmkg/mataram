<?php
if (session_status() === PHP_SESSION_NONE) session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();
    $judul = $_POST['judul'] ?? 'Tanpa Judul';
    $isi = $_POST['isi'] ?? '';
    $id_kat = $_POST['id_kategori'] ?? 1;
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $judul)));
    
    $gambar_nama = 'default.jpg';
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] === 0) {
        $ext = pathinfo($_FILES['gambar']['name'], PATHINFO_EXTENSION);
        $gambar_nama = time() . '_' . uniqid() . '.' . $ext;
        $uploadDir = __DIR__ . "/../../images/news/";
        move_uploaded_file($_FILES['gambar']['tmp_name'], $uploadDir . $gambar_nama);
    }

    $sql = "INSERT INTO berita (judul, slug, isi_berita, id_kategori, id_penulis, gambar_utama, status, tanggal_publish) 
            VALUES (?, ?, ?, ?, ?, ?, 'publish', NOW())";
    $stmt = $db->prepare($sql);
    $stmt->execute([$judul, $slug, $isi, $id_kat, $_SESSION['user_id'] ?? 1, $gambar_nama]);

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
