<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

global $db;

// jika admin mengirim form tambah berita
$admin_message = '';
if (isLoggedIn() && isAdmin() && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['admin_article'])) {
    // gunakan fungsi baru dari includes/functions.php
    if (addArticle(array_merge($_POST, ['author_id' => $_SESSION['user_id']]), $_FILES['featured_image'] ?? null)) {
        $admin_message = 'Berita berhasil ditambahkan.';
    } else {
        $admin_message = 'Gagal menambahkan berita.';
    }
}

// Get pages per content
$per_page = 6;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $per_page;

// Get total articles
$total_result = $db->query("SELECT COUNT(*) as total FROM articles WHERE status = 'published'");
$total_row = $total_result->fetch_assoc();
$total_articles = $total_row['total'];
$total_pages = ceil($total_articles / $per_page);

// Get articles
$sql = "SELECT a.*, c.name as category_name, u.username as author_name 
        FROM articles a 
        LEFT JOIN categories c ON a.category_id = c.id 
        LEFT JOIN users u ON a.author_id = u.id 
        WHERE a.status = 'published'
        ORDER BY a.published_at DESC 
        LIMIT $offset, $per_page";

$articles = $db->query($sql)->fetch_all(MYSQLI_ASSOC);

// Get categories untuk filter
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Berita - SMP Muhammadiyah (Tahfidz) Salatiga</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <!-- NAVBAR -->
    <nav>
        <div class="navbar-container">
            <a href="index.php" class="navbar-brand">
                <span><img src="assets/images/logo.png" alt="Logo SMP Muhammadiyah"></span>
                <span>SMP Muhammadiyah<br>(Tahfidz) Salatiga</span>
            </a>
            <button class="nav-toggle">☰</button>
            <ul class="navbar-menu">
                <li><a href="index.php">Beranda</a></li>
                <li><a href="akademik.php">Akademik</a></li>
                <li><a href="berita.php" class="active">Berita</a></li>
                <li><a href="spmb.php">SPMB</a></li>
                <li><a href="galeri.php">Galeri</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <h1>Berita dan Pengumuman</h1>
            <p>Informasi terbaru mengenai kegiatan dan perkembangan sekolah</p>
        </div>
    </section>

    <!-- SEARCH -->
    <section class="section">
        <div class="search-box">
            <input type="text" id="searchNews" placeholder="🔍 Cari berita..." style="padding: 0.75rem 1rem;" onkeyup="searchArticles()">
        </div>

        <?php if (isLoggedIn() && isAdmin()): ?>
            <?php if ($admin_message): ?>
                <p class="alert alert-success" style="margin-top:1rem"><?php echo $admin_message; ?></p>
            <?php endif; ?>
            
        <?php endif; ?>
    </section>

    <!-- BERITA LIST -->
    <section class="section" style="background-color: #f9fafb;">
        <h2 class="section-title">Daftar Berita</h2>
        <div style="max-width: 1200px; margin: 0 auto;">
            <div id="newsList" class="cards-grid" style="grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));">
                <?php foreach ($articles as $article): ?>
                    <div class="card">
                        <?php if ($article['featured_image']): ?>
                            <div style="width: 100%; height: 200px; overflow: hidden; background: #ddd;">
                                <img src="uploads/<?php echo $article['featured_image']; ?>" alt="<?php echo $article['title']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                            </div>
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: #0052CC; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold;">
                                <?php echo $article['category_name']; ?>
                            </div>
                        <?php endif; ?>
                        <div class="card-body">
                            <div class="article-meta">📅 <?php echo formatDate($article['published_at']); ?> | 👤 <?php echo $article['author_name']; ?></div>
                            <h3 style="color: var(--primary-color); margin-bottom: 1rem;"><?php echo $article['title']; ?></h3>
                            <p>
                                <?php echo substr(strip_tags($article['content']), 0, 150); ?>...
                            </p>
                            <p style="margin-top: 1rem; color: var(--text-light); font-size: 12px;">
                                📂 <?php echo $article['category_name']; ?> | 👁️ <?php echo $article['views']; ?> views
                            </p>
                            <a href="artikel.php?slug=<?php echo $article['slug']; ?>" class="btn btn-primary">Baca Selengkapnya →</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- PAGINATION -->
            <div class="pagination" style="margin-top: 2rem; text-align: center;">
                <?php if ($page > 1): ?>
                    <a href="berita.php?page=<?php echo $page - 1; ?>" class="pagination-link">← Sebelumnya</a>
                <?php endif; ?>

                <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                    <?php if ($i == $page): ?>
                        <span class="active" style="display: inline-block; padding: 8px 12px; background: var(--primary-color); color: white; border-radius: 5px; margin: 0 5px;"><?php echo $i; ?></span>
                    <?php else: ?>
                        <a href="berita.php?page=<?php echo $i; ?>" style="display: inline-block; padding: 8px 12px; border: 1px solid #ddd; border-radius: 5px; margin: 0 5px; text-decoration: none; color: var(--primary-color);"><?php echo $i; ?></a>
                    <?php endif; ?>
                <?php endfor; ?>

                <?php if ($page < $total_pages): ?>
                    <a href="berita.php?page=<?php echo $page + 1; ?>" class="pagination-link">Selanjutnya →</a>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <?php if (isLoggedIn() && isAdmin()): ?>
    <!-- ADMIN ADD ARTICLE MODAL -->
    <div id="adminArticleModal" class="modal">
        <div class="modal-content" style="max-width:600px;">
            <span class="modal-close" onclick="closeModal('adminArticleModal')">&times;</span>
            <h2>Tambah Berita Baru</h2>
            <form method="POST" enctype="multipart/form-data">
                <input type="hidden" name="admin_article" value="1">
                <div class="form-group">
                    <label>Judul</label>
                    <input type="text" name="title" required>
                </div>
                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category_id" required>
                        <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo $cat['id']; ?>"><?php echo $cat['name']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="form-group">
                    <label>Ringkasan</label> <!-- human-readable, DB field still excerpt -->
                    <textarea name="excerpt"></textarea>
                </div>
                <div class="form-group">
                    <label>Isi</label>
                    <textarea name="content" required></textarea>
                </div>
                <div class="form-group">
                    <label>Gambar Utama</label>
                    <input type="file" name="featured_image" accept="image/*">
                </div>
                <div class="form-group">
                    <label>Status</label>
                    <select name="status">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Simpan</button>
                <button type="button" class="btn btn-secondary" onclick="closeModal('adminArticleModal')">Batal</button>
            </form>
        </div>
    </div>
    <?php endif; ?>

    <!-- FOOTER -->
    <footer>
        <div class="footer-container">
            <div class="footer-section">
                <div style="display: flex; align-items: center; gap: 1rem; margin-bottom: 1.5rem;">
                    <span style="font-size: 2.5rem;"><img src="assets/images/logo.png" alt="Logo SMP Muhammadiyah"></span>
                    <div style="font-weight: 700; font-size: 0.95rem;">SMP Muhammadiyah<br>(Tahfidz) Salatiga</div>
                </div>
                <p>Jl. Cempaka No.5-7, Jetis, Kecamatan Sidorejo, Kota Salatiga, Jawa Tengah 50711.</p>
                <p style="margin-top: 1.5rem; font-weight: 600;">Follow us on:</p>
                <div class="social-links">
                    <a href="https://facebook.com/smpmuhsltg" target="_blank" title="Facebook"><img src="assets/images/facebook.jpg" alt="facebook"></a>
                    <a href="https://instagram.com/smpmuhammadiyahsalatiga" target="_blank" title="Instagram"><img src="assets/images/instagram.jpg" alt="instagram"></a>
                    <a href="https://www.tiktok.com/@smpmuhammadiyahsalatiga" target="_blank" title="TikTok"><img src="assets/images/tiktok.jpg" alt="tiktok"></a>
                    <a href="https://youtube.com/@smpmuhammadiyahcempakasala5600" target="_blank" title="YouTube"><img src="assets/images/youtube.jpg" alt="youtube"></a>
                </div>
            </div>
            <div class="footer-section">
                <h4>Quick Links</h4>
                <a href="index.php">Home</a>
                <a href="akademik.php">Akademik</a>
                <a href="berita.php">Berita</a>
                <a href="spmb.php">SPMB</a>
            </div>
            <div class="footer-section">
                <h4>All Pages</h4>
                <a href="akademik.php">Akademik</a>
                <a href="berita.php">Berita</a>
                <a href="kontak.php">Kontak</a>
                <a href="spmb.php">SPMB</a>
            </div>
        </div>
        <div class="footer-bottom">
            <p>&copy; 2026, SMP Muhammadiyah Tahfidz. All Rights Reserved.</p>
        </div>
    </footer>

    <script src="assets/js/script.js"></script>
    <script>
        function searchArticles() {
            const keyword = document.getElementById('searchNews').value;
            if (keyword.length >= 3) {
                window.location.href = 'berita.php?search=' + encodeURIComponent(keyword);
            }
        }
    </script>
    <?php if (isLoggedIn() && isAdmin()): ?>
    <script>
        function openModal(id) { document.getElementById(id).classList.add('show'); }
        function closeModal(id) { document.getElementById(id).classList.remove('show'); }
    </script>
    <?php endif; ?>
</body>
</html>
