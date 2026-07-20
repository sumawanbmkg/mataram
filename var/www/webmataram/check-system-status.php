<?php
/**
 * Quick System Status Check
 * Checks database, files, and configuration
 */

header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>System Status Check</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background: #f5f5f5; padding: 20px; }
        .container { max-width: 900px; margin: 0 auto; }
        .header { background: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .header h1 { color: #333; margin-bottom: 10px; }
        .section { background: white; padding: 20px; border-radius: 8px; margin-bottom: 15px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .section h2 { color: #333; margin-bottom: 15px; font-size: 18px; border-bottom: 2px solid #007bff; padding-bottom: 10px; }
        .check-item { display: flex; align-items: center; padding: 10px; margin-bottom: 8px; background: #f9f9f9; border-radius: 4px; }
        .check-item .icon { width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-right: 15px; font-weight: bold; }
        .check-item.ok .icon { background: #28a745; color: white; }
        .check-item.error .icon { background: #dc3545; color: white; }
        .check-item.warning .icon { background: #ffc107; color: white; }
        .check-item .info { flex: 1; }
        .check-item .label { font-weight: 500; color: #333; margin-bottom: 3px; }
        .check-item .detail { font-size: 13px; color: #666; }
        .summary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; border-radius: 8px; margin-bottom: 20px; }
        .summary h3 { margin-bottom: 10px; }
        .summary .stats { display: grid; grid-template-columns: repeat(3, 1fr); gap: 15px; margin-top: 15px; }
        .summary .stat { text-align: center; }
        .summary .stat-value { font-size: 32px; font-weight: bold; }
        .summary .stat-label { font-size: 14px; opacity: 0.9; }
        code { background: #f0f0f0; padding: 2px 6px; border-radius: 3px; font-family: 'Courier New', monospace; font-size: 13px; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔍 System Status Check</h1>
            <p>Comprehensive system verification for BMKG News CMS</p>
        </div>

        <?php
        $checks = [];
        $okCount = 0;
        $errorCount = 0;
        $warningCount = 0;

        // Check 1: .env file
        $envFile = __DIR__ . '/.env';
        if (file_exists($envFile)) {
            $envContent = file_get_contents($envFile);
            $hasHost = strpos($envContent, 'DB_HOST=127.0.0.1') !== false;
            $hasUser = strpos($envContent, 'DB_USER=bmkg_user') !== false;
            $hasPass = strpos($envContent, 'DB_PASS=NewStr0ngP@ss!') !== false;
            
            if ($hasHost && $hasUser && $hasPass) {
                $checks[] = ['status' => 'ok', 'label' => '.env File', 'detail' => 'Credentials configured correctly'];
                $okCount++;
            } else {
                $checks[] = ['status' => 'warning', 'label' => '.env File', 'detail' => 'Some credentials may be incorrect'];
                $warningCount++;
            }
        } else {
            $checks[] = ['status' => 'error', 'label' => '.env File', 'detail' => 'File not found'];
            $errorCount++;
        }

        // Check 2: Database connection
        require_once __DIR__ . '/api/config.php';
        try {
            $conn = getDBConnection();
            if ($conn) {
                $checks[] = ['status' => 'ok', 'label' => 'Database Connection', 'detail' => 'Connected successfully'];
                $okCount++;
                
                // Check 3: Count news
                $result = $conn->query("SELECT COUNT(*) as total FROM berita");
                $row = $result->fetch_assoc();
                $totalNews = $row['total'];
                $checks[] = ['status' => 'ok', 'label' => 'News Count', 'detail' => $totalNews . ' berita found in database'];
                $okCount++;
                
                // Check 4: News with images
                $result = $conn->query("SELECT COUNT(*) as total FROM berita WHERE gambar_utama IS NOT NULL AND gambar_utama != ''");
                $row = $result->fetch_assoc();
                $newsWithImages = $row['total'];
                
                if ($newsWithImages > 0) {
                    $checks[] = ['status' => 'ok', 'label' => 'News with Images', 'detail' => $newsWithImages . ' berita have images'];
                    $okCount++;
                } else {
                    $checks[] = ['status' => 'warning', 'label' => 'News with Images', 'detail' => 'No news have images - thumbnails will show placeholder'];
                    $warningCount++;
                }
                
                // Check 5: Sample image filename
                $result = $conn->query("SELECT gambar_utama FROM berita WHERE gambar_utama IS NOT NULL AND gambar_utama != '' LIMIT 1");
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $sampleImage = $row['gambar_utama'];
                    $checks[] = ['status' => 'ok', 'label' => 'Sample Image Filename', 'detail' => $sampleImage];
                    $okCount++;
                } else {
                    $checks[] = ['status' => 'warning', 'label' => 'Sample Image Filename', 'detail' => 'No images in database'];
                    $warningCount++;
                }
                
            } else {
                $checks[] = ['status' => 'error', 'label' => 'Database Connection', 'detail' => 'Connection failed'];
                $errorCount++;
            }
        } catch (Exception $e) {
            $checks[] = ['status' => 'error', 'label' => 'Database Connection', 'detail' => $e->getMessage()];
            $errorCount++;
        }

        // Check 6: Images folder
        $imagesDir = __DIR__ . '/images/news';
        if (is_dir($imagesDir)) {
            $files = glob($imagesDir . '/*.{jpg,jpeg,png,gif,webp}', GLOB_BRACE);
            $fileCount = count($files);
            
            if ($fileCount > 0) {
                $checks[] = ['status' => 'ok', 'label' => 'Images Folder', 'detail' => $fileCount . ' image files found'];
                $okCount++;
            } else {
                $checks[] = ['status' => 'warning', 'label' => 'Images Folder', 'detail' => 'Folder exists but no images found'];
                $warningCount++;
            }
        } else {
            $checks[] = ['status' => 'error', 'label' => 'Images Folder', 'detail' => 'Folder does not exist: ' . $imagesDir];
            $errorCount++;
        }

        // Check 7: API files
        $apiFiles = ['manage_news.php', 'get_news.php', 'config.php'];
        $apiOk = true;
        foreach ($apiFiles as $file) {
            if (!file_exists(__DIR__ . '/api/' . $file)) {
                $apiOk = false;
                break;
            }
        }
        
        if ($apiOk) {
            $checks[] = ['status' => 'ok', 'label' => 'API Files', 'detail' => 'All required API files exist'];
            $okCount++;
        } else {
            $checks[] = ['status' => 'error', 'label' => 'API Files', 'detail' => 'Some API files are missing'];
            $errorCount++;
        }

        // Check 8: Admin files
        $adminFiles = ['admin-fixed.js', 'index.html'];
        $adminOk = true;
        foreach ($adminFiles as $file) {
            if (!file_exists(__DIR__ . '/admin/' . $file)) {
                $adminOk = false;
                break;
            }
        }
        
        if ($adminOk) {
            $checks[] = ['status' => 'ok', 'label' => 'Admin Files', 'detail' => 'All required admin files exist'];
            $okCount++;
        } else {
            $checks[] = ['status' => 'error', 'label' => 'Admin Files', 'detail' => 'Some admin files are missing'];
            $errorCount++;
        }

        // Check 9: KHK Admin
        if (is_dir(__DIR__ . '/khk')) {
            $khkConfig = __DIR__ . '/khk/config/config.php';
            if (file_exists($khkConfig)) {
                $checks[] = ['status' => 'ok', 'label' => 'KHK Admin System', 'detail' => 'KHK admin system is available'];
                $okCount++;
            } else {
                $checks[] = ['status' => 'warning', 'label' => 'KHK Admin System', 'detail' => 'KHK folder exists but config missing'];
                $warningCount++;
            }
        } else {
            $checks[] = ['status' => 'warning', 'label' => 'KHK Admin System', 'detail' => 'KHK admin system not found'];
            $warningCount++;
        }

        // Calculate overall status
        $totalChecks = count($checks);
        $overallStatus = 'ok';
        if ($errorCount > 0) {
            $overallStatus = 'error';
        } elseif ($warningCount > 0) {
            $overallStatus = 'warning';
        }
        ?>

        <div class="summary">
            <h3>Overall Status: <?php 
                if ($overallStatus === 'ok') echo '✅ GOOD';
                elseif ($overallStatus === 'warning') echo '⚠️ NEEDS ATTENTION';
                else echo '❌ CRITICAL ISSUES';
            ?></h3>
            <div class="stats">
                <div class="stat">
                    <div class="stat-value"><?php echo $okCount; ?></div>
                    <div class="stat-label">Passed</div>
                </div>
                <div class="stat">
                    <div class="stat-value"><?php echo $warningCount; ?></div>
                    <div class="stat-label">Warnings</div>
                </div>
                <div class="stat">
                    <div class="stat-value"><?php echo $errorCount; ?></div>
                    <div class="stat-label">Errors</div>
                </div>
            </div>
        </div>

        <div class="section">
            <h2>System Checks</h2>
            <?php foreach ($checks as $check): ?>
                <div class="check-item <?php echo $check['status']; ?>">
                    <div class="icon">
                        <?php 
                            if ($check['status'] === 'ok') echo '✓';
                            elseif ($check['status'] === 'warning') echo '!';
                            else echo '✗';
                        ?>
                    </div>
                    <div class="info">
                        <div class="label"><?php echo $check['label']; ?></div>
                        <div class="detail"><?php echo $check['detail']; ?></div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="section">
            <h2>Next Steps</h2>
            <?php if ($overallStatus === 'ok'): ?>
                <p>✅ System looks good! You can now:</p>
                <ul style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                    <li>Access admin panel: <code>http://your-domain/admin/index.html</code></li>
                    <li>Test thumbnails: <code>http://your-domain/admin/test-thumbnail-debug.html</code></li>
                    <li>Access KHK admin: <code>http://your-domain/khk/pintu-masuk-rahasia.html</code></li>
                </ul>
            <?php elseif ($overallStatus === 'warning'): ?>
                <p>⚠️ System is working but needs attention:</p>
                <ul style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                    <li>Upload images to news items that don't have images</li>
                    <li>Check warnings above and fix them</li>
                    <li>Test admin panel functionality</li>
                </ul>
            <?php else: ?>
                <p>❌ Critical issues found:</p>
                <ul style="margin-left: 20px; margin-top: 10px; line-height: 1.8;">
                    <li>Fix database connection issues</li>
                    <li>Create missing folders</li>
                    <li>Check file permissions</li>
                    <li>Review error messages above</li>
                </ul>
            <?php endif; ?>
        </div>

        <div class="section">
            <h2>Quick Links</h2>
            <ul style="margin-left: 20px; line-height: 1.8;">
                <li><a href="admin/test-thumbnail-debug.html" target="_blank">Thumbnail Debug Tool</a></li>
                <li><a href="api/test_db_connection.php" target="_blank">Database Connection Test</a></li>
                <li><a href="khk/test-db-connection.php" target="_blank">KHK Database Test</a></li>
                <li><a href="admin/index.html" target="_blank">Admin Panel</a></li>
                <li><a href="khk/pintu-masuk-rahasia.html" target="_blank">KHK Admin Login</a></li>
            </ul>
        </div>
    </div>
</body>
</html>

