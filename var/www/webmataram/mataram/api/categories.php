<?php
// Pastikan session terbaca agar tidak 'Unauthorized'
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

try {
    // Gunakan koneksi database dari class Database Bapak
    $db = Database::getInstance()->getConnection();
    
    // Ambil data kategori
    $query = "SELECT id_kategori, nama_kategori FROM kategori ORDER BY nama_kategori ASC";
    $stmt = $db->prepare($query);
    $stmt->execute();
    $list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Jika tabel di database ternyata kosong, kita berikan kategori default agar dropdown tidak kosong
    if (count($list) === 0) {
        $list = [
            ['id_kategori' => '1', 'nama_kategori' => 'Berita'],
            ['id_kategori' => '2', 'nama_kategori' => 'Gempabumi'],
            ['id_kategori' => '3', 'nama_kategori' => 'Tsunami'],
            ['id_kategori' => '4', 'nama_kategori' => 'Umum']
        ];
    }

    echo json_encode([
        'success' => true,
        'data' => $list
    ]);

} catch (Exception $e) {
    // Jika database error, kirim pesan error sebagai JSON
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'data' => [['id_kategori' => '1', 'nama_kategori' => 'Umum (DB Error)']]
    ]);
}
