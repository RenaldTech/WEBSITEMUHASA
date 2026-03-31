<?php
$pageTitle = 'Kelola Galeri';
$headerButtons = '<button class="btn btn-primary" onclick="openModal()">+ Upload Galeri</button>';

// header include will perform login check and load dependencies
require_once 'partials/admin_header.php';

global $db;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'delete') {
            $id = (int)$_POST['gallery_id'];
            $gallery = $db->query("SELECT image_path FROM gallery WHERE id=$id")->fetch_assoc();
            if ($gallery && file_exists(UPLOAD_DIR . $gallery['image_path'])) {
                unlink(UPLOAD_DIR . $gallery['image_path']);
            }
            $sql = "DELETE FROM gallery WHERE id=$id";
            if ($db->query($sql)) {
                $message = 'Galeri berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus galeri!';
            }
        }
    }
}

if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
    $title = $db->escapeString($_POST['title']);
    $category = $db->escapeString($_POST['category']);
    $description = $db->escapeString($_POST['description']);

    if (!$title) {
        $error = 'Judul galeri harus diisi!';
    } else {
        // properly handle uploadImage response
        $upload = uploadImage($_FILES['image'], 'gallery');
        if ($upload['success']) {
            $image_path = $upload['path'];
            $sql = "INSERT INTO gallery (title, category, image_path, description) VALUES ('$title', '$category', '$image_path', '$description')";
            if ($db->query($sql)) {
                $message = 'Galeri berhasil ditambahkan!';
            } else {
                $error = 'Gagal menambahkan galeri!';
            }
        } else {
            $error = $upload['message'];
        }
    }
}

$gallery = $db->query("SELECT * FROM gallery ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>
<?php // header include already outputs DOCTYPE, sidebar and opening tags ?>

<?php // nothing to do here, content begins below ?>

<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>

<div class="gallery-grid">                <?php foreach ($gallery as $item): ?>
                    <div class="gallery-item">
                        <img src="<?php echo SITE_URL; ?>uploads/<?php echo $item['image_path']; ?>" alt="<?php echo $item['title']; ?>">
                        <div class="gallery-info">
                            <h3><?php echo $item['title']; ?></h3>
                            <p>📁 <?php echo $item['category']; ?></p>
                            <p style="font-size: 0.8rem; color: #999;"><?php echo date('d M Y', strtotime($item['created_at'])); ?></p>
                            <form method="POST" style="display:inline; margin-top: 0.5rem;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                <input type="hidden" name="action" value="delete">
                                <input type="hidden" name="gallery_id" value="<?php echo $item['id']; ?>">
                                <button type="submit" class="btn btn-danger" style="padding:0.375rem 0.75rem; font-size:0.8rem;">Hapus</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

    <div id="galleryModal" class="modal">
        <div class="modal-content">
            <h2>Upload Galeri</h2>
            <form method="POST" enctype="multipart/form-data">
                <div class="form-group">
                    <label>Judul Galeri</label>
                    <input type="text" name="title" required>
                </div>

                <div class="form-group">
                    <label>Kategori</label>
                    <select name="category" required>
                        <option value="akademik">Akademik</option>
                        <option value="keagamaan">Keagamaan</option>
                        <option value="olahraga">Olahraga</option>
                        <option value="ekstrakurikuler">Ekstrakurikuler</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description"></textarea>
                </div>

                <div class="form-group">
                    <label>Upload Gambar (JPG, PNG, max 5MB)</label>
                    <input type="file" name="image" accept="image/jpeg,image/png" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Upload Galeri</button>
                    <button type="button" class="btn" onclick="closeModal()" style="background: #6b7280; color: white;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('galleryModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('galleryModal').style.display = 'none';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('galleryModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
<?php require_once 'partials/admin_footer.php'; ?>
