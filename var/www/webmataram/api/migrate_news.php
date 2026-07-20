<?php
// migrate_news.php - Database migrations for news API
require_once __DIR__.'/config.php';

function runMigrations() {
    $conn = getDBConnection();
    if (!$conn) {
        echo "Database connection failed\n";
        exit(1);
    }
    // Ensure slug is unique
    $sqlUnique = "ALTER TABLE berita ADD UNIQUE INDEX idx_slug_unique (slug)";
    $conn->query($sqlUnique);
    // Create audit_log table if not exists
    $sqlAudit = "CREATE TABLE IF NOT EXISTS audit_log (\n        id BIGINT AUTO_INCREMENT PRIMARY KEY,\n        user VARCHAR(255) NOT NULL,\n        action VARCHAR(255) NOT NULL,\n        details TEXT,\n        ip VARCHAR(45) NOT NULL,\n        created_at DATETIME NOT NULL\n    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $conn->query($sqlAudit);
    echo "Migrations applied.\n";
}
runMigrations();
?>
