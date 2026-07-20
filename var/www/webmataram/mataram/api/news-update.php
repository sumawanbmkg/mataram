<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
header('Content-Type: application/json');
require_once __DIR__ . '/../config/config.php';

function create_slug_update(string $text): string
{
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $text)));
    return trim($slug, '-') ?: ('berita-' . time());
}

try {
    $db = Database::getInstance()->getConnection();

    $id = isset($_POST['id_berita']) ? (int) $_POST['id_berita'] : 0;
    $judul = trim($_POST['judul'] ?? '');
    $isi = trim($_POST['isi'] ?? ($_POST['isi_berita'] ?? ''));
    $idKategori = isset($_POST['id_kategori']) ? (int) $_POST['id_kategori'] : 0;
    $status = trim($_POST['status'] ?? 'draft');
    $ringkasan = trim($_POST['ringkasan'] ?? '');
    $featured = isset($_POST['featured']) ? (int) $_POST['featured'] : 0;
    $altGambar = trim($_POST['alt_gambar'] ?? '');
    $metaDescription = trim($_POST['meta_description'] ?? '');
    $tags = trim($_POST['tags'] ?? '');
    $gambarLama = trim($_POST['gambar_utama_lama'] ?? ($_POST['gambar_utama'] ?? ''));

    if ($id <= 0) {
        echo json_encode(['success' => false, 'message' => 'ID berita tidak valid']);
        exit;
    }
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

    $checkStmt = $db->prepare("SELECT id_berita, gambar_utama FROM berita WHERE id_berita = :id LIMIT 1");
    $checkStmt->bindValue(':id', $id, PDO::PARAM_INT);
    $checkStmt->execute();
    $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);

    if (!$existing) {
        echo json_encode(['success' => false, 'message' => 'Berita tidak ditemukan']);
        exit;
    }

    $gambarNama = $gambarLama !== '' ? $gambarLama : ($existing['gambar_utama'] ?? 'default.jpg');

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

    $slug = create_slug_update($judul);

    $sql = "UPDATE berita SET
                judul = :judul,
                slug = :slug,
                ringkasan = :ringkasan,
                isi_berita = :isi_berita,
                id_kategori = :id_kategori,
                status = :status,
                featured = :featured,
                gambar_utama = :gambar_utama,
                alt_gambar = :alt_gambar,
                meta_description = :meta_description,
                tags = :tags,
                tanggal_publish = CASE
                    WHEN :status_publish = 'publish' AND (tanggal_publish IS NULL OR tanggal_publish = '0000-00-00 00:00:00')
                    THEN NOW()
                    ELSE tanggal_publish
                END
            WHERE id_berita = :id";

    $stmt = $db->prepare($sql);
    $success = $stmt->execute([
        ':judul' => $judul,
        ':slug' => $slug,
        ':ringkasan' => $ringkasan,
        ':isi_berita' => $isi,
        ':id_kategori' => $idKategori,
        ':status' => $status,
        ':featured' => $featured ? 1 : 0,
        ':gambar_utama' => $gambarNama,
        ':alt_gambar' => $altGambar,
        ':meta_description' => $metaDescription,
        ':tags' => $tags,
        ':status_publish' => $status,
        ':id' => $id
    ]);

    echo json_encode([
        'success' => (bool) $success,
        'data' => ['id_berita' => $id]
    ]);
} catch (Throwable $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
