<?php
// Common admin header and sidebar
if (!isset($pageTitle)) {
    $pageTitle = 'Admin';
}

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if (!isLoggedIn()) {
    redirect('admin/login.php');
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
    <div class="admin-container">
        <aside class="admin-sidebar">
            <div class="sidebar-title">Admin Panel</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php" <?php if(basename($_SERVER['PHP_SELF'])=='dashboard.php') echo 'class="active"'; ?>>📊 Dashboard</a></li>
                <li><a href="articles.php" <?php if(basename($_SERVER['PHP_SELF'])=='articles.php') echo 'class="active"'; ?>>📝 Berita</a></li>
                <li><a href="categories.php" <?php if(basename($_SERVER['PHP_SELF'])=='categories.php') echo 'class="active"'; ?>>📂 Kategori</a></li>
                <li><a href="achievements.php" <?php if(basename($_SERVER['PHP_SELF'])=='achievements.php') echo 'class="active"'; ?>>🏆 Prestasi</a></li>
                <li><a href="extracurricular.php" <?php if(basename($_SERVER['PHP_SELF'])=='extracurricular.php') echo 'class="active"'; ?>>⚽ Ekstrakurikuler</a></li>
                <li><a href="programs.php" <?php if(basename($_SERVER['PHP_SELF'])=='programs.php') echo 'class="active"'; ?>>🎓 Program Unggulan</a></li>
                <li><a href="gallery.php" <?php if(basename($_SERVER['PHP_SELF'])=='gallery.php') echo 'class="active"'; ?>>📷 Galeri</a></li>
                <li><a href="comments.php" <?php if(basename($_SERVER['PHP_SELF'])=='comments.php') echo 'class="active"'; ?>>💬 Komentar</a></li>
                <li><a href="messages.php" <?php if(basename($_SERVER['PHP_SELF'])=='messages.php') echo 'class="active"'; ?>>✉️ Pesan Kontak</a></li>
                <li><a href="spmb.php" <?php if(basename($_SERVER['PHP_SELF'])=='spmb.php') echo 'class="active"'; ?>>🎓 SPMB</a></li>
                <li><a href="logout.php" class="btn-logout">🚪 Logout</a></li>
            </ul>
        </aside>

        <div class="admin-content">
            <div class="admin-header">
                <h1><?php echo $pageTitle; ?></h1>
                <?php if (!empty($headerButtons)) echo $headerButtons; ?>
            </div>
