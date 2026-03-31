<?php
// page setup + shared header
$pageTitle = 'Kelola Ekstrakurikuler';
$headerButtons = '<button class="btn btn-primary" onclick="openModal()">+ Tambah Ekstrakurikuler</button>';
require_once 'partials/admin_header.php';

global $db;

// form processing
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
        $name = $db->escapeString($_POST['name']);
        $type = $_POST['type'];
        $description = $db->escapeString($_POST['description']);
        if (!$name) {
            $error = 'Nama ekstrakurikuler harus diisi!';
        } else {
            if ($_POST['action'] === 'add') {
                $db->query("INSERT INTO extracurriculars (name,type,description) VALUES ('$name','$type','$description')");
                $message = 'Ekstrakurikuler berhasil ditambahkan!';
            } else {
                $id = (int)$_POST['extracurricular_id'];
                $db->query("UPDATE extracurriculars SET name='$name',type='$type',description='$description' WHERE id=$id");
                $message = 'Ekstrakurikuler berhasil diupdate!';
            }
        }
    } elseif ($_POST['action'] === 'delete') {
        $id = (int)$_POST['extracurricular_id'];
        $db->query("DELETE FROM extracurriculars WHERE id=$id");
        $message = 'Ekstrakurikuler berhasil dihapus!';
    }
}

$extracurriculars = $db->query("SELECT * FROM extracurriculars ORDER BY type DESC, name ASC")->fetch_all(MYSQLI_ASSOC);
?>

<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Jenis</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($extracurriculars as $e): ?>
            <tr>
                <td><?php echo htmlspecialchars($e['name']); ?></td>
                <td><?php echo ucfirst($e['type']); ?></td>
                <td><?php echo htmlspecialchars($e['description']); ?></td>
                <td>
                    <button class="btn btn-edit btn-small" onclick="editModal(<?php echo $e['id']; ?>)">Edit</button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="extracurricular_id" value="<?php echo $e['id']; ?>">
                        <button class="btn btn-danger btn-small">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- modal -->
<div id="ekstrakurikulerModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Ekstrakurikuler</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="extracurricular_id" id="extracurricular_id" value="">
            <div class="form-group">
                <label>Nama</label>
                <input type="text" name="name" id="name" required>
            </div>
            <div class="form-group">
                <label>Jenis</label>
                <select name="type" id="type">
                    <option value="wajib">Wajib</option>
                    <option value="pilihan">Pilihan</option>
                </select>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" id="description"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModal() {
    document.getElementById('modalTitle').textContent = 'Tambah Ekstrakurikuler';
    document.getElementById('action').value = 'add';
    document.getElementById('extracurricular_id').value = '';
    document.getElementById('name').value = '';
    document.getElementById('type').value = 'wajib';
    document.getElementById('description').value = '';
    document.getElementById('ekstrakurikulerModal').style.display = 'block';
}
function closeModal() {
    document.getElementById('ekstrakurikulerModal').style.display = 'none';
}
function editModal(id) {
    // find the item in the PHP array and populate form
    var items = <?php echo json_encode($extracurriculars); ?>;
    var ext = items.find(e => e.id == id);
    if (!ext) return;
    document.getElementById('modalTitle').textContent = 'Edit Ekstrakurikuler';
    document.getElementById('action').value = 'edit';
    document.getElementById('extracurricular_id').value = ext.id;
    document.getElementById('name').value = ext.name;
    document.getElementById('type').value = ext.type;
    document.getElementById('description').value = ext.description;
    document.getElementById('ekstrakurikulerModal').style.display = 'block';
}
window.onclick = function(event) {
    var modal = document.getElementById('ekstrakurikulerModal');
    if (event.target == modal) modal.style.display = 'none';
}
</script>



