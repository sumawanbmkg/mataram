<?php
class NewsManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getAllNews() {
        try {
            // Kita buat alias ganda: judul AS title, status AS status, dll.
            $query = "SELECT 
                        b.id_berita as id, 
                        b.id_berita,
                        b.judul as title,
                        b.judul, 
                        b.status, 
                        b.views,
                        b.views as view_count,
                        b.tanggal_publish as date,
                        b.tanggal_publish as published_at,
                        b.tanggal_publish,
                        k.nama_kategori as category, 
                        k.nama_kategori as kategori,
                        p.nama_lengkap as author,
                        p.nama_lengkap as penulis
                      FROM berita b
                      LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
                      LEFT JOIN penulis p ON b.id_penulis = p.id_penulis
                      ORDER BY b.created_at DESC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return [];
        }
    }
}
