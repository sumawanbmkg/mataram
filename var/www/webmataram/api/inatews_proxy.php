<?php
/**
 * InaTEWS WRS Proxy - Real-time earthquake data from InaTEWS/WRS
 * Sumber: https://bmkg-content-inatews.storage.googleapis.com/gempaQL.json
 * Update setiap 10 menit
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Cache-Control: public, max-age=300'); // 5 menit cache

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'all';
$url = 'https://bmkg-content-inatews.storage.googleapis.com/gempaQL.json';

$ch = curl_init();
curl_setopt_array($ch, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 15,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36',
    CURLOPT_HTTPHEADER => [
        'Accept: application/json',
        'Referer: https://inatews.bmkg.go.id/',
    ],
]);
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

if ($httpCode !== 200 || !$response) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Gagal mengambil data dari InaTEWS', 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

$data = json_decode($response, true);
if (!$data || !isset($data['features'])) {
    http_response_code(502);
    echo json_encode(['success' => false, 'message' => 'Data tidak valid dari InaTEWS', 'timestamp' => date('Y-m-d H:i:s')]);
    exit;
}

// Filter by NTB region if requested
if ($mode === 'ntb') {
    $ntbFeatures = [];
    foreach ($data['features'] as $f) {
        $coords = $f['geometry']['coordinates'] ?? [0, 0];
        $lng = (float)$coords[0];
        $lat = (float)$coords[1];
        // NTB bounding box: Lombok + Sumbawa
        if ($lat >= -9.5 && $lat <= -7.8 && $lng >= 115.5 && $lng <= 119.5) {
            $ntbFeatures[] = $f;
        }
    }
    $data['features'] = $ntbFeatures;
    $data['total'] = count($ntbFeatures);
}

$data['success'] = true;
$data['timestamp'] = date('Y-m-d H:i:s');
$data['source'] = 'InaTEWS BMKG';

echo json_encode($data);
