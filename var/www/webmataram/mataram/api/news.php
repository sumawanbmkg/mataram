<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

try {
    $db = Database::getInstance()->getConnection();

    $page = isset($_GET['page']) ? max(1, (int) $_GET['page']) : 1;
    $limit = isset($_GET['limit']) ? max(1, min(100, (int) $_GET['limit'])) : 10;
    $offset = ($page - 1) * $limit;

    $search = isset($_GET['search']) ? trim($_GET['search']) : '';
    $status = isset($_GET['status']) ? trim($_GET['status']) : '';
    $kategori = isset($_GET['kategori']) ? (int) $_GET['kategori'] : 0;

    $where = [];
    $params = [];

    if ($search !== '') {
        $where[] = "(b.judul LIKE :search OR b.ringkasan LIKE :search OR b.isi_berita LIKE :search)";
        $params[':search'] = '%' . $search . '%';
    }

    if ($status !== '') {
        $where[] = "b.status = :status";
        $params[':status'] = $status;
    }

    if ($kategori > 0) {
        $where[] = "b.id_kategori = :kategori";
        $params[':kategori'] = $kategori;
    }

    $whereSql = count($where) ? ('WHERE ' . implode(' AND ', $where)) : '';

    $countSql = "SELECT COUNT(*) AS total FROM berita b $whereSql";
    $countStmt = $db->prepare($countSql);
    foreach ($params as $key => $value) {
        $countStmt->bindValue($key, $value);
    }
    $countStmt->execute();
    $total = (int) ($countStmt->fetch(PDO::FETCH_ASSOC)['total'] ?? 0);

    $listSql = "
        SELECT 
            b.*,
            COALESCE(k.nama_kategori, 'Umum') AS nama_kategori,
            'Admin' AS penulis_nama
        FROM berita b
        LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
        $whereSql
        ORDER BY b.id_berita DESC
        LIMIT :limit OFFSET :offset
    ";

    $listStmt = $db->prepare($listSql);
    foreach ($params as $key => $value) {
        $listStmt->bindValue($key, $value);
    }
    $listStmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $listStmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $listStmt->execute();

    $list = $listStmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($list as &$item) {
        $item['isi'] = $item['isi_berita'] ?? '';
        $item['views'] = isset($item['views']) ? (int) $item['views'] : 0;
    }

    $totalPages = $total > 0 ? (int) ceil($total / $limit) : 1;

    echo json_encode([
        'success' => true,
        'data' => $list,
        'meta' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $total,
            'total_pages' => $totalPages
        ]
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
