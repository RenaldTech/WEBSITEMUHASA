<?php
// include login and common header
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('admin/login.php');
}

global $db;

// Get statistik
$stats_articles = $db->query("SELECT COUNT(*) as total FROM articles WHERE status = 'published'")->fetch_assoc();
$stats_comments = $db->query("SELECT COUNT(*) as total FROM comments WHERE status = 'pending'")->fetch_assoc();
$stats_messages = $db->query("SELECT COUNT(*) as total FROM contact_messages WHERE status = 'new'")->fetch_assoc();
$stats_categories = $db->query("SELECT COUNT(*) as total FROM categories")->fetch_assoc();
$stats_users = $db->query("SELECT COUNT(*) as total FROM users")->fetch_assoc();
$stats_gallery = $db->query("SELECT COUNT(*) as total FROM gallery")->fetch_assoc();

// Get artikel terbaru
$recent_articles = getArticles(5);
// Get pesan terbaru untuk preview
$recent_messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5")->fetch_all(MYSQLI_ASSOC);

$pageTitle = 'Dashboard';
$headerButtons = '<a href="articles.php" class="btn btn-primary">+ Tambah Berita</a>' .
                 '<a href="articles.php" class="btn btn-primary ml-2">📝 Kelola Berita</a>' .
                 '<a href="categories.php" class="btn btn-primary ml-2">📂 Kelola Kategori</a>' .
                 '<a href="gallery.php" class="btn btn-primary ml-2">📷 Kelola Galeri</a>';

require_once 'partials/admin_header.php';
?>

<?php // dashboard content starts here ?>
            
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_articles['total']; ?></div>
                    <div class="stat-label">Total Berita Dipublikasi</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_comments['total']; ?></div>
                    <div class="stat-label">Komentar Menunggu</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_messages['total']; ?></div>
                    <div class="stat-label">Pesan Baru</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_categories['total']; ?></div>
                    <div class="stat-label">Kategori</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_users['total']; ?></div>
                    <div class="stat-label">Pengguna</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $stats_gallery['total']; ?></div>
                    <div class="stat-label">Item Galeri</div>
                </div>
            </div>
            
            <div class="recent-articles">
                <h2>Berita Terbaru</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Judul</th>
                            <th>Kategori</th>
                            <th>Penulis</th>
                            <th>Tanggal</th>
                            <th>Views</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_articles as $article): ?>
                            <tr>
                                <td><?php echo $article['title']; ?></td>
                                <td><?php echo $article['category_name']; ?></td>
                                <td><?php echo $article['author_name']; ?></td>
                                <td><?php echo formatDate($article['published_at']); ?></td>
                                <td><?php echo $article['views']; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="articles.php" class="btn btn-primary" style="margin-top:10px;">Lihat Semua Berita</a>
            </div>
            
            <div class="recent-articles" style="margin-top:20px;">
                <h2>Pesan Terbaru</h2>
                <table>
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Subjek</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recent_messages as $m): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($m['name']); ?></td>
                                <td><?php echo htmlspecialchars($m['subject']); ?></td>
                                <td><?php echo formatDate($m['created_at']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <a href="messages.php" class="btn btn-primary" style="margin-top:10px;">Kelola Pesan</a>
            </div>
<?php include 'partials/admin_footer.php'; ?>
