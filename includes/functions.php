<?php
// Utility functions for the website. Include this file once in your scripts.

// start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';

// ensure comments table has admin_reply column (run once)
if ($db) {
    $col = $db->query("SHOW COLUMNS FROM comments LIKE 'admin_reply'");
    if ($col->num_rows === 0) {
        $db->query("ALTER TABLE comments ADD COLUMN admin_reply TEXT NULL");
    }
    // create programs table if it doesn't exist yet
    $db->query("CREATE TABLE IF NOT EXISTS programs (
        id INT PRIMARY KEY AUTO_INCREMENT,
        title VARCHAR(255) NOT NULL,
        description TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
}

/**
 * Generate slug from string
 */
function createSlug($string) {
    $string = strtolower(trim($string));
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Format a date string into Indonesian format (e.g. 01 Januari 2021)
 */
function formatDate($date) {
    // avoid passing null/empty to DateTime (PHP 8.1+ deprecation)
    if (empty($date)) {
        return '';
    }

    $months = [
        'January' => 'Januari', 'February' => 'Februari', 'March' => 'Maret',
        'April' => 'April', 'May' => 'Mei', 'June' => 'Juni',
        'July' => 'Juli', 'August' => 'Agustus', 'September' => 'September',
        'October' => 'Oktober', 'November' => 'November', 'December' => 'Desember'
    ];
    try {
        $date_obj = new DateTime($date);
    } catch (Exception $e) {
        return '';
    }
    $formatted = $date_obj->format('d F Y');
    foreach ($months as $eng => $indo) {
        $formatted = str_replace($eng, $indo, $formatted);
    }
    return $formatted;
}

/**
 * Simple email validation
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Upload an image and return status array
 */
function uploadImage($file, $folder = 'articles') {
    global $db;

    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File tidak ditemukan'];
    }

    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowed)) {
        return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
    }

    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar'];
    }

    $upload_path = UPLOAD_DIR . $folder . '/';
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }

    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filepath = $upload_path . $filename;

    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        // try resize to square if GD functions are available
        if (function_exists('imagecreatetruecolor') && function_exists('getimagesize')) {
            list($origW, $origH, $type) = getimagesize($filepath);
            $size = 300; // target width/height
            $dst = imagecreatetruecolor($size, $size);
            // create source image based on type
            switch ($type) {
                case IMAGETYPE_JPEG:
                    $src = imagecreatefromjpeg($filepath);
                    break;
                case IMAGETYPE_PNG:
                    $src = imagecreatefrompng($filepath);
                    // preserve transparency
                    imagecolortransparent($dst, imagecolorallocatealpha($dst, 0, 0, 0, 127));
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                    break;
                case IMAGETYPE_GIF:
                    $src = imagecreatefromgif($filepath);
                    break;
                case IMAGETYPE_WEBP:
                    $src = imagecreatefromwebp($filepath);
                    break;
                default:
                    $src = null;
            }
            if ($src) {
                // calculate cropping square
                if ($origW > $origH) {
                    $src_x = ($origW - $origH) / 2;
                    $src_y = 0;
                    $src_size = $origH;
                } else {
                    $src_x = 0;
                    $src_y = ($origH - $origW) / 2;
                    $src_size = $origW;
                }
                imagecopyresampled($dst, $src, 0, 0, $src_x, $src_y, $size, $size, $src_size, $src_size);
                // overwrite original file
                switch ($type) {
                    case IMAGETYPE_JPEG:
                        imagejpeg($dst, $filepath, 85);
                        break;
                    case IMAGETYPE_PNG:
                        imagepng($dst, $filepath);
                        break;
                    case IMAGETYPE_GIF:
                        imagegif($dst, $filepath);
                        break;
                    case IMAGETYPE_WEBP:
                        imagewebp($dst, $filepath);
                        break;
                }
                imagedestroy($src);
                imagedestroy($dst);
            }
        }

        return ['success' => true, 'filename' => $filename, 'path' => $folder . '/' . $filename];
    }

    return ['success' => false, 'message' => 'Gagal upload file'];
}

/**
 * Upload any file (pdf, doc, etc.) to a folder under uploads
 * returns ['success'=>bool, 'path'=>folder/filename, 'message'=>string]
 */
function uploadFile($file, $folder = 'spmb') {
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File tidak ditemukan'];
    }
    if ($file['size'] > MAX_FILE_SIZE) {
        return ['success' => false, 'message' => 'Ukuran file terlalu besar'];
    }
    $upload_path = UPLOAD_DIR . $folder . '/';
    if (!is_dir($upload_path)) {
        mkdir($upload_path, 0755, true);
    }
    $filename = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '', $file['name']);
    $filepath = $upload_path . $filename;
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'path' => $folder . '/' . $filename];
    }
    return ['success' => false, 'message' => 'Gagal memindahkan file'];
}

/* database helper functions */

function getArticles($limit = null, $category_id = null, $status = 'published') {
    global $db;
    $status = $db->escapeString($status);
    $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.status = '$status'";
    if ($category_id) {
        $sql .= " AND a.category_id = " . (int)$category_id;
    }
    $sql .= " ORDER BY a.published_at DESC";
    if ($limit) {
        $sql .= " LIMIT " . (int)$limit;
    }
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getArticleBySlug($slug) {
    global $db;
    $slug = $db->escapeString($slug);
    $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.slug = '$slug' AND a.status = 'published' LIMIT 1";
    $result = $db->query($sql);
    return $result ? $result->fetch_assoc() : null;
}

function updateArticleViews($article_id) {
    global $db;
    $id = (int)$article_id;
    $db->query("UPDATE articles SET views = views + 1 WHERE id = $id");
}

/**
 * Add a new article record. Accepts array of fields and optional file upload data.
 * Returns boolean success.
 */
function addArticle($data, $file = null) {
    global $db;

    $title       = $db->escapeString($data['title']);
    $category_id = (int)$data['category_id'];
    $content     = $db->escapeString($data['content']);
    $excerpt     = $db->escapeString($data['excerpt']);
    $status      = $db->escapeString($data['status']);
    $slug        = createSlug($title);
    $author_id   = isset($data['author_id']) ? (int)$data['author_id'] : null;

    $featured_image = '';
    if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($file, 'articles');
        if ($upload['success']) {
            $featured_image = $upload['path'];
        }
    }

    $published_at = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    $fields = "title, slug, category_id, author_id, content, excerpt";
    $values = "'$title', '$slug', $category_id, " . ($author_id ? $author_id : 'NULL') . ", '$content', '$excerpt'";
    if ($featured_image) {
        $fields .= ", featured_image";
        $values .= ", '$featured_image'";
    }
    $fields .= ", status, published_at";
    $values .= ", '$status', " . ($published_at ? "'$published_at'" : "NULL");

    $sql = "INSERT INTO articles ($fields) VALUES ($values)";
    return $db->query($sql);
}

/**
 * Update an existing article by id. Data array may contain same keys as addArticle.
 */
function updateArticle($id, $data, $file = null) {
    global $db;
    $id = (int)$id;

    $title       = $db->escapeString($data['title']);
    $category_id = (int)$data['category_id'];
    $content     = $db->escapeString($data['content']);
    $excerpt     = $db->escapeString($data['excerpt']);
    $status      = $db->escapeString($data['status']);
    $slug        = createSlug($title);

    $featured_image = '';
    if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($file, 'articles');
        if ($upload['success']) {
            $featured_image = $upload['path'];
        }
    }

    $published_at_sql = '';
    if ($status === 'published') {
        $check = $db->query("SELECT published_at FROM articles WHERE id = $id")->fetch_assoc();
        if (!$check['published_at']) {
            $published_at_sql = ", published_at = '" . date('Y-m-d H:i:s') . "'";
        }
    }

    $sql = "UPDATE articles SET ";
    $sql .= "title='$title', slug='$slug', category_id=$category_id, content='$content', excerpt='$excerpt', status='$status'";
    if ($featured_image) {
        $sql .= ", featured_image='$featured_image'";
    }
    $sql .= $published_at_sql;
    $sql .= " WHERE id=$id";

    return $db->query($sql);
}

function getComments($article_id, $status = 'approved') {
    global $db;
    $id = (int)$article_id;
    $status = $db->escapeString($status);
    $sql = "SELECT * FROM comments 
            WHERE article_id = $id AND status = '$status' 
            ORDER BY created_at DESC";
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function addComment($article_id, $author_name, $author_email, $content) {
    global $db;
    $article_id = (int)$article_id;
    $author_name = $db->escapeString($author_name);
    $author_email = $db->escapeString($author_email);
    $content = $db->escapeString($content);
    $sql = "INSERT INTO comments (article_id, author_name, author_email, content) 
            VALUES ($article_id, '$author_name', '$author_email', '$content')";
    return $db->query($sql);
}

function getGallery($category = null) {
    global $db;
    $sql = "SELECT * FROM gallery";
    if ($category) {
        $sql .= " WHERE category = '" . $db->escapeString($category) . "'";
    }
    $sql .= " ORDER BY created_at DESC";
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getAchievements() {
    global $db;
    $sql = "SELECT * FROM achievements ORDER BY year DESC, id DESC";
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function getExtracurriculars($type = null) {
    global $db;
    $sql = "SELECT * FROM extracurriculars";
    if ($type) {
        $sql .= " WHERE type = '" . $db->escapeString($type) . "'";
    }
    $sql .= " ORDER BY name ASC";
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/* PROGRAM UNGGULAN helpers */
function getPrograms() {
    global $db;
    $sql = "SELECT * FROM programs ORDER BY id ASC";
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

function addProgram($title, $description) {
    global $db;
    $t = $db->escapeString($title);
    $d = $db->escapeString($description);
    $sql = "INSERT INTO programs (title, description) VALUES ('$t', '$d')";
    return $db->query($sql);
}

function updateProgram($id, $title, $description) {
    global $db;
    $id = (int)$id;
    $t = $db->escapeString($title);
    $d = $db->escapeString($description);
    $sql = "UPDATE programs SET title='$t', description='$d' WHERE id=$id";
    return $db->query($sql);
}

function deleteProgram($id) {
    global $db;
    $id = (int)$id;
    return $db->query("DELETE FROM programs WHERE id=$id");
}

function searchArticles($keyword, $limit = 10) {
    global $db;
    $keyword = $db->escapeString($keyword);
    $sql = "SELECT a.*, c.name as category_name, u.username as author_name 
            FROM articles a 
            LEFT JOIN categories c ON a.category_id = c.id 
            LEFT JOIN users u ON a.author_id = u.id 
            WHERE a.status = 'published' AND 
            (a.title LIKE '%$keyword%' OR a.content LIKE '%$keyword%' OR 
             a.excerpt LIKE '%$keyword%' OR c.name LIKE '%$keyword%') 
            ORDER BY a.published_at DESC 
            LIMIT " . (int)$limit;
    $result = $db->query($sql);
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
}

/* authentication helpers */

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function isAdmin() {
    return isLoggedIn() && (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

function redirect($url) {
    header("Location: " . SITE_URL . ltrim($url, '/'));
    exit;
}

// end of functions file
