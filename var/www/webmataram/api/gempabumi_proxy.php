<?php
/**
 * Gempabumi Proxy - Proksi untuk API BMKG
 * Menggunakan cURL untuk menghindari rate limiting & masalah SSL
 */
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Cache-Control: public, max-age=120'); // cache 2 menit

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

$type = isset($_GET['type']) ? $_GET['type'] : 'terkini';

$endpoints = [
    'terkini'   => 'https://data.bmkg.go.id/DataMKG/TEWS/gempaterkini.json',
    'terbaru'   => 'https://data.bmkg.go.id/DataMKG/TEWS/autogempa.json',
    'dirasakan' => 'https://data.bmkg.go.id/DataMKG/TEWS/gempadirasakan.json',
];

$url = $endpoints[$type] ?? $endpoints['terkini'];

// Coba via cURL
function fetchViaCurl($url) {
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 20,
        CURLOPT_CONNECTTIMEOUT => 10,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_USERAGENT => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        CURLOPT_HTTPHEADER => [
            'Accept: application/json, text/plain, */*',
            'Accept-Language: id-ID,id;q=0.9,en;q=0.8',
            'Referer: https://www.bmkg.go.id/gempabumi/',
            'Origin: https://www.bmkg.go.id',
        ],
    ]);
    $result = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    return [$result, $httpCode];
}

// Coba via file_get_contents (fallback)
function fetchViaFile($url) {
    $ctx = stream_context_create([
        'http' => [
            'method' => 'GET',
            'timeout' => 15,
            'header' =>
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n" .
                "Accept: application/json, text/plain, */*\r\n" .
                "Accept-Language: id-ID,id;q=0.9,en;q=0.8\r\n" .
                "Referer: https://www.bmkg.go.id/gempabumi/\r\n" .
                "Origin: https://www.bmkg.go.id\r\n",
        ],
        'ssl' => ['verify_peer' => false, 'verify_peer_name' => false],
    ]);
    return [@file_get_contents($url, false, $ctx), 200];
}

// Coba curl dulu, fallback ke file_get_contents
$response = null;
$httpCode = 0;

if (function_exists('curl_init')) {
    list($response, $httpCode) = fetchViaCurl($url);
    if ($httpCode === 429) {
        sleep(2);
        list($response, $httpCode) = fetchViaCurl($url);
    }
}

if (!$response && ini_get('allow_url_fopen')) {
    list($response, $httpCode) = fetchViaFile($url);
}

if (!$response) {
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal mengambil data dari BMKG',
        'hint' => function_exists('curl_init') ? 'curl_used' : 'no_curl',
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
    exit;
}

$data = json_decode($response, true);
if ($data === null) {
    http_response_code(502);
    echo json_encode([
        'success' => false,
        'message' => 'Gagal parse data BMKG',
        'timestamp' => date('Y-m-d H:i:s'),
    ]);
    exit;
}

echo json_encode([
    'success' => true,
    'data' => $data,
    'source' => $type,
    'timestamp' => date('Y-m-d H:i:s'),
]);
