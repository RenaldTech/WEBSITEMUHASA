<?php
// shared header and login check
$pageTitle = 'Kelola Pesan';
$headerButtons = '';
require_once 'partials/admin_header.php';

global $db;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)$_POST['message_id'];
    if ($_POST['action'] === 'mark_read') {
        $db->query("UPDATE contact_messages SET status='read' WHERE id=$id");
        $message = 'Pesan ditandai sudah dibaca!';
    } elseif ($_POST['action'] === 'mark_replied') {
        $db->query("UPDATE contact_messages SET status='replied' WHERE id=$id");
        $message = 'Pesan ditandai sudah dibalas!';
    } elseif ($_POST['action'] === 'delete') {
        $db->query("DELETE FROM contact_messages WHERE id=$id");
        $message = 'Pesan berhasil dihapus!';
    }
}

$messages = $db->query("SELECT * FROM contact_messages ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Nama</th>
            <th>Email</th>
            <th>Subjek</th>
            <th>Pesan</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($messages as $m): ?>
            <tr>
                <td><?php echo htmlspecialchars($m['name']); ?></td>
                <td><?php echo htmlspecialchars($m['email']); ?></td>
                <td><?php echo htmlspecialchars($m['subject']); ?></td>
                <td><a href="#" class="view-msg" data-msg="<?php echo htmlspecialchars($m['message'], ENT_QUOTES); ?>"><?php echo htmlspecialchars(substr($m['message'],0,40)).'...'; ?></a></td>
                <td><span class="status <?php echo $m['status']; ?>"><?php echo ucfirst($m['status']); ?></span></td>
                <td><?php echo formatDate($m['created_at']); ?></td>
                <td>
                    <?php if ($m['status'] === 'new'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="mark_read">
                            <input type="hidden" name="message_id" value="<?php echo $m['id']; ?>">
                            <button class="btn btn-info btn-small">Tandai Baca</button>
                        </form>
                    <?php endif; ?>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus pesan?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="message_id" value="<?php echo $m['id']; ?>">
                        <button class="btn btn-danger btn-small">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<!-- message modal -->
<div id="messageModal" class="modal">
    <div class="modal-content" style="max-width:600px;">
        <span class="close" onclick="closeMsgModal()">&times;</span>
        <h2>Isi Pesan</h2>
        <p id="modalMessage"></p>
    </div>
</div>

<script>
    document.querySelectorAll('.view-msg').forEach(function(el){
        el.addEventListener('click', function(ev){
            ev.preventDefault();
            var text = el.getAttribute('data-msg');
            document.getElementById('modalMessage').innerText = text;
            document.getElementById('messageModal').style.display = 'block';
        });
    });
    function closeMsgModal(){
        document.getElementById('messageModal').style.display = 'none';
    }
    window.onclick = function(event) {
        var modal = document.getElementById('messageModal');
        if (event.target == modal) modal.style.display = 'none';
    }
</script>

<?php include 'partials/admin_footer.php'; ?>
