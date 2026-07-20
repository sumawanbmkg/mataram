<?php
session_save_path('/tmp');
session_start();

header('Content-Type: application/json');

if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    echo json_encode(['success' => true, 'user' => $_SESSION['admin_user']]);
} else {
    echo json_encode(['success' => false]);
}
