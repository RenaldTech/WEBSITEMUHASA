<?php
$pageTitle = 'Kelola Kategori';
$headerButtons = '<button class="btn btn-primary" onclick="openModal()">+ Tambah Kategori</button>';

// perform login check and load dependencies
require_once 'partials/admin_header.php';

global $db;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        } else if ($_POST['action'] === 'delete') {
            $id = (int)$_POST['category_id'];
            $sql = "DELETE FROM categories WHERE id=$id";
            if ($db->query($sql)) {
                $message = 'Kategori berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus kategori!';
            }
        }
    }
}

$categories = $db->query("SELECT * FROM categories ORDER BY name ASC")->fetch_all(MYSQLI_ASSOC);
?>
<?php // header include handles DOCTYPE, sidebar, header tags ?>

<?php // no extra head/style needed; admin.css provides common rules ?>

<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>

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
                    <?php foreach ($categories as $category): ?>
                        <tr>
                            <td><?php echo $category['name']; ?></td>
                            <td><?php echo $category['slug']; ?></td>
                            <td><?php echo $category['description']; ?></td>
                            <td>
                                <button class="btn btn-primary btn-small" onclick="editModal(<?php echo $category['id']; ?>)">Edit</button>
                                <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="category_id" value="<?php echo $category['id']; ?>">
                                    <button type="submit" class="btn btn-danger btn-small">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>


    <div id="categoryModal" class="modal">
        <div class="modal-content">
            <h2 id="modalTitle">Tambah Kategori</h2>
            <form method="POST">
                <input type="hidden" name="action" id="action" value="add">
                <input type="hidden" name="category_id" id="category_id" value="">

                <div class="form-group">
                    <label>Nama Kategori</label>
                    <input type="text" name="name" id="name" required>
                </div>

                <div class="form-group">
                    <label>Deskripsi</label>
                    <textarea name="description" id="description"></textarea>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn btn-primary">Simpan Kategori</button>
                    <button type="button" class="btn" onclick="closeModal()" style="background: #6b7280; color: white;">Batal</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openModal() {
            document.getElementById('modalTitle').textContent = 'Tambah Kategori';
            document.getElementById('action').value = 'add';
            document.getElementById('category_id').value = '';
            document.getElementById('name').value = '';
            document.getElementById('description').value = '';
            document.getElementById('categoryModal').style.display = 'block';
        }

        function closeModal() {
            document.getElementById('categoryModal').style.display = 'none';
        }

        function editModal(id) {
            document.getElementById('modalTitle').textContent = 'Edit Kategori';
            document.getElementById('action').value = 'edit';
            document.getElementById('category_id').value = id;
            document.getElementById('categoryModal').style.display = 'block';
        }

        window.onclick = function(event) {
            var modal = document.getElementById('categoryModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
<?php require_once 'partials/admin_footer.php'; ?>
