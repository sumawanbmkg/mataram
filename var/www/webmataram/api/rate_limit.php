<?php
// api/rate_limit.php
// Simple file‑based token bucket rate limiter (100 req/min per IP).

function getClientIp() {
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function checkRateLimit($key, $limit = 100, $windowSeconds = 60) {
    $ip = getClientIp();
    $file = __DIR__ . '/rate_limit_data.json';
    $data = [];
    if (file_exists($file)) {
        $json = file_get_contents($file);
        $data = json_decode($json, true) ?: [];
    }
    $now = time();
    if (!isset($data[$ip])) {
        $data[$ip] = [];
    }
    // Clean old entries
    foreach ($data[$ip] as $k => $entry) {
        if ($now - $entry['timestamp'] > $windowSeconds) {
            unset($data[$ip][$k]);
        }
    }
    // Count current requests for this key
    $count = $data[$ip][$key]['count'] ?? 0;
    if ($count >= $limit) {
        // Save updated data before returning
        file_put_contents($file, json_encode($data));
        return false; // limit exceeded
    }
    // Increment count
    $data[$ip][$key] = ['timestamp' => $now, 'count' => $count + 1];
    file_put_contents($file, json_encode($data));
    return true;
}
?>
