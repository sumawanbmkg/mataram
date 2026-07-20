<?php
class Statistics {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getDashboardStats() {
        return [
            'total_news' => $this->db->query("SELECT COUNT(*) FROM berita")->fetchColumn(),
            'total_views' => $this->db->query("SELECT SUM(views) FROM berita")->fetchColumn() ?: 0,
            'total_categories' => $this->db->query("SELECT COUNT(*) FROM kategori")->fetchColumn(),
            'recent_activity' => [] 
        ];
    }
}
