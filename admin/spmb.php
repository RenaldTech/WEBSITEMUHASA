<?php
require_once '../includes/config.php';
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn()) {
    redirect('admin/login.php');
}

global $db;

// ensure necessary columns exist (migration)
$columns = $db->query("SHOW COLUMNS FROM spmb_settings");
$colNames = [];
while ($col = $columns->fetch_assoc()) {
    $colNames[] = $col['Field'];
}
$needsAlter = [];
if (!in_array('banner_image', $colNames)) {
    $needsAlter[] = "ADD COLUMN banner_image VARCHAR(255)";
}
if (!in_array('technical_pdf', $colNames)) {
    $needsAlter[] = "ADD COLUMN technical_pdf VARCHAR(255)";
}
if (!in_array('announcement_pdf', $colNames)) {
    $needsAlter[] = "ADD COLUMN announcement_pdf VARCHAR(255)";
}
if ($needsAlter) {
    $db->query("ALTER TABLE spmb_settings " . implode(', ', $needsAlter));
}

// ensure there is always one row
$setting = $db->query("SELECT * FROM spmb_settings LIMIT 1")->fetch_assoc();
if (!$setting) {
    // table may exist but no rows; insert blank row
    $db->query("INSERT INTO spmb_settings (banner_image, technical_pdf, announcement_pdf) VALUES ('', '', '')");
    $setting = $db->query("SELECT * FROM spmb_settings LIMIT 1")->fetch_assoc();
}
// if for some reason the query still returned nothing, fall back to defaults
if (!$setting || !is_array($setting)) {
    $setting = ['banner_image' => '', 'technical_pdf' => '', 'announcement_pdf' => ''];
}
// merge with defaults to ensure keys exist (avoids undefined index warnings)
$setting = array_merge(
    ['banner_image' => '', 'technical_pdf' => '', 'announcement_pdf' => ''],
    $setting
);

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // process banner image upload
    $banner = $setting['banner_image'];
    if (isset($_FILES['banner_image']) && $_FILES['banner_image']['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($_FILES['banner_image'], 'spmb');
        if ($upload['success']) {
            $banner = $upload['path'];
        } else {
            $error = $upload['message'];
        }
    }

    // process technical pdf
    $technical = $setting['technical_pdf'];
    if (isset($_FILES['technical_pdf']) && $_FILES['technical_pdf']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['technical_pdf']['type'] === 'application/pdf') {
            $upload = uploadFile($_FILES['technical_pdf'], 'spmb');
            if ($upload['success']) {
                $technical = $upload['path'];
            } else {
                $error = $upload['message'];
            }
        } else {
            $error = 'File teknis harus berformat PDF.';
        }
    }

    // process announcement pdf
    $announcement = $setting['announcement_pdf'];
    if (isset($_FILES['announcement_pdf']) && $_FILES['announcement_pdf']['error'] === UPLOAD_ERR_OK) {
        if ($_FILES['announcement_pdf']['type'] === 'application/pdf') {
            $upload = uploadFile($_FILES['announcement_pdf'], 'spmb');
            if ($upload['success']) {
                $announcement = $upload['path'];
            } else {
                $error = $upload['message'];
            }
        } else {
            $error = 'File pengumuman harus berformat PDF.';
        }
    }

    if (!$error) {
        $sql = "UPDATE spmb_settings SET banner_image='$banner', technical_pdf='$technical', announcement_pdf='$announcement' WHERE id=" . (int)$setting['id'];
        if ($db->query($sql)) {
            $message = 'Pengaturan SPMB berhasil disimpan.';
        } else {
            $error = 'Gagal menyimpan ke database: ' . $db->error;
        }
    }

    // reload setting
    $setting = $db->query("SELECT * FROM spmb_settings LIMIT 1")->fetch_assoc();
}

include 'partials/admin_header.php';
?>

<?php if ($message): ?>
    <div class="message success"><?php echo $message; ?></div>
<?php endif; ?>
<?php if ($error): ?>
    <div class="message error"><?php echo $error; ?></div>
<?php endif; ?>

<h2>Pengaturan SPMB / PPDB</h2>
<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Banner PPDB (gambar)</label><br>
        <?php if ($setting['banner_image']): ?>
            <img src="../uploads/<?php echo htmlspecialchars($setting['banner_image']); ?>" style="max-width:200px; display:block; margin-bottom:10px;">
        <?php endif; ?>
        <input type="file" name="banner_image" accept="image/*">
    </div>

    <div class="form-group">
        <label>Petunjuk Teknis (PDF)</label><br>
        <?php if ($setting['technical_pdf']): ?>
            <a href="../uploads/<?php echo htmlspecialchars($setting['technical_pdf']); ?>" target="_blank">Download file saat ini</a><br>
        <?php endif; ?>
        <input type="file" name="technical_pdf" accept="application/pdf">
    </div>

    <div class="form-group">
        <label>Pengumuman Hasil Seleksi (PDF)</label><br>
        <?php if ($setting['announcement_pdf']): ?>
            <a href="../uploads/<?php echo htmlspecialchars($setting['announcement_pdf']); ?>" target="_blank">Download file saat ini</a><br>
        <?php endif; ?>
        <input type="file" name="announcement_pdf" accept="application/pdf">
    </div>

    <button type="submit" class="btn btn-primary">Simpan</button>
</form>

<?php include 'partials/admin_footer.php'; ?>