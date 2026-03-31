<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';

global $db;

// Get featured articles
$featured = getArticles(3);

// Get ekstrakurikuler
$extracurriculars = getExtracurriculars();

// Get programs (same as akademik page)
$programs = getPrograms();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMP Muhammadiyah (Tahfidz) Salatiga - Beranda</title>
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
                <li><a href="index.php" class="active">Beranda</a></li>
                <li><a href="akademik.php">Akademik</a></li>
                <li><a href="berita.php">Berita</a></li>
                <li><a href="spmb.php">SPMB</a></li>
                <li><a href="galeri.php">Galeri</a></li>
                <li><a href="kontak.php">Kontak</a></li>
            </ul>
        </div>
    </nav>  

    <!-- HERO SECTION -->
    <section class="hero home-hero">
        <div class="hero-content">
            <h1>Selamat Datang di SMP Muhammadiyah Salatiga</h1>
            <p>Membangun generasi unggul dengan semangat Islami.</p>
        </div>
    </section>

  <!-- MOTTO BASKARA -->
<section class="motto-section">

    <h2 class="welcome-title">
      
    </h2>

    <div class="welcome-card">

        <div class="welcome-image">
            <img src="assets/images/baskara.png" alt="baskara">
        </div>

        <div class="welcome-text">
            <h2>"BASKARA"</h2>
            <p>
                Bertaqwa, Arief, Shaleh <br>
                Kreatif, Amanah, Rajin <br>
                Akhlak Mulia
            </p>
        </div>

    </div>

</section>

    <!-- VISI DAN MISI -->
       <section class="section">
        <h2 class="section-title">Visi dan Misi</h2>
        <div class="visi-misi">
            <div class="visi-box">
                <h3>Visi</h3>
                <p>
                    Membentuk generasi Islami yang berkarakter, unggul, berwawasan ilmu pengetahuan 
                    dan teknologi, serta memiliki kepedulian terhadap lingkungan dan masyarakat.
                </p>
            </div>
            <div class="misi-box">
                <h3>Misi</h3>
                <ul style="list-style-position: inside; color: var(--text-dark); line-height: 2;">
                    <p>1.Menanamkan kesadaran menjalankan sholat, membaca Al Qur'an secara optimal.</li>
                    <p>2.Menanamkan kesadaran menjalankan sholat, membaca Al Qur'an secara optimal.</li>
                    <p>3.Bebas buta baca tulis Al Qur'an untuk seluruh warga sekolah.</li>
                    <p>4.Menumbuhkan karakter nasionalisme, mandiri, tanggung jawab, sopan santun, jujur dalam berucap dan berbuat</li>
                    <p>5.Meningkatkan disiplin dan etos kerja yang tinggi dan pelayanan prima.</li>
                    <p>6.Menumbuhkan semangat berprestasi dalam bidang akademis kepada seluruh warga sekolah</li>
                    <p>7.Mengembangkan minat dan bakat seluruh warga sekolah serta meningkatkan prestasi non akademik.</li>
                    <p>8.Meningkatkan kualitas sumber daya peserta didik, pendidik, dan tenaga kependidikan</li>
                    <p>9.Mewujudkan pengembangan inovasi pembelajaran.</li>
                    <p>10.Menumbuhkan semangat literasi, apresiasi seni, olah raga dan iptek pada seluruh warga sekolah.</li>
                    <p>11.menjalin kerja sama yang harmonis antar warga sekolah, lingkungan dan pemerintah.</li>
                    <p>12.mewujudkan sekolah yang bersih, hijau dan meminimalis hasil sampah yang tidak bermanfaat.</li>
                    <p>13.menumbuhkan sikap pola hidup sehat dan berbudaya lingkungan.</li>
                </ul>
            </div>
        </div>
    </section>

   <!-- PROGRAM UNGGULAN -->
<section class="section" style="background-color:#f9fafb;">
    <h2 class="section-title">Program Unggulan</h2>

    <div class="cards-grid">
        <?php
            // replace hard‑coded programs with dynamic data from database
            if (count($programs) === 0) {
                echo '<p>Tidak ada program unggulan yang tersedia saat ini.</p>';
            } else {
                foreach ($programs as $prog) {
                    echo '<div class="card">';
                    echo '    <div class="card-header"><h3>' . htmlspecialchars($prog['title']) . '</h3></div>';
                    echo '    <div class="card-body"><p>' . nl2br(htmlspecialchars($prog['description'])) . '</p></div>';
                    echo '</div>';
                }
            }
        ?>
    </div>
</section>
    <!-- INFO PENDAFTARAN -->
    <section class="section">
        <h2 class="section-title">Informasi Penting</h2>
        <div class="cards-grid">
            <div class="card">
                <div class="card-header">
                    <h3>📚 Akademik</h3>
                </div>
                <div class="card-body">
                    <p>Pelajari program akademik dan ekstrakurikuler kami yang dirancang untuk mengembangkan potensi siswa secara optimal.</p>
                    <a href="akademik.php" class="btn btn-primary">Pelajari Lebih</a>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>📝 Pendaftaran</h3>
                </div>
                <div class="card-body">
                    <p>Daftarkan putra/putri Anda di SMP Muhammadiyah Salatiga melalui sistem PPDB yang transparan dan profesional.</p>
                    <a href="spmb.php" class="btn btn-primary">Info PPDB</a>
                </div>
            </div>
            <div class="card">
                <div class="card-header">
                    <h3>📞 Hubungi Kami</h3>
                </div>
                <div class="card-body">
                    <p>Hubungi kami untuk mendapatkan informasi lebih lanjut mengenai sekolah, pendaftaran, atau pertanyaan lainnya.</p>
                    <a href="kontak.php" class="btn btn-primary">Kontak</a>
                </div>
            </div>
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
</body>
</html>
