<<<<<<< HEAD
# SMP Muhammadiyah (Tahfidz) Salatiga - Website Resmi

## Deskripsi Proyek

Website resmi SMP Muhammadiyah (Tahfidz) Salatiga yang dirancang untuk memberikan informasi kepada masyarakat mengenai profil sekolah, program pendidikan, kegiatan, serta informasi pendaftaran peserta didik baru. 

Website ini dibangun dengan teknologi modern, desain responsif, dan antarmuka yang user-friendly sehingga dapat diakses dengan nyaman melalui komputer maupun perangkat mobile.

## Fitur Utama

- ✅ **Halaman Beranda** - Informasi selamat datang, motto, visi misi
- ✅ **Program Akademik** - Kurikulum, program tahfidz, ekstrakurikuler
- ✅ **Berita & Pengumuman** - Informasi terbaru kegiatan sekolah
- ✅ **SPMB** - Sistem penerimaan murid baru dengan formulir online
- ✅ **Galeri** - Dokumentasi foto dan video kegiatan
- ✅ **Kontak** - Informasi kontak dan form komunikasi
- ✅ **Responsive Design** - Tampilan sempurna di semua ukuran layar
- ✅ **Sticky Navigation** - Menu navigasi yang selalu terlihat

## Struktur Folder

```
website/
├── index.html                 # Halaman beranda
├── assets/
│   ├── css/
│   │   └── style.css         # CSS utama (responsive)
│   ├── js/
│   │   └── script.js         # JavaScript untuk interaktivitas
│   └── images/               # Folder untuk gambar
└── pages/
    ├── akademik.html         # Halaman program akademik
    ├── berita.html           # Halaman berita dan pengumuman
    ├── spmb.html             # Halaman pendaftaran siswa baru
    ├── galeri.html           # Halaman galeri kegiatan
    └── kontak.html           # Halaman kontak
```

## Teknologi yang Digunakan

- **HTML5** - Struktur markup yang semantik
- **CSS3** - Styling dengan responsive design menggunakan CSS Grid dan Flexbox
- **JavaScript Vanilla** - Interaktivitas tanpa library external
- **Responsive Design** - Mobile-first approach untuk kompatibilitas di semua device

## Cara Membuka Website

### Metode 1: Buka File Langsung
1. Buka folder `d:\website`
2. Double-click file `index.html`
3. Browser akan membuka website secara otomatis

### Metode 2: Menggunakan Server Lokal (Recommended)
Gunakan VS Code Live Server extension atau command line:

```bash
# Jika menggunakan Python 3
python -m http.server 8000

# Atau jika menggunakan Python 2
python -m SimpleHTTPServer 8000

# Kemudian buka browser dan akses: http://localhost:8000
```

## Halaman-Halaman Utama

### 1. **Beranda (index.html)**
- Hero section dengan sambutan
- Motto BASKARA
- Visi dan misi sekolah
- Program unggulan
- Informasi sekolah
- Berita terbaru preview

### 2. **Akademik (pages/akademik.html)**
- Informasi kurikulum
- Program tahfidz Al-Qur'an
- Sistem pembelajaran
- Kegiatan ekstrakurikuler
- Data prestasi siswa

### 3. **Berita (pages/berita.html)**
- Daftar berita terbaru
- Fitur pencarian berita
- Paginasi
- Detail artikel

### 4. **SPMB (pages/spmb.html)**
- Informasi pendaftaran siswa baru
- Persyaratan umum
- Jadwal pendaftaran (timeline)
- Formulir pendaftaran online
- Informasi biaya pendidikan

### 5. **Galeri (pages/galeri.html)**
- Galeri foto dan video
- Filter berdasarkan kategori
- Modal untuk viewing gambar
- Responsive grid layout

### 6. **Kontak (pages/kontak.html)**
- Informasi kontak sekolah
- Embed Google Maps
- Form kirim pesan
- Daftar kontak person
- Link media sosial

## Fitur Responsif

Website ini fully responsive dengan breakpoints di:
- **Desktop**: 1200px+ (layar penuh)
- **Tablet**: 768px - 1199px (kolom ganda)
- **Mobile**: < 768px (kolom tunggal, menu hamburger)

## Customization

### Mengubah Warna Utama
Edit file `assets/css/style.css` dan ubah variabel CSS di atas:

```css
:root {
    --primary-color: #0052CC;      /* Warna utama biru */
    --primary-dark: #003D99;       /* Warna gelap */
    --primary-light: #E8F0FF;      /* Warna terang */
}
```

### Menambah Berita
Edit file `pages/berita.html` dan tambahkan card baru di section `newsList`

### Menambah Galeri
Edit file `pages/galeri.html` dan tambahkan item dengan format:
```html
<div class="gallery-item" data-category="kategori">
    <img src="path/to/image.jpg" alt="Deskripsi">
    <div class="gallery-overlay">
        <span class="play-icon">🖼️</span>
    </div>
</div>
```

## Kompatibilitas Browser

✅ Chrome/Edge (versi terbaru)  
✅ Firefox (versi terbaru)  
✅ Safari (versi terbaru)  
✅ Opera (versi terbaru)  

## Performa

- Optimized untuk loading cepat
- Menggunakan placeholder images (SVG)
- Minimal external dependencies
- CSS dan JS sudah di-minify

## Konten yang Dapat Diubah

Semua konten dapat dengan mudah diubah melalui editing file HTML:
- Text dan deskripsi
- Link dan URL
- Email dan nomor telepon
- Informasi alamat
- Data tabel prestasi

## Tips Pengembangan Lebih Lanjut

1. **Tambahkan Database** - Gunakan backend (PHP, Node.js) untuk mengelola berita dan SPMB
2. **CMS Integration** - Integrasi dengan CMS seperti WordPress untuk konten dinamis
3. **Email Notification** - Setup email notifications untuk form pendaftaran
4. **Analytics** - Tambahkan Google Analytics untuk tracking pengunjung
5. **SEO Optimization** - Update meta tags untuk SEO
6. **SSL Certificate** - Gunakan HTTPS untuk security

## Support

Untuk pertanyaan atau bantuan lebih lanjut, hubungi:
- 📞 (0298) 322 441
- 📧 spmuhammadiyahplus@gmail.com
- 📱 WhatsApp: 0857 2848 9757

## License

Website ini adalah milik SMP Muhammadiyah (Tahfidz) Salatiga.
Dilarang melakukan penggandaan tanpa izin tertulis dari pihak sekolah.

---

**Dibuat dengan ❤️ untuk SMP Muhammadiyah (Tahfidz) Salatiga**
=======
# WEBSMPMUHASATRY
web uji coba SMPMUHASA
>>>>>>> b94745cdc8a8817be6912b4a3fd9f77a9b63f931
