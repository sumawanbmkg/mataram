<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

function create_slug(string $text): string
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    return trim($slug, '-') ?: ('berita-' . time());
}

try {
    $db = Database::getInstance()->getConnection();

    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? ($_POST['isi_berita'] ?? ''));
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $idKategori = (int) ($_POST['id_kategori'] ?? 0);
    $status = trim($_POST['status'] ?? 'draft');
    $featured = isset($_POST['featured']) ? (int) $_POST['featured'] : 0;
    $altGambar = trim($_POST['alt_gambar'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $idPenulis = (int) ($_SESSION['user_id'] ?? ($_SESSION['id_penulis'] ?? 1));

    if ($judul === '') {
        echo json_encode(['success' => false, 'message' => 'Judul wajib diisi']);
        exit;
    }
    if ($isi === '') {
        echo json_encode(['success' => false, 'message' => 'Isi berita wajib diisi']);
        exit;
    }
    if ($idKategori <= 0) {
        echo json_encode(['success' => false, 'message' => 'Kategori wajib dipilih']);
        exit;
    }
    if (!in_array($status, ['draft', 'publish', 'archived'], true)) {
        $status = 'draft';
    }

    $gambarNama = 'default.jpg';
    if (isset($_FILES['gambar']) && (int)$_FILES['gambar']['error'] !== UPLOAD_ERR_NO_FILE) {
        if ((int)$_FILES['gambar']['error'] !== UPLOAD_ERR_OK) {
            echo json_encode(['success' => false, 'message' => 'Gagal upload gambar']);
            exit;
        }

        $mime = mime_content_type($_FILES['gambar']['tmp_name']);
        $allowedMime = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
        if (!isset($allowedMime[$mime])) {
            echo json_encode(['success' => false, 'message' => 'Format gambar tidak didukung']);
            exit;
        }

        if ((int)$_FILES['gambar']['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'Ukuran gambar maksimal 5MB']);
            exit;
        }

        $extension = $allowedMime[$mime];
        $gambarNama = time() . '_' . uniqid('', true) . '.' . $extension;

        $uploadDirPrimary = realpath(__DIR__ . '/../../../images/news');
        $uploadDirFallback = __DIR__ . '/../../images/news';
        $uploadDir = $uploadDirPrimary !== false ? $uploadDirPrimary : $uploadDirFallback;

        if (!is_dir($uploadDir) && !mkdir($uploadDir, 0775, true)) {
            echo json_encode(['success' => false, 'message' => 'Direktori upload gambar tidak tersedia']);
            exit;
        }

        $targetPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $gambarNama;

        if (!is_writable($uploadDir)) {
            echo json_encode(['success' => false, 'message' => 'Direktori upload tidak dapat ditulis']);
            exit;
        }

        if (!move_uploaded_file($_FILES['gambar']['tmp_name'], $targetPath)) {
            $lastError = error_get_last();
            $errMsg = $lastError['message'] ?? 'unknown';
            echo json_encode(['success' => false, 'message' => 'Gagal menyimpan gambar: ' . $errMsg]);
            exit;
        }
    }

    $slug = create_slug($judul);

    $sql = "INSERT INTO berita (
                judul, slug, ringkasan, isi_berita, id_kategori, id_penulis,
                gambar_utama, alt_gambar, status, featured, meta_description, tags, tanggal_publish
            ) VALUES (
                :judul, :slug, :ringkasan, :isi_berita, :id_kategori, :id_penulis,
                :gambar_utama, :alt_gambar, :status, :featured, :meta_description, :tags,
                CASE WHEN :status_publish = 'publish' THEN NOW() ELSE NULL END
            )";

    $stmt = $db->prepare($sql);
    $stmt->execute([
        ':judul' => $judul,
        ':slug' => $slug,
        ':ringkasan' => $ringkasan,
        ':isi_berita' => $isi,
        ':id_kategori' => $idKategori,
        ':id_penulis' => $idPenulis,
        ':gambar_utama' => $gambarNama,
        ':alt_gambar' => $altGambar,
        ':status' => $status,
        ':featured' => $featured ? 1 : 0,
        ':meta_description' => $metaDescription,
        ':tags' => $tags,
        ':status_publish' => $status
    ]);

    echo json_encode([
        'success' => true,
        'data' => ['id_berita' => (int) $db->lastInsertId()]
    ]);
} catch (Throwable $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
