<?php
$pageTitle = 'Kelola Prestasi Siswa';
$headerButtons = '<button class="btn btn-primary" onclick="openModal()">+ Tambah Prestasi</button>';

// load common header (includes login check)
require_once 'partials/admin_header.php';

global $db;

// Proses form
$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'add' || $_POST['action'] === 'edit') {
            $student_name = $db->escapeString($_POST['student_name']);
            $year = (int)$_POST['year'];
            $achievement_title = $db->escapeString($_POST['achievement_title']);
            $category = $db->escapeString($_POST['category']);
            $level = $db->escapeString($_POST['level']);

            if (!$student_name || !$achievement_title) {
                $error = 'Nama siswa dan judul prestasi harus diisi!';
            } else {
                if ($_POST['action'] === 'add') {
                    $sql = "INSERT INTO achievements (student_name, year, achievement_title, category, level) 
                            VALUES ('$student_name', $year, '$achievement_title', '$category', '$level')";
                    if ($db->query($sql)) {
                        $message = 'Prestasi berhasil ditambahkan!';
                    } else {
                        $error = 'Gagal menambahkan prestasi!';
                    }
                } else {
                    $id = (int)$_POST['achievement_id'];
                    $sql = "UPDATE achievements SET student_name='$student_name', year=$year, 
                            achievement_title='$achievement_title', category='$category', level='$level' 
                            WHERE id=$id";
                    if ($db->query($sql)) {
                        $message = 'Prestasi berhasil diupdate!';
                    } else {
                        $error = 'Gagal mengupdate prestasi!';
                    }
                }
            }
        } else if ($_POST['action'] === 'delete') {
            $id = (int)$_POST['achievement_id'];
            $sql = "DELETE FROM achievements WHERE id=$id";
            if ($db->query($sql)) {
                $message = 'Prestasi berhasil dihapus!';
            } else {
                $error = 'Gagal menghapus prestasi!';
            }
        }
    }
}

// Ambil data prestasi
$achievements = $db->query("SELECT * FROM achievements ORDER BY year DESC, no ASC")->fetch_all(MYSQLI_ASSOC);
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
            <th>Nama Siswa</th>
            <th>Judul Prestasi</th>
            <th>Kategori</th>
            <th>Level</th>
            <th>Tahun</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($achievements as $achievement): ?>
            <tr>
                <td><?php echo $achievement['student_name']; ?></td>
                <td><?php echo $achievement['achievement_title']; ?></td>
                <td><?php echo $achievement['category']; ?></td>
                <td><?php echo $achievement['level']; ?></td>
                <td><?php echo $achievement['year']; ?></td>
                <td>
                    <button class="btn btn-edit btn-small" onclick="editModal(<?php echo $achievement['id']; ?>)">Edit</button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="achievement_id" value="<?php echo $achievement['id']; ?>">
                        <button type="submit" class="btn btn-danger btn-small">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- modal for add/edit -->
<div id="achievementModal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2 id="modalTitle">Tambah Prestasi</h2>
            <span class="close" onclick="closeModal()">&times;</span>
        </div>

        <form method="POST">
            <input type="hidden" name="action" id="action" value="add">
            <input type="hidden" name="achievement_id" id="achievement_id" value="">

            <div class="form-group">
                <label>Nama Siswa</label>
                <input type="text" name="student_name" id="student_name" required>
            </div>

            <div class="form-group">
                <label>Judul Prestasi</label>
                <input type="text" name="achievement_title" id="achievement_title" required>
            </div>

            <div class="form-group">
                <label>Kategori</label>
                <input type="text" name="category" id="category" placeholder="Akademik, Olahraga, Seni, dll">
            </div>

            <div class="form-group">
                <label>Level</label>
                <select name="level" id="level">
                    <option value="Sekolah">Tingkat Sekolah</option>
                    <option value="Kota">Tingkat Kota</option>
                    <option value="Provinsi">Tingkat Provinsi</option>
                    <option value="Nasional">Tingkat Nasional</option>
                    <option value="Internasional">Tingkat Internasional</option>
                </select>
            </div>

            <div class="form-group">
                <label>Tahun</label>
                <input type="number" name="year" id="year" value="<?php echo date('Y'); ?>" required>
            </div>

            <div class="form-group">
                <button type="submit" class="btn btn-primary">Simpan Prestasi</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openModal() {
        document.getElementById('modalTitle').textContent = 'Tambah Prestasi';
        document.getElementById('action').value = 'add';
        document.getElementById('achievement_id').value = '';
        document.getElementById('student_name').value = '';
        document.getElementById('achievement_title').value = '';
        document.getElementById('category').value = '';
        document.getElementById('level').value = 'Sekolah';
        document.getElementById('year').value = new Date().getFullYear();
        document.getElementById('achievementModal').style.display = 'block';
    }

    function closeModal() {
        document.getElementById('achievementModal').style.display = 'none';
    }

    function editModal(id) {
        document.getElementById('modalTitle').textContent = 'Edit Prestasi';
        document.getElementById('action').value = 'edit';
        document.getElementById('achievement_id').value = id;
        document.getElementById('achievementModal').style.display = 'block';
    }

    window.onclick = function(event) {
        var modal = document.getElementById('achievementModal');
        if (event.target == modal) {
            modal.style.display = 'none';
        }
    }
</script>

<?php include 'partials/admin_footer.php'; ?>
