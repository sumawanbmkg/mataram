<?php
require_once 'includes/Auth.php';
$db = Database::getInstance()->getConnection();
$stmt = $db->prepare("SELECT username, status, password FROM penulis WHERE username = 'superadmin'");
$stmt->execute();
$user = $stmt->fetch(PDO::FETCH_ASSOC);

echo "<pre>";
echo "Data di DB:\n";
print_r($user);
echo "\nTes Verifikasi Password 'BmkgAdmin2026!':\n";
var_dump(password_verify('BmkgAdmin2026!', $user['password']));
echo "</pre>";
