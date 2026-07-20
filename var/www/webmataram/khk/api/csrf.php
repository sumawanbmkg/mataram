<?php
session_start();
header('Content-Type: application/json');
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
echo json_encode(['success' => true, 'csrf_token' => $_SESSION['csrf_token']]);
