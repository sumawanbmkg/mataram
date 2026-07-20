<?php
// API untuk mengambil data berita (List & Detail)
require_once 'config.php';
require_once 'rate_limit.php';

if (!checkRateLimit('api_get_news')) {
    sendJsonResponse(['success' => false, 'message' => 'Rate limit exceeded'], 429);
    exit;
}

$cacheEnabled = false;
if (file_exists('cache_helper.php')) {
    try {
        require_once 'cache_helper.php';
        $cacheEnabled = true;
    } catch (Exception $e) { }
}

try {
    $database = new Database();
    $db = $database->getConnection();
    
    // Tangkap parameter
    $slug = isset($_GET['slug']) ? sanitizeInput($_GET['slug']) : '';
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 6;
    $category = isset($_GET['category']) ? sanitizeInput($_GET['category']) : '';
    $search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
    $sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'newest';
    $featured = isset($_GET['featured']) ? (bool)$_GET['featured'] : false;

    // --- LOGIKA UNTUK DETAIL BERITA (SINGLE NEWS) ---
    if (!empty($slug)) {
        $query = "
            SELECT 
                b.*, 
                k.nama_kategori as kategori, 
                p.nama_lengkap as penulis
            FROM berita b
            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
            LEFT JOIN penulis p ON b.id_penulis = p.id_penulis
            WHERE b.slug = :slug AND b.status = 'publish' 
            LIMIT 1
        ";
        
        $stmt = $db->prepare($query);
        $stmt->bindValue(':slug', $slug);
        $stmt->execute();
        $item = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            // Update views
            $db->prepare("UPDATE berita SET views = views + 1 WHERE id_berita = ?")->execute([$item['id_berita']]);
            
            // Format data untuk JS
            $item['tanggal_publish_formatted'] = formatDateIndonesian($item['tanggal_publish']);
            $item['konten'] = $item['konten'] ?? $item['isi_berita'] ?? ''; // Support multi-column name
            
            sendJsonResponse(['success' => true, 'data' => $item]);
            exit;
        } else {
            sendJsonResponse(['success' => false, 'message' => 'Berita tidak ditemukan'], 404);
            exit;
        }
    }

    // --- LOGIKA UNTUK DAFTAR BERITA (LIST) ---
    $where_conditions = ["b.status = 'publish'"];
    $params = [];
    if ($featured) $where_conditions[] = "b.featured = 1";
    if (!empty($category)) {
        $where_conditions[] = "k.slug_kategori = :category";
        $params[':category'] = $category;
    }
    if (!empty($search)) {
        $where_conditions[] = "(b.judul LIKE :search OR b.ringkasan LIKE :search)";
        $params[':search'] = "%$search%";
    }
    
    $where_clause = implode(' AND ', $where_conditions);
    $offset = ($page - 1) * $limit;

    $query = "
        SELECT 
            b.id_berita, b.judul, b.slug, b.ringkasan, b.gambar_utama, 
            b.views, b.tanggal_publish, k.nama_kategori as kategori, p.nama_lengkap as penulis
        FROM berita b
        LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
        LEFT JOIN penulis p ON b.id_penulis = p.id_penulis
        WHERE $where_clause
        ORDER BY b.tanggal_publish DESC
        LIMIT :limit OFFSET :offset
    ";
    
    $stmt = $db->prepare($query);
    foreach ($params as $key => $value) $stmt->bindValue($key, $value);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    
    $news = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Proses URL Gambar agar konsisten
    foreach ($news as &$item) {
        $item['tanggal_publish_formatted'] = formatDateIndonesian($item['tanggal_publish']);
        $item['gambar_url'] = !empty($item['gambar_utama']) ? 'images/news/' . $item['gambar_utama'] : 'images/placeholder-news.jpg';
    }

    sendJsonResponse(['success' => true, 'data' => $news]);

} catch (Exception $e) {
    sendJsonResponse(['success' => false, 'message' => $e->getMessage()], 500);
}
