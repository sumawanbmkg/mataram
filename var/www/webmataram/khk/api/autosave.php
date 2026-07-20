<?php
header('Content-Type: application/json');
// Kita buat respon sukses saja tanpa proses berat
echo json_encode([
    'status' => 'success',
    'message' => 'Autosave received'
]);
exit;
