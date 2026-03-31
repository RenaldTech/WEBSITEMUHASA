<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

// Buat file ini sesuai dengan spmb.html yang sudah ada
// File ini masih menggunakan HTML static untuk PPDB

// ambil setting SPMB
$spmb = $db->query("SELECT * FROM spmb_settings LIMIT 1")->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SPMB - SMP Muhammadiyah (Tahfidz) Salatiga</title>
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
                <li><a href="spmb.php" class="active">SPMB</a></li>
                <li><a href="galeri.php">Galeri</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </div>
    </nav>

    <!-- HERO SECTION -->
  <section class="ppdb-section">

<h2 class="ppdb-title">Informasi PPDB 2026/2027</h2>

<div class="ppdb-grid">

<div class="ppdb-image">
<?php if (!empty($spmb['banner_image'])): ?>
    <img src="uploads/<?php echo htmlspecialchars($spmb['banner_image']); ?>" alt="Brosur PPDB">
<?php endif; ?>
</div>

<div class="ppdb-links">

<h3>Link Informasi Penting PPDB</h3>

<ul>
<li><a href="#">Klik Link PPDB Jateng</a></li>
<li><a href="#">Klik Link Join Grup WA SMP Muhasa</a></li>
<li><a href="#">Klik Link Download Brosur</a></li>
<li><a href="#">Klik Link Petunjuk Teknis</a></li>
</ul>

</div>

</div>

</section>

<?php if (!empty($spmb['technical_pdf'])): ?>
<section class="section">

<h2 class="section-title">Petunjuk Teknis</h2>

<div class="pdf-viewer">
    <iframe 
    src="uploads/<?php echo htmlspecialchars($spmb['technical_pdf']); ?>" 
    width="100%" 
    height="600px">
    </iframe>
</div>
<div style="text-align:center; margin-top:15px;">
    <a href="uploads/<?php echo htmlspecialchars($spmb['technical_pdf']); ?>" class="btn btn-primary" download>Download Petunjuk Teknis</a>
</div>

</section>
<?php endif; ?>

<?php if (!empty($spmb['announcement_pdf'])): ?>
<section class="section">

<h2 class="section-title">Pengumuman</h2>

<div class="announcement">

<p>
Hasil seleksi penerimaan peserta didik baru akan diumumkan 
setelah proses seleksi selesai dilakukan.
</p>

<a href="uploads/<?php echo htmlspecialchars($spmb['announcement_pdf']); ?>" class="btn btn-primary" download>Download Pengumuman</a>

</div>

<h2 class="section-title">Link Hasil Seleksi Akhir Peserta PPDB</h2>

<div class="pdf-viewer">
    <iframe 
    src="uploads/<?php echo htmlspecialchars($spmb['announcement_pdf']); ?>"
    width="100%"
    height="700px">
    </iframe>
</div>

</section>
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
</body>
</html>
