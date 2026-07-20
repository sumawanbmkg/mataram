<?php
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

// Get statistics
$stats = [];

// Total berita
$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita");
$stats['total_berita'] = $stmt->fetch()['total'];

// Berita published
$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita WHERE status = 'published'");
$stats['published'] = $stmt->fetch()['total'];

// Berita draft
$stmt = $pdo->query("SELECT COUNT(*) as total FROM berita WHERE status = 'draft'");
$stats['draft'] = $stmt->fetch()['total'];

// Total kategori
$stmt = $pdo->query("SELECT COUNT(*) as total FROM categories");
$stats['categories'] = $stmt->fetch()['total'];

// Recent news
$stmt = $pdo->query("SELECT b.*, c.name as category_name, u.full_name as author_name 
                     FROM berita b 
                     LEFT JOIN categories c ON b.category_id = c.id 
                     LEFT JOIN users u ON b.author_id = u.id 
                     ORDER BY b.created_at DESC LIMIT 10");
$recent_news = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Dashboard - Admin BMKG</title>
    <link rel="stylesheet" href="assets/styles/css/themes/lite-purple.min.css">
</head>
<body class="app">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="page-container">
        <?php include 'includes/header.php'; ?>
        
        <main class="main-content bgc-grey-100">
            <div id="mainContent">
                <div class="container-fluid">
                    <h4 class="c-grey-900 mT-10 mB-30">Dashboard</h4>
                    
                    <!-- Statistics Cards -->
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-primary text-white rounded-circle mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <i class="ti-file"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Total Berita</h6>
                                            <h3 class="mb-0"><?php echo number_format($stats['total_berita']); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-success text-white rounded-circle mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <i class="ti-check"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Published</h6>
                                            <h3 class="mb-0"><?php echo number_format($stats['published']); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-warning text-white rounded-circle mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <i class="ti-pencil"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Draft</h6>
                                            <h3 class="mb-0"><?php echo number_format($stats['draft']); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center">
                                        <div class="icon-box bg-info text-white rounded-circle mr-3" style="width: 50px; height: 50px; display: flex; align-items: center; justify-content: center;">
                                            <i class="ti-folder"></i>
                                        </div>
                                        <div>
                                            <h6 class="mb-0">Kategori</h6>
                                            <h3 class="mb-0"><?php echo number_format($stats['categories']); ?></h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Recent News Table -->
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header">
                                    <h5 class="mb-0">Berita Terbaru</h5>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table table-hover">
                                            <thead>
                                                <tr>
                                                    <th>Judul</th>
                                                    <th>Kategori</th>
                                                    <th>Penulis</th>
                                                    <th>Status</th>
                                                    <th>Tanggal</th>
                                                    <th>Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($recent_news as $news): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($news['title']); ?></td>
                                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($news['category_name']); ?></span></td>
                                                    <td><?php echo htmlspecialchars($news['author_name']); ?></td>
                                                    <td>
                                                        <?php if ($news['status'] === 'published'): ?>
                                                            <span class="badge badge-success">Published</span>
                                                        <?php else: ?>
                                                            <span class="badge badge-warning">Draft</span>
                                                        <?php endif; ?>
                                                    </td>
                                                    <td><?php echo formatDateID($news['created_at']); ?></td>
                                                    <td>
                                                        <a href="news-edit.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-primary">Edit</a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
        
        <?php include 'includes/footer.php'; ?>
    </div>
    
    <script src="assets/scripts/index.js"></script>
</body>
</html>
