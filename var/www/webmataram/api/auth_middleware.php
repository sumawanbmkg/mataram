<?php
// auth_middleware.php - Session + JWT authentication middleware.

require_once __DIR__.'/config.php';

function getAuthorizationHeader(){
    $headers = null;
    if (isset($_SERVER['Authorization'])) {
        $headers = trim($_SERVER['Authorization']);
    } elseif (isset($_SERVER['HTTP_AUTHORIZATION'])) {
        $headers = trim($_SERVER['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $requestHeaders = apache_request_headers();
        $requestHeaders = array_change_key_case($requestHeaders, CASE_LOWER);
        if (isset($requestHeaders['authorization'])) {
            $headers = trim($requestHeaders['authorization']);
        }
    }
    return $headers;
}

function base64UrlDecode($input){
    $remainder = strlen($input) % 4;
    if ($remainder) {
        $padlen = 4 - $remainder;
        $input .= str_repeat('=', $padlen);
    }
    return base64_decode(strtr($input, '-_', '+/'));
}

function verifyJwt($jwt){
    $parts = explode('.', $jwt);
    if (count($parts) !== 3) return false;
    list($headerB64, $payloadB64, $signatureB64) = $parts;
    $header = json_decode(base64UrlDecode($headerB64), true);
    $payload = json_decode(base64UrlDecode($payloadB64), true);
    if (!$header || !$payload) return false;
    if (($header['alg'] ?? '') !== 'HS256') return false;
    $expectedSig = hash_hmac('sha256', "$headerB64.$payloadB64", JWT_SECRET, true);
    $expectedSigB64 = rtrim(strtr(base64_encode($expectedSig), '+/', '-_'), '=');
    if (!hash_equals($expectedSigB64, $signatureB64)) return false;
    if (isset($payload['exp']) && time() > $payload['exp']) return false;
    return $payload;
}

function requireAuth(){
    // Try session auth first (for KHK admin panel compatibility)
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
        return; // Session authenticated
    }
    
    // Fallback to JWT Bearer auth
    $authHeader = getAuthorizationHeader();
    if (!$authHeader) {
        sendJsonResponse(['error' => 'Authorization header required'], 401);
        exit;
    }
    if (strpos($authHeader,'Bearer ')!==0) {
        sendJsonResponse(['error' => 'Invalid Authorization format'], 401);
        exit;
    }
    $token = substr($authHeader,7);
    $payload = verifyJwt($token);
    if (!$payload) {
        sendJsonResponse(['error' => 'Invalid or expired token'], 401);
        exit;
    }
    $GLOBALS['auth_user'] = $payload;
}
?>
