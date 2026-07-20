<?php
require_once 'config.php';
checkAuth();

$pdo = getDBConnection();

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 20;
$offset = ($page - 1) * $limit;

// Search and filter
$search = $_GET['search'] ?? '';
$status = $_GET['status'] ?? '';
$category = $_GET['category'] ?? '';

// Build query
$where = [];
$params = [];

if ($search) {
    $where[] = "(b.title LIKE ? OR b.content LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if ($status) {
    $where[] = "b.status = ?";
    $params[] = $status;
}

if ($category) {
    $where[] = "b.category_id = ?";
    $params[] = $category;
}

$whereClause = $where ? 'WHERE ' . implode(' AND ', $where) : '';

// Get total count
$countSql = "SELECT COUNT(*) as total FROM berita b $whereClause";
$stmt = $pdo->prepare($countSql);
$stmt->execute($params);
$total = $stmt->fetch()['total'];
$totalPages = ceil($total / $limit);

// Get news
$sql = "SELECT b.*, c.name as category_name, u.full_name as author_name 
        FROM berita b 
        LEFT JOIN categories c ON b.category_id = c.id 
        LEFT JOIN users u ON b.author_id = u.id 
        $whereClause
        ORDER BY b.created_at DESC 
        LIMIT $limit OFFSET $offset";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$news_list = $stmt->fetchAll();

// Get categories for filter
$categories = $pdo->query("SELECT * FROM categories ORDER BY name")->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Daftar Berita - Admin BMKG</title>
    <link rel="stylesheet" href="assets/styles/css/themes/lite-purple.min.css">
</head>
<body class="app">
    <?php include 'includes/sidebar.php'; ?>
    
    <div class="page-container">
        <?php include 'includes/header.php'; ?>
        
        <main class="main-content bgc-grey-100">
            <div id="mainContent">
                <div class="container-fluid">
                    <div class="d-flex justify-content-between align-items-center mT-10 mB-30">
                        <h4 class="c-grey-900 mb-0">Daftar Berita</h4>
                        <a href="news-add.php" class="btn btn-primary">
                            <i class="ti-plus"></i> Tambah Berita
                        </a>
                    </div>
                    
                    <!-- Filter -->
                    <div class="card mb-4">
                        <div class="card-body">
                            <form method="GET" action="" class="row">
                                <div class="col-md-4">
                                    <input type="text" name="search" class="form-control" placeholder="Cari berita..." value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <select name="status" class="form-control">
                                        <option value="">Semua Status</option>
                                        <option value="published" <?php echo $status === 'published' ? 'selected' : ''; ?>>Published</option>
                                        <option value="draft" <?php echo $status === 'draft' ? 'selected' : ''; ?>>Draft</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <select name="category" class="form-control">
                                        <option value="">Semua Kategori</option>
                                        <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo $cat['id']; ?>" <?php echo $category == $cat['id'] ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($cat['name']); ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <button type="submit" class="btn btn-primary btn-block">Filter</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    
                    <!-- News Table -->
                    <div class="card">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th width="50">ID</th>
                                            <th>Judul</th>
                                            <th>Kategori</th>
                                            <th>Penulis</th>
                                            <th>Status</th>
                                            <th>Views</th>
                                            <th>Tanggal</th>
                                            <th width="150">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (empty($news_list)): ?>
                                        <tr>
                                            <td colspan="8" class="text-center">Tidak ada data</td>
                                        </tr>
                                        <?php else: ?>
                                            <?php foreach ($news_list as $news): ?>
                                            <tr>
                                                <td><?php echo $news['id']; ?></td>
                                                <td>
                                                    <strong><?php echo htmlspecialchars($news['title']); ?></strong>
                                                    <?php if ($news['is_featured']): ?>
                                                        <span class="badge badge-warning ml-2">Featured</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><span class="badge badge-info"><?php echo htmlspecialchars($news['category_name']); ?></span></td>
                                                <td><?php echo htmlspecialchars($news['author_name']); ?></td>
                                                <td>
                                                    <?php if ($news['status'] === 'published'): ?>
                                                        <span class="badge badge-success">Published</span>
                                                    <?php else: ?>
                                                        <span class="badge badge-warning">Draft</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo number_format($news['views']); ?></td>
                                                <td><?php echo formatDateID($news['created_at']); ?></td>
                                                <td>
                                                    <a href="news-edit.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                                        <i class="ti-pencil"></i>
                                                    </a>
                                                    <a href="news-delete.php?id=<?php echo $news['id']; ?>" class="btn btn-sm btn-danger" title="Hapus" onclick="return confirm('Yakin ingin menghapus berita ini?')">
                                                        <i class="ti-trash"></i>
                                                    </a>
                                                </td>
                                            </tr>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                            <nav class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page-1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&category=<?php echo $category; ?>">Previous</a>
                                    </li>
                                    <?php endif; ?>
                                    
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                    <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                                        <a class="page-link" href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&category=<?php echo $category; ?>"><?php echo $i; ?></a>
                                    </li>
                                    <?php endfor; ?>
                                    
                                    <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $page+1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status; ?>&category=<?php echo $category; ?>">Next</a>
                                    </li>
                                    <?php endif; ?>
                                </ul>
                            </nav>
                            <?php endif; ?>
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
