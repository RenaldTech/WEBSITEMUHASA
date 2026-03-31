<?php
$pageTitle = 'Kelola Program Unggulan';
$headerButtons = '<button class="btn btn-primary" onclick="openModal()">+ Tambah Program</button>';

require_once 'partials/admin_header.php';

global $db;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $title = $db->escapeString($_POST['title']);
            $description = $db->escapeString($_POST['description']);
            if (!$title) {
                $error = 'Judul program harus diisi!';
            } else {
                if ($_POST['action'] === 'add') {
                    $sql = "INSERT INTO programs (title, description) VALUES ('$title', '$description')";
                    if ($db->query($sql)) {
                        $message = 'Program berhasil ditambahkan!';
                    } else {
                        $error = 'Gagal menambahkan program!';
                    }
                } else {
                    $id = (int)$_POST['program_id'];
                    $sql = "UPDATE programs SET title='$title', description='$description' WHERE id=$id";
                    if ($db->query($sql)) {
                        $message = 'Program berhasil diupdate!';
                    } else {
                        $error = 'Gagal mengupdate program!';
                    }
                }
            }
        } else if ($_POST['action'] === 'delete') {
            $id = (int)$_POST['program_id'];
            $sql = "DELETE FROM programs WHERE id=$id";
            if ($db->query($sql)) {
                $message = 'Program berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus program!';
            }
        }
    }
}

// fetch programs
$programs = $db->query("SELECT * FROM programs ORDER BY id ASC")->fetch_all(MYSQLI_ASSOC);
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
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($programs as $prog): ?>
            <tr>
                <td><?php echo $prog['title']; ?></td>
                <td><?php echo nl2br(htmlspecialchars($prog['description'])); ?></td>
                <td>
                    <button class="btn btn-edit btn-small" onclick="editModal(<?php echo $prog['id']; ?>)">Edit</button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus program ini?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="program_id" value="<?php echo $prog['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-small">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- modal for program -->
<div id="programModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Program</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>
        <form method="POST">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="program_id" id="program_id" value="">
            <div class="form-group">
                <label>Judul</label>
                <input type="text" name="title" id="title" required>
            </div>
            <div class="form-group">
                <label>Deskripsi</label>
                <textarea name="description" id="description"></textarea>
            </div>
            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan Program</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Program';
        document.getElementById('action').value = 'add';
        document.getElementById('program_id').value = '';
        document.getElementById('title').value = '';
        document.getElementById('description').value = '';
        document.getElementById('programModal').style.display = 'block';
    }
    function closeModal() {
        document.getElementById('programModal').style.display = 'none';
    }
    function editModal(id) {
        var programs = <?php echo json_encode($programs); ?>;
        var prog = programs.find(p => p.id == id);
        if (!prog) return;
        document.getElementById('modalTitle').textContent = 'Edit Program';
        document.getElementById('action').value = 'edit';
        document.getElementById('program_id').value = id;
        document.getElementById('title').value = prog.title;
        document.getElementById('description').value = prog.description;
        document.getElementById('programModal').style.display = 'block';
    }
    window.onclick = function(event) {
        var modal = document.getElementById('programModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

<?php include 'partials/admin_footer.php'; ?>