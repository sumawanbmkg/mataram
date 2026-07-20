<?php
// migration.php - Create/adjust database structures for news management enhancements

require_once __DIR__.'/config.php';

$conn = getDBConnection();
if (!$conn) {
    echo "Database connection failed\n";
    exit(1);
}

// 1. Ensure unique index on slug in berita table
$uniqueSlugIdx = $conn->query("SHOW INDEX FROM berita WHERE Key_name='idx_unique_slug'");
if ($uniqueSlugIdx->num_rows == 0) {
    $sql = "ALTER TABLE berita ADD UNIQUE INDEX idx_unique_slug (slug)";
    if ($conn->query($sql) === TRUE) {
        echo "Added unique index on slug.\n";
    } else {
        echo "Error adding unique index on slug: " . $conn->error . "\n";
    }
} else {
    echo "Unique index on slug already exists.\n";
}

// 2. Create audit_log table if not exists
$createAudit = "CREATE TABLE IF NOT EXISTS audit_log (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user VARCHAR(255) NOT NULL,
    action VARCHAR(255) NOT NULL,
    details JSON NULL,
    ip VARCHAR(45) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
if ($conn->query($createAudit) === TRUE) {
    echo "Audit log table ensured.\n";
} else {
    echo "Error creating audit_log table: " . $conn->error . "\n";
}

$conn->close();
?>
