<?php
/**
 * News Management API - Simplified Version
 */

// Security: Disable error reporting in production
if (getenv('APP_ENV') === 'production') {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', __DIR__ . '/../logs/php_errors.log');
} else {
    // Development: Show errors
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}

// Start output buffering
ob_start();

require_once 'config.php';
require_once __DIR__.'/auth_middleware.php';
require_once __DIR__.'/rate_limit.php';
require_once __DIR__.'/validation.php';
require_once __DIR__.'/audit_log.php';

// Security Headers
header('Content-Type: application/json');
sendCorsHeaders();

// Response function
function sendResponse($code, $success, $message, $data = null, $error_code = null) {
    if (ob_get_length()) ob_clean();
    http_response_code($code);
    $response = ['success' => $success, 'message' => $message];
    if ($error_code !== null) {
        $response['error_code'] = $error_code;
    }
    if ($data !== null) $response['data'] = $data;
    echo json_encode($response);
    exit();
}

// Handle OPTIONS
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

$method = $_SERVER['REQUEST_METHOD'];
// Rate limiting
if (!checkRateLimit('global')) {
    // checkRateLimit sends response on limit exceed
    exit();
}
$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    $conn = getDBConnection();
    if (!$conn) {
        sendResponse(500, false, 'Database connection failed');
    }
    
    // Route requests
    if ($method === 'GET') {
        if ($action === 'list') {
            getNewsList($conn);
        } elseif ($action === 'detail' && isset($_GET['id'])) {
            getNewsDetail($conn, $_GET['id']);
        } elseif ($action === 'stats') {
            getNewsStats($conn);
        } else {
            getNewsList($conn);
        }
    } elseif ($method === 'POST' && $action === 'add') {
        requireAuth();
    addNews($conn);
    } elseif ($method === 'POST') {
        // Handle JSON POST requests for featured news management
        requireAuth();
    $data = json_decode(file_get_contents('php://input'), true);
        $postAction = isset($data['action']) ? $data['action'] : '';
        
        if ($postAction === 'set_featured') {
            setFeaturedNews($conn, $data);
        } elseif ($postAction === 'remove_featured') {
            removeFeaturedNews($conn, $data);
        } elseif ($postAction === 'remove_all_featured') {
            removeAllFeaturedNews($conn);
        } else {
            sendResponse(400, false, 'Invalid action');
        }
    } elseif ($method === 'PUT') {
        // Accept PUT either with ?action=update or without
        requireAuth();
        updateNews($conn);
    } elseif ($method === 'DELETE' && $action === 'delete' && isset($_GET['id'])) {
        requireAuth();
        deleteNews($conn, $_GET['id']);
    } else {
        sendResponse(405, false, 'Method not allowed');
    }
    
} catch (Exception $e) {
    error_log('Exception: ' . $e->getMessage());
    sendResponse(500, false, 'Server error: ' . $e->getMessage());
}

// Functions
function getNewsList($conn) {
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $search = isset($_GET['search']) ? $_GET['search'] : '';
    
    // Validate status - whitelist only allowed values
    $allowed_statuses = ['draft', 'publish'];
    if ($status && !in_array($status, $allowed_statuses)) {
        sendResponse(400, false, 'Invalid status value');
    }
    
    $sql = "SELECT b.*, k.nama_kategori, p.nama_lengkap as penulis 
            FROM berita b
            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
            LEFT JOIN penulis p ON b.id_penulis = p.id_penulis
            WHERE 1=1";
    
    $params = [];
    $types = '';
    
    // Use prepared statement for status
    if ($status) {
        $sql .= " AND b.status = ?";
        $types .= "s";
        $params[] = $status;
    }
    
    // Use prepared statement for search
    if ($search) {
        $sql .= " AND b.judul LIKE ?";
        $types .= "s";
        $search_param = '%' . $search . '%';
        $params[] = $search_param;
    }
    
    $sql .= " ORDER BY b.tanggal_publish DESC LIMIT 50";
    
    if (!empty($params)) {
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $result = $stmt->get_result();
    } else {
        $result = $conn->query($sql);
    }
    
    $news = [];
    while ($row = $result->fetch_assoc()) {
        // Add gambar_url for frontend compatibility
        // Return just the filename - frontend will add the path prefix
        if (!isset($row['gambar_url']) && isset($row['gambar_utama'])) {
            $row['gambar_url'] = $row['gambar_utama']; // Just filename
        }
        $news[] = $row;
    }
    sendResponse(200, true, 'Success', $news);
}

function getNewsDetail($conn, $id) {
    $stmt = $conn->prepare("SELECT b.*, k.nama_kategori, p.nama_lengkap as penulis 
                            FROM berita b
                            LEFT JOIN kategori k ON b.id_kategori = k.id_kategori
                            LEFT JOIN penulis p ON b.id_penulis = p.id_penulis
                            WHERE b.id_berita = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        // Add gambar_url for frontend compatibility
        // For admin panel (uses ../ prefix), provide relative path
        if (!isset($data['gambar_url']) && isset($data['gambar_utama'])) {
            $data['gambar_url'] = $data['gambar_utama']; // Just filename, let frontend add path
        }
        sendResponse(200, true, 'Success', $data);
    } else {
        sendResponse(404, false, 'News not found');
    }
}

function getNewsStats($conn) {
    $stats = [];
    $stats['total_news'] = $conn->query("SELECT COUNT(*) as c FROM berita")->fetch_assoc()['c'];
    $stats['total_views'] = $conn->query("SELECT SUM(views) as c FROM berita")->fetch_assoc()['c'] ?? 0;
    $stats['published_news'] = $conn->query("SELECT COUNT(*) as c FROM berita WHERE status='publish'")->fetch_assoc()['c'];
    $stats['draft_news'] = $conn->query("SELECT COUNT(*) as c FROM berita WHERE status='draft'")->fetch_assoc()['c'];
    sendResponse(200, true, 'Success', $stats);
}

function addNews($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    if (empty($data['judul']) || empty($data['isi_berita']) || empty($data['id_kategori'])) {
        sendResponse(400, false, 'Missing required fields');
    }
    
    $judul = trim($data['judul']);
    $isi_berita = trim($data['isi_berita']);
    $id_kategori = intval($data['id_kategori']);
    $id_penulis = isset($data['id_penulis']) ? intval($data['id_penulis']) : 1;
    $gambar = isset($data['gambar']) ? trim($data['gambar']) : '';
    $status = isset($data['status']) ? $data['status'] : 'draft';
    
    // Validate status - whitelist only allowed values
    $allowed_statuses = ['draft', 'publish'];
    if (!in_array($status, $allowed_statuses)) {
        sendResponse(400, false, 'Invalid status value');
    }
    
    // Generate slug
    $slug = strtolower($judul);
    $slug = preg_replace('/[^a-z0-9\s-]/', '', $slug);
    $slug = preg_replace('/[\s-]+/', '-', $slug);
    $slug = trim($slug, '-');
    
    // Make slug unique using prepared statement
    $originalSlug = $slug;
    $counter = 1;
    while (true) {
        $check_stmt = $conn->prepare("SELECT id_berita FROM berita WHERE slug = ? LIMIT 1");
        $check_stmt->bind_param("s", $slug);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows == 0) {
            break;
        }
        $slug = $originalSlug . '-' . $counter++;
    }
    
    // Insert using prepared statement
    $stmt = $conn->prepare("INSERT INTO berita (judul, slug, isi_berita, id_kategori, id_penulis, gambar_utama, status, tanggal_publish) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("sssiiss", $judul, $slug, $isi_berita, $id_kategori, $id_penulis, $gambar, $status);
    
    if ($stmt->execute()) {
        sendResponse(201, true, 'Berita berhasil ditambahkan', ['id_berita' => $conn->insert_id, 'slug' => $slug]);
    } else {
        sendResponse(500, false, 'Failed: ' . $stmt->error);
    }
}

function updateNews($conn) {
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Accept both 'id' and 'id_berita' field names
    $id = isset($data['id_berita']) ? intval($data['id_berita']) : (isset($data['id']) ? intval($data['id']) : 0);
    
    if (!$id || empty($data['judul']) || empty($data['isi_berita'])) {
        sendResponse(400, false, 'Missing required fields');
    }
    
    $judul = trim($data['judul']);
    $isi_berita = trim($data['isi_berita']);
    
    $sql = "UPDATE berita SET judul = ?, isi_berita = ?";
    $types = "ss";
    $params = [$judul, $isi_berita];

    if (isset($data['id_kategori'])) {
        $sql .= ", id_kategori = ?";
        $types .= "i";
        $params[] = intval($data['id_kategori']);
    }

    if (isset($data['gambar_utama'])) {
        $sql .= ", gambar_utama = ?";
        $types .= "s";
        $params[] = trim($data['gambar_utama']);
    } elseif (isset($data['gambar'])) {
        $sql .= ", gambar_utama = ?";
        $types .= "s";
        $params[] = trim($data['gambar']);
    }

    if (isset($data['status'])) {
        $sql .= ", status = ?";
        $types .= "s";
        $params[] = $data['status'];
    }

    if (isset($data['ringkasan'])) {
        $sql .= ", ringkasan = ?";
        $types .= "s";
        $params[] = trim($data['ringkasan']);
    }

    if (isset($data['alt_gambar'])) {
        $sql .= ", alt_gambar = ?";
        $types .= "s";
        $params[] = trim($data['alt_gambar']);
    }

    if (isset($data['meta_description'])) {
        $sql .= ", meta_description = ?";
        $types .= "s";
        $params[] = trim($data['meta_description']);
    }

    if (isset($data['tags'])) {
        $sql .= ", tags = ?";
        $types .= "s";
        $params[] = trim($data['tags']);
    }

    if (isset($data['featured'])) {
        $sql .= ", featured = ?";
        $types .= "i";
        $params[] = intval($data['featured']);
    }

    $sql .= " WHERE id_berita = ?";
    $types .= "i";
    $params[] = $id;
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$params);
    
    if ($stmt->execute()) {
        sendResponse(200, true, 'Berita berhasil diupdate');
    } else {
        sendResponse(500, false, 'Failed: ' . $stmt->error);
    }
}

function deleteNews($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM berita WHERE id_berita = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        sendResponse(200, true, 'Berita berhasil dihapus');
    } else {
        sendResponse(500, false, 'Failed: ' . $stmt->error);
    }
}

// Featured News Management Functions
function setFeaturedNews($conn, $data) {
    if (!isset($data['id_berita'])) {
        sendResponse(400, false, 'Missing id_berita parameter');
    }
    
    $id_berita = intval($data['id_berita']);
    
    // First, remove featured status from all news
    $conn->query("UPDATE berita SET featured = 0");
    
    // Then set this news as featured
    requireAuth();
    $stmt = $conn->prepare("UPDATE berita SET featured = 1 WHERE id_berita = ?");
    $stmt->bind_param("i", $id_berita);
    
    if ($stmt->execute()) {
        sendResponse(200, true, 'Berita berhasil dijadikan berita utama');
    } else {
        sendResponse(500, false, 'Failed: ' . $stmt->error);
    }
}

function removeFeaturedNews($conn, $data) {
    if (!isset($data['id_berita'])) {
        sendResponse(400, false, 'Missing id_berita parameter');
    }
    
    $id_berita = intval($data['id_berita']);
    
    requireAuth();
    $stmt = $conn->prepare("UPDATE berita SET featured = 0 WHERE id_berita = ?");
    $stmt->bind_param("i", $id_berita);
    
    if ($stmt->execute()) {
        sendResponse(200, true, 'Status berita utama berhasil dihapus');
    } else {
        sendResponse(500, false, 'Failed: ' . $stmt->error);
    }
}

function removeAllFeaturedNews($conn) {
    requireAuth();
    if ($conn->query("UPDATE berita SET featured = 0")) {
        sendResponse(200, true, 'Semua status berita utama berhasil dihapus');
    } else {
        sendResponse(500, false, 'Failed: ' . $conn->error);
    }
}
?>
