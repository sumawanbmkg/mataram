<?php
session_start();
// Sesuaikan dengan variabel yang diset di Auth.php
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: dashboard.html");
} else {
    header("Location: pintu-masuk-rahasia.html");
}
exit;
?>
