<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('admin/login.php');
}

global $db;

// Proses form
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // handle categories section separately
    if (isset($_POST['entity']) && $_POST['entity'] === 'category') {
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
                $name = $db->escapeString($_POST['name']);
                $description = $db->escapeString($_POST['description']);
                $slug = createSlug($name);

                if (!$name) {
                    $error = 'Nama kategori harus diisi!';
                } else {
                    if ($_POST['action'] === 'add') {
                        $sql = "INSERT INTO categories (name, slug, description) VALUES ('$name', '$slug', '$description')";
                        if ($db->query($sql)) {
                            $message = 'Kategori berhasil ditambahkan!';
                        } else {
                            $error = 'Gagal menambahkan kategori!';
                        }
                    } else {
                        $id = (int)$_POST['category_id'];
                        $sql = "UPDATE categories SET name='$name', slug='$slug', description='$description' WHERE id=$id";
                        if ($db->query($sql)) {
                            $message = 'Kategori berhasil diupdate!';
                        } else {
                            $error = 'Gagal mengupdate kategori!';
                        }
                    }
                }
            } elseif ($_POST['action'] === 'delete') {
                $id = (int)$_POST['category_id'];
                $sql = "DELETE FROM categories WHERE id=$id";
                if ($db->query($sql)) {
                    $message = 'Kategori berhasil dihapus!';
                } else {
                    $error = 'Gagal menghapus kategori!';
                }
            }
        }
    } elseif (isset($_POST['action'])) {
        // previous article logic unchanged
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $title = $db->escapeString($_POST['title']);
            $category_id = (int)$_POST['category_id'];
            $content = $db->escapeString($_POST['content']);
            $excerpt = $db->escapeString($_POST['excerpt']);
            $status = $_POST['status'];
            
            $slug = createSlug($title);
            
            // Handle upload gambar
            $featured_image = '';
            if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === 0) {
                $upload = uploadImage($_FILES['featured_image'], 'articles');
                if ($upload['success']) {
                    $featured_image = $upload['path'];
                } else {
                    $error = $upload['message'];
                }
            }
            
            if ($_POST['action'] === 'add') {
                $author_id = $_SESSION['user_id'];
                $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : NULL;
                
                if ($featured_image) {
                    $sql = "INSERT INTO articles (title, slug, category_id, author_id, content, excerpt, featured_image, status, published_at) 
                            VALUES ('$title', '$slug', $category_id, $author_id, '$content', '$excerpt', '$featured_image', '$status', " . ($published_at ? "'$published_at'" : "NULL") . ")";
                } else {
                    $sql = "INSERT INTO articles (title, slug, category_id, author_id, content, excerpt, status, published_at) 
                            VALUES ('$title', '$slug', $category_id, $author_id, '$content', '$excerpt', '$status', " . ($published_at ? "'$published_at'" : "NULL") . ")";
                }
                
                if ($db->query($sql)) {
                    $message = 'Berita berhasil ditambahkan!';
                } else {
                    $error = 'Gagal menambahkan berita: ' . $db->getConnection()->error;
                }
            } else if ($_POST['action'] === 'edit') {
                $article_id = (int)$_POST['article_id'];
                
                $update_fields = "title = '$title', slug = '$slug', category_id = $category_id, content = '$content', excerpt = '$excerpt', status = '$status'";
                
                if ($featured_image) {
                    $update_fields .= ", featured_image = '$featured_image'";
                }
                
                if ($status === 'published') {
                    $check_published = $db->query("SELECT published_at FROM articles WHERE id = $article_id")->fetch_assoc();
                    if (!$check_published['published_at']) {
                        $update_fields .= ", published_at = '" . date('Y-m-d H:i:s') . "'";
                    }
                }
                
                $sql = "UPDATE articles SET $update_fields WHERE id = $article_id";
                
                if ($db->query($sql)) {
                    $message = 'Berita berhasil diperbarui!';
                } else {
                    $error = 'Gagal memperbarui berita: ' . $db->getConnection()->error;
                }
            }
        } else if ($_POST['action'] === 'delete') {
            $article_id = (int)$_POST['article_id'];
            
            // Hapus file gambar jika ada
            $article = $db->query("SELECT featured_image FROM articles WHERE id = $article_id")->fetch_assoc();
            if ($article && $article['featured_image']) {
                $file_path = UPLOAD_DIR . $article['featured_image'];
                if (file_exists($file_path)) {
                    unlink($file_path);
                }
            }
            
            $sql = "DELETE FROM articles WHERE id = $article_id";
            if ($db->query($sql)) {
                $message = 'Berita berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus berita';
            }
        }
    }
}

// Ambil data artikel
$articles = $db->query("SELECT a.*, c.name as category_name FROM articles a 
                        LEFT JOIN categories c ON a.category_id = c.id 
                        ORDER BY a.created_at DESC")->fetch_all(MYSQLI_ASSOC);

// Ambil kategori
$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);

// bring in shared header/navigation
include 'partials/admin_header.php';
?>

<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>

<h2>Kelola Berita</h2>
<button class="btn-add" onclick="openModal('articleModal')">+ Tambah Berita</button>
<table>
    <thead>
        <tr>
            <th>Judul</th>
            <th>Kategori</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($articles as $a): ?>
        <tr>
            <td><?php echo $a['title']; ?></td>
            <td><?php echo $a['category_name']; ?></td>
            <td><span class="status-<?php echo $a['status']; ?>"><?php echo ucfirst($a['status']); ?></span></td>
            <td><?php echo formatDate($a['created_at']); ?></td>
            <td>
                <button class="btn btn-edit btn-small" onclick="editArticle(<?php echo $a['id']; ?>)">Edit</button>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus artikel?');">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="article_id" value="<?php echo $a['id']; ?>">
                    <button class="btn btn-danger btn-small">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<h2>Kelola Kategori</h2>
<button class="btn-add" onclick="openModal('categoryModal')">+ Tambah Kategori</button>
<table>
    <thead>
        <tr>
            <th>Nama Kategori</th>
            <th>Slug</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
            <td><?php echo $c['name']; ?></td>
            <td><?php echo $c['slug']; ?></td>
            <td><?php echo $c['description']; ?></td>
            <td>
                <button class="btn btn-edit btn-small" onclick="editCategory(<?php echo $c['id']; ?>)">Edit</button>
                <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus kategori?');">
                    <input type="hidden" name="entity" value="category">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="category_id" value="<?php echo $c['id']; ?>">
                    <button class="btn btn-danger btn-small">Hapus</button>
                </form>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- modals for article and category -->

<div id="articleModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('articleModal')">&times;</span>
        <h3 id="articleModalTitle">Tambah Berita</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" id="articleAction" value="add">
            <input type="hidden" name="article_id" id="articleId">
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" id="articleTitle">
            </div>
            <div class="form-group">
                <label>Kategori</label>
                <select name="category_id" id="articleCategory">
                    <?php foreach ($categories as $c): ?>
                        <option value="<?php echo $c['id']; ?>"><?php echo $c['name']; ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label>Konten</label>
                <textarea name="content" id="articleContent"></textarea>
            </div>
            <div class="form-group">
                <label>Ringkasan</label>
                <textarea name="excerpt" id="articleExcerpt"></textarea>
            </div>
            <div class="form-group">
                <label>Gambar Unggulan</label>
                <input type="file" name="featured_image">
            </div>
            <div class="form-group">
                <label>Status</label>
                <select name="status" id="articleStatus">
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                </select>
            </div>
            <button type="submit" class="btn-submit">Simpan</button>
            <button type="button" class="btn-cancel" onclick="closeModal('articleModal')">Batal</button>
        </form>
    </div>
</div>

<div id="categoryModal" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="closeModal('categoryModal')">&times;</span>
        <h3 id="categoryModalTitle">Tambah Kategori</h3>
        <form method="POST">
            <input type="hidden" name="entity" value="category">
            <input type="hidden" name="action" id="categoryAction" value="add">
            <input type="hidden" name="category_id" id="categoryId">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" id="categoryName">
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" id="categoryDescription"></textarea>
            </div>
            <button type="submit" class="btn-submit">Simpan</button>
            <button type="button" class="btn-cancel" onclick="closeModal('categoryModal')">Batal</button>
        </form>
    </div>
</div>

<script>
function openModal(id) { document.getElementById(id).classList.add('show'); }
function closeModal(id) { document.getElementById(id).classList.remove('show'); }

function editArticle(id) {
    var article = <?php echo json_encode($articles); ?>.find(a => a.id == id);
    if (!article) return;
    document.getElementById('articleModalTitle').innerText = 'Edit Berita';
    document.getElementById('articleAction').value = 'edit';
    document.getElementById('articleId').value = article.id;
    document.getElementById('articleTitle').value = article.title;
    document.getElementById('articleCategory').value = article.category_id;
    document.getElementById('articleContent').value = article.content;
    document.getElementById('articleExcerpt').value = article.excerpt;
    document.getElementById('articleStatus').value = article.status;
    openModal('articleModal');
}

function editCategory(id) {
    var cat = <?php echo json_encode($categories); ?>.find(c => c.id == id);
    if (!cat) return;
    document.getElementById('categoryModalTitle').innerText = 'Edit Kategori';
    document.getElementById('categoryAction').value = 'edit';
    document.getElementById('categoryId').value = cat.id;
    document.getElementById('categoryName').value = cat.name;
    document.getElementById('categoryDescription').value = cat.description;
    openModal('categoryModal');
}
</script>

<?php include 'partials/admin_footer.php'; ?>
