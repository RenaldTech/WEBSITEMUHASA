<?php
require_once 'includes/config.php';
require_once 'includes/db.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kontak - SMP Muhammadiyah (Tahfidz) Salatiga</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        /* enforce uniform icon dimensions in case stylesheet is cached */
        .social-card img { width: 80px !important; height: 80px !important; object-fit: contain; margin: 0 auto 15px; display: block; }

        .social-card{
            background:white;
            padding:30px;
            border-radius:10px;
            text-align:center;
            text-decoration:none;
            color:#333;
            box-shadow:0 4px 10px rgba(0,0,0,0.08);
            transition:0.3s;
            display:flex;
            flex-direction:column;
            align-items:center;
            gap:0.5rem;
        }
    </style>
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
                <li><a href="galeri.php">Galeri</a></li>
                <li><a href="kontak.php" class="active">Kontak</a></li>
            </ul>
        </div>
    </nav>

    <!-- HERO SECTION -->
<section class="section">
    <h2 class="section-title">Hubungi Kami</h2>
    <p class="section-subtitle">
        Kami siap membantu pertanyaan mengenai akademik, pendaftaran, maupun informasi sekolah.
    </p>
</section>
<section class="section" style="background:#f9fafb;">
    <h2 class="section-title">Media Sosial</h2>
    <p class="section-subtitle">Ikuti kami untuk mendapatkan informasi terbaru</p>

    <div class="social-grid">

        <a href="https://facebook.com/smpmuhammadiyahsalatiga" target="_blank" class="social-card facebook">
            <img src="assets/images/facebook.jpg" alt="Facebook">
            <h3>Facebook</h3>
            <p>SMP Muhammadiyah Salatiga</p>
        </a>

        <a href="https://instagram.com/smpmuhammadiyahsalatiga" target="_blank" class="social-card instagram">
            <img src="assets/images/instagram.jpg" alt="Instagram">
            <h3>Instagram</h3>
            <p>@smpmuhammadiyahsalatiga</p>
        </a>

        <a href="https://tiktok.com/@smpmuhammadiyahsalatiga" target="_blank" class="social-card tiktok">
            <img src="assets/images/tiktok.jpg" alt="TikTok">
            <h3>TikTok</h3>
            <p>@smpmuhammadiyahsalatiga</p>
        </a>

        <a href="https://youtube.com/@smpmuhammadiyahcempakasala5600" target="_blank" class="social-card youtube">
            <img src="assets/images/youtube.jpg" alt="YouTube">
            <h3>YouTube</h3>
            <p>SMP Muhammadiyah Salatiga</p>
        </a>

    </div>
</section>
<section class="section">
    <h2 class="section-title">Lokasi & Informasi Kontak</h2>

    <div class="contact-grid">

        <!-- MAP (static image linking to Google Maps app) -->
        <div class="map-container" style="position:relative; cursor:pointer;">
            <!-- replace 'assets/images/map-preview.png' with the actual image you provided -->
            <a href="https://maps.app.goo.gl/8B1vxUkt4LJeHs5NA" target="_blank" id="mapLink" style="display:block; position:relative;">
                <img src="assets/images/maps.png" alt="Lokasi SMP Muhammadiyah Salatiga" style="width:100%; display:block;">
                <!-- overlay now inside link so clicks hit it too -->
                <div id="mapOverlay" style="
                    position:absolute; top:0; left:0; width:100%; height:100%;
                    background:rgba(0,0,0,0.4); opacity:0; transition:opacity 0.2s;
                    display:flex; align-items:center; justify-content:center;
                    color:#fff; font-weight:700; font-size:1.2rem;
                    pointer-events:none; /* allow clicks through overlay */
                ">Lihat di Google Maps</div>
            </a>
        </div>
        <script>
            const mapContainer = document.querySelector('.map-container');
            const overlay = document.getElementById('mapOverlay');
            mapContainer.addEventListener('mouseenter', () => overlay.style.opacity = '1');
            mapContainer.addEventListener('mouseleave', () => overlay.style.opacity = '0');
        </script>
 
        <!-- CONTACT INFO -->
        <div class="contact-details">

            <div class="contact-row">
                <h3>📍 Alamat</h3>
                <p>
                Jl. Cempaka No.5-7, Jetis<br>
                Kecamatan Sidorejo<br>
                Kota Salatiga, Jawa Tengah 50711
                </p>
            </div>

            <div class="contact-row">
                <h3>📞 Telepon</h3>
                <p>(0298) 322 441</p>
                <p>WA: 0857 2848 9757</p>
            </div>

            <div class="contact-row">
                <h3>📧 Email</h3>
                <p>info@smpmuhammadiyah-salatiga.sch.id</p>
                <p>spmb@smpmuhammadiyah-salatiga.sch.id</p>
            </div>

            <div class="contact-row">
                <h3>⏰ Jam Operasional</h3>
                <p>Senin - Jumat</p>
                <p>07.00 - 16.00 WIB</p>
            </div>

        </div>

    </div>
</section>
<section class="section" style="background:#f9fafb;">
    <h2 class="section-title">Kirim Pesan</h2>
    <p class="section-subtitle">
        Silakan kirim pertanyaan atau pesan kepada kami melalui formulir berikut.
    </p>

    <div class="contact-form-container" style="max-width: 600px; margin: auto;">
        <form id="contactForm">
            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="text" name="name" placeholder="Nama Lengkap" required>
                <input type="email" name="email" placeholder="Email" required>
            </div>

            <div class="form-row" style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                <input type="tel" name="phone" placeholder="Nomor Telepon" required>
                <select name="subject" required style="padding: 12px; border: 1px solid #ddd; border-radius: 5px;">
                    <option value="">Pilih Subjek</option>
                    <option>SPMB</option>
                    <option>Akademik</option>
                    <option>Fasilitas</option>
                    <option>Lainnya</option>
                </select>
            </div>

            <textarea name="message" placeholder="Tulis pesan Anda..." rows="6" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 5px; font-family: inherit;"></textarea>

            <button type="submit" class="btn btn-primary" style="margin-top: 15px;">
                Kirim Pesan
            </button>
        </form>
    </div>
</section>
    <!-- FOOTER --><footer>
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
        document.getElementById('contactForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            
            fetch('api/contact.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Pesan Anda berhasil dikirim! Terima kasih');
                    document.getElementById('contactForm').reset();
                } else {
                    alert('Error: ' + data.error);
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
