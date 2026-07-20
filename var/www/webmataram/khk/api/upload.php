<?php
header('Content-Type: application/json');
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$fileKey = !empty($_FILES) ? array_key_first($_FILES) : null;

if ($fileKey && $_FILES[$fileKey]['error'] === 0) {
    $targetDir = "../../images/news/";
    $fileInfo = pathinfo($_FILES[$fileKey]['name']);
    $fileName = time() . '_' . uniqid() . '.' . $fileInfo['extension'];
    
    if (!file_exists($targetDir)) mkdir($targetDir, 0775, true);

    if (move_uploaded_file($_FILES[$fileKey]['tmp_name'], $targetDir . $fileName)) {
        // Kita kirim URL yang lengkap untuk preview
        echo json_encode([
            'success' => true, 
            'location' => '../images/news/' . $fileName,
            'file_name' => $fileName,
            'url' => '../images/news/' . $fileName // Beberapa script mencari 'url'
        ]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Gagal simpan file']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'File tidak terbaca']);
}
