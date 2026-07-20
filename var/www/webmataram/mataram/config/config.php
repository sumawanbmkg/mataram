<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
// Konstanta Keamanan
if (!defined('SESSION_LIFETIME')) define('SESSION_LIFETIME', 86400);
if (!defined('MAX_LOGIN_ATTEMPTS')) define('MAX_LOGIN_ATTEMPTS', 10);
if (!defined('MFA_ENABLED')) define('MFA_ENABLED', false);

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $host = '127.0.0.1';
        $db   = 'db_berita';
        $user = 'admin_mataram';
        $pass = 'Mataram2026!';

        try {
            $this->conn = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => 'DB Error: ' . $e->getMessage()]);
            exit;
        }
    }

    public static function getInstance() {
        if (!self::$instance) self::$instance = new Database();
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }
}
