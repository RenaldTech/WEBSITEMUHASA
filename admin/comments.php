<?php
// page setup and shared header
$pageTitle = 'Kelola Komentar';
$headerButtons = '';
require_once 'partials/admin_header.php';

global $db;

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        if ($_POST['action'] === 'approve' || $_POST['action'] === 'reject') {
            $id = (int)$_POST['comment_id'];
            $status = $_POST['action'] === 'approve' ? 'approved' : 'rejected';
            $db->query("UPDATE comments SET status='$status' WHERE id=$id");
            $message = 'Komentar berhasil ' . ($status === 'approved' ? 'disetujui' : 'ditolak') . '!';
        } elseif ($_POST['action'] === 'delete') {
            $id = (int)$_POST['comment_id'];
            $db->query("DELETE FROM comments WHERE id=$id");
            $message = 'Komentar berhasil dihapus!';
        } elseif ($_POST['action'] === 'reply') {
            $id = (int)$_POST['comment_id'];
            $reply = $db->escapeString($_POST['reply']);
            $db->query("UPDATE comments SET admin_reply='$reply' WHERE id=$id");
            $message = 'Balasan berhasil disimpan!';
        }
    }
}

$comments = $db->query("SELECT c.*, a.title as article_title FROM comments c 
                        LEFT JOIN articles a ON c.article_id = a.id 
                        ORDER BY c.created_at DESC")->fetch_all(MYSQLI_ASSOC);
?>

<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>

<table>
    <thead>
        <tr>
            <th>Artikel</th>
            <th>Penulis</th>
            <th>Komentar</th>
            <th>Status</th>
            <th>Tanggal</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($comments as $c): ?>
            <tr>
                <td><?php echo htmlspecialchars($c['article_title']); ?></td>
                <td><?php echo htmlspecialchars($c['author_name']); ?></td>
                <td><a href="#" class="view-comment" data-content="<?php echo htmlspecialchars($c['content'],ENT_QUOTES); ?>"><?php echo htmlspecialchars(substr($c['content'],0,50)).'...'; ?></a></td>
                <td><span class="status <?php echo $c['status']; ?>"><?php echo ucfirst($c['status']); ?></span></td>
                <td><?php echo formatDate($c['created_at']); ?></td>
                <td>
                    <?php if ($c['status'] === 'pending'): ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="approve">
                            <input type="hidden" name="comment_id" value="<?php echo $c['id']; ?>">
                            <button class="btn btn-success btn-small">Setuju</button>
                        </form>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="action" value="reject">
                            <input type="hidden" name="comment_id" value="<?php echo $c['id']; ?>">
                            <button class="btn btn-warning btn-small">Tolak</button>
                        </form>
                    <?php endif; ?>
                    <button type="button" class="btn btn-info btn-small reply-btn" data-id="<?php echo $c['id']; ?>" data-content="<?php echo htmlspecialchars($c['content'],ENT_QUOTES); ?>">Balas</button>
                    <form method="POST" style="display:inline;" onsubmit="return confirm('Hapus komentar?');">
                        <input type="hidden" name="action" value="delete">
                        <input type="hidden" name="comment_id" value="<?php echo $c['id']; ?>">
                        <button class="btn btn-danger btn-small">Hapus</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<!-- view/reply modal -->
<div id="commentModal" class="modal">
    <div class="modal-content" style="max-width:600px;">
        <span class="close" onclick="closeCommentModal()">&times;</span>
        <h2>Detail Komentar</h2>
        <p id="modalComment"></p>
        <hr>
        <h3>Balas</h3>
        <form id="replyForm" method="POST">
            <input type="hidden" name="action" value="reply">
            <input type="hidden" name="comment_id" id="reply_comment_id" value="">
            <div class="form-group">
                <textarea name="reply" id="reply_text" rows="4" style="width:100%;"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Kirim Balasan</button>
        </form>
    </div>
</div>

<script>
    document.querySelectorAll('.view-comment').forEach(function(el){
        el.addEventListener('click', function(e){
            e.preventDefault();
            document.getElementById('modalComment').innerText = el.getAttribute('data-content');
            document.getElementById('commentModal').style.display = 'block';
        });
    });
    document.querySelectorAll('.reply-btn').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.getElementById('modalComment').innerText = btn.getAttribute('data-content');
            document.getElementById('reply_comment_id').value = btn.getAttribute('data-id');
            document.getElementById('commentModal').style.display = 'block';
        });
    });
    function closeCommentModal(){ document.getElementById('commentModal').style.display='none'; }
    window.onclick=function(ev){var m=document.getElementById('commentModal'); if(ev.target==m) m.style.display='none';}
</script>

<?php include 'partials/admin_footer.php'; ?>
