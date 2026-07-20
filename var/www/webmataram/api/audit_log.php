<?php
// audit_log.php - Simple audit logging for admin actions

require_once __DIR__.'/config.php';

function logAudit($action, $details = []) {
    $conn = getDBConnection();
    if (!$conn) return;
    $user = $GLOBALS['auth_user']['sub'] ?? 'unknown';
    $stmt = $conn->prepare('INSERT INTO audit_log (user, action, details, ip, created_at) VALUES (?, ?, ?, ?, NOW())');
    $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
    $detailsJson = json_encode($details);
    $stmt->bind_param('sssd', $user, $action, $detailsJson, $ip);
    $stmt->execute();
    $stmt->close();
}
?>
