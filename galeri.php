<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

global $db;

// Get gallery
$gallery = getGallery();
$categories = ['akademik', 'keagamaan', 'olahraga', 'ekstrakurikuler'];
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - SMP Muhammadiyah (Tahfidz) Salatiga</title>
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
                <li><a href="berita.php">Berita</a></li>
                <li><a href="spmb.php">SPMB</a></li>
                <li><a href="galeri.php" class="active">Galeri</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="hero-content">
            <h1>Galeri Kegiatan</h1>
            <p>Dokumentasi foto dan video aktivitas sekolah</p>
        </div>
    </section>

    <!-- GALLERY FILTER & DISPLAY -->
    <section class="section" style="background-color:#f9fafb;">
        <h2 class="section-title">Koleksi Galeri</h2>
        
        <div class="gallery-filter">
            <button class="filter-btn active" onclick="filterGallery('all')">Semua</button>
            <button class="filter-btn" onclick="filterGallery('akademik')">Akademik</button>
            <button class="filter-btn" onclick="filterGallery('keagamaan')">Keagamaan</button>
            <button class="filter-btn" onclick="filterGallery('olahraga')">Olahraga</button>
            <button class="filter-btn" onclick="filterGallery('ekstrakurikuler')">Ekstrakurikuler</button>
        </div>

        <div id="galleryContainer" class="gallery">
            <?php foreach ($gallery as $item): ?>
                <div class="gallery-item" data-category="<?php echo $item['category']; ?>">
                    <img src="uploads/<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
                    <div class="gallery-overlay">
                        <h3><?php echo $item['title']; ?></h3>
                        <p><?php echo $item['description']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

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
        function filterGallery(category) {
            // Update active button
            document.querySelectorAll('.filter-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            event.target.classList.add('active');
            
            // Filter items
            const items = document.querySelectorAll('.gallery-item');
            items.forEach(item => {
                if (category === 'all' || item.dataset.category === category) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        }
    </script>
</body>
</html>
