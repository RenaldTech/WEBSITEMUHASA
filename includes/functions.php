<?php
// Utility functions for the website. Include this file once in your scripts.

// start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'db.php';
require_once __DIR__ . '/../src/classes/Validator.php';
require_once __DIR__ . '/../src/classes/FileUpload.php';
require_once __DIR__ . '/../src/classes/Logger.php';

$logger = new Logger(LOG_BASE_PATH);

// ensure comments table has admin_reply column (run once)
if (isset($db) && $db instanceof Database) {
    $col = $db->fetchOne("SHOW COLUMNS FROM comments LIKE 'admin_reply'");
    if (empty($col)) {
        $db->execute("ALTER TABLE comments ADD COLUMN admin_reply TEXT NULL");
    }
    // create programs table if it doesn't exist yet
    $db->execute("CREATE TABLE IF NOT EXISTS programs (
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
    global $logger;

    try {
        $upload = FileUpload::store($file, $folder);
    } catch (InvalidArgumentException $e) {
        $logger->error('Invalid image upload folder', ['exception' => $e->getMessage(), 'folder' => $folder]);
        return ['success' => false, 'message' => 'Folder upload tidak valid. Silakan hubungi administrator.'];
    } catch (Throwable $e) {
        $logger->error('Image upload failed unexpectedly', ['exception' => $e->getMessage(), 'folder' => $folder]);
        return ['success' => false, 'message' => 'Terjadi kesalahan saat mengunggah gambar. Silakan coba lagi.'];
    }

    if (!$upload['success']) {
        $logger->warning('Invalid image upload', ['error' => $upload['message'], 'folder' => $folder]);
    }

    return $upload;
}

/**
 * Upload any file (pdf, doc, etc.) to a folder under uploads
 * returns ['success'=>bool, 'path'=>folder/filename, 'message'=>string]
 */
function uploadFile($file, $folder = 'spmb') {
    global $logger;

    try {
        $upload = FileUpload::store($file, $folder);
    } catch (InvalidArgumentException $e) {
        $logger->error('Invalid file upload folder', ['exception' => $e->getMessage(), 'folder' => $folder]);
        return ['success' => false, 'message' => 'Folder upload tidak valid. Silakan hubungi administrator.'];
    } catch (Throwable $e) {
        $logger->error('File upload failed unexpectedly', ['exception' => $e->getMessage(), 'folder' => $folder]);
        return ['success' => false, 'message' => 'Terjadi kesalahan saat mengunggah file. Silakan coba lagi.'];
    }

    if (!$upload['success']) {
        $logger->warning('Invalid file upload', ['error' => $upload['message'], 'folder' => $folder]);
    }

    return $upload;
}

/* database helper functions */

function getArticles($limit = null, $category_id = null, $status = 'published') {
    global $db;

    $sql = "SELECT a.*, c.name as category_name, u.username as author_name
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.status = ?";

    $params = [$status];

    if ($category_id) {
        $sql .= " AND a.category_id = ?";
        $params[] = (int)$category_id;
    }

    $sql .= " ORDER BY a.published_at DESC";

    if ($limit) {
        $limit = (int)$limit;
        $sql .= " LIMIT $limit";
    }

    return $db->fetchAll($sql, $params);
}

function getArticleBySlug($slug) {
    global $db;

    return $db->fetchOne(
        "SELECT a.*, c.name as category_name, u.username as author_name
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.slug = ? AND a.status = 'published' LIMIT 1",
        [$slug]
    );
}

function updateArticleViews($article_id) {
    global $db;
    $db->execute("UPDATE articles SET views = views + 1 WHERE id = ?", [(int)$article_id]);
}

/**
 * Add a new article record. Accepts array of fields and optional file upload data.
 * Returns boolean success.
 */
function addArticle($data, $file = null) {
    global $db;

    $title       = Validator::sanitizeString($data['title'] ?? '');
    $category_id = (int)($data['category_id'] ?? 0);
    $content     = Validator::sanitizeRichText($data['content'] ?? '');
    $excerpt     = Validator::sanitizeString($data['excerpt'] ?? '');
    $status      = Validator::sanitizeString($data['status'] ?? 'draft');
    $slug        = createSlug($title);
    $author_id   = isset($data['author_id']) ? (int)$data['author_id'] : null;

    $featured_image = '';
    if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($file, 'articles');
        if ($upload['success']) {
            $featured_image = $upload['path'];
        }
    }

    $published_at = $status === 'published' ? date('Y-m-d H:i:s') : null;

    $sql = "INSERT INTO articles (title, slug, category_id, author_id, content, excerpt, status, featured_image, published_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";

    return $db->execute($sql, [
        $title,
        $slug,
        $category_id,
        $author_id,
        $content,
        $excerpt,
        $status,
        $featured_image,
        $published_at,
    ]);
}

/**
 * Update an existing article by id. Data array may contain same keys as addArticle.
 */
function updateArticle($id, $data, $file = null) {
    global $db;
    $id = (int)$id;

    $title       = Validator::sanitizeString($data['title'] ?? '');
    $category_id = (int)($data['category_id'] ?? 0);
    $content     = Validator::sanitizeRichText($data['content'] ?? '');
    $excerpt     = Validator::sanitizeString($data['excerpt'] ?? '');
    $status      = Validator::sanitizeString($data['status'] ?? 'draft');
    $slug        = createSlug($title);

    $featured_image = '';
    if ($file && isset($file['error']) && $file['error'] === UPLOAD_ERR_OK) {
        $upload = uploadImage($file, 'articles');
        if ($upload['success']) {
            $featured_image = $upload['path'];
        }
    }

    $check = $db->fetchOne('SELECT published_at FROM articles WHERE id = ?', [$id]);
    $published_at = $status === 'published' && empty($check['published_at']) ? date('Y-m-d H:i:s') : $check['published_at'];

    $sql = "UPDATE articles SET title = ?, slug = ?, category_id = ?, content = ?, excerpt = ?, status = ?, featured_image = ?, published_at = ? WHERE id = ?";
    return $db->execute($sql, [$title, $slug, $category_id, $content, $excerpt, $status, $featured_image, $published_at, $id]);
}

function getComments($article_id, $status = 'approved') {
    global $db;
    return $db->fetchAll(
        'SELECT * FROM comments WHERE article_id = ? AND status = ? ORDER BY created_at DESC',
        [(int)$article_id, $status]
    );
}

function addComment($article_id, $author_name, $author_email, $content) {
    global $db;
    return $db->execute(
        'INSERT INTO comments (article_id, author_name, author_email, content) VALUES (?, ?, ?, ?)',
        [(int)$article_id, Validator::sanitizeString($author_name), Validator::sanitizeString($author_email), Validator::sanitizeString($content)]
    );
}

function getGallery($category = null) {
    global $db;

    if ($category) {
        return $db->fetchAll(
            'SELECT * FROM gallery WHERE category = ? ORDER BY created_at DESC',
            [Validator::sanitizeString($category)]
        );
    }

    return $db->fetchAll('SELECT * FROM gallery ORDER BY created_at DESC');
}

function getAchievements() {
    global $db;
    return $db->fetchAll('SELECT * FROM achievements ORDER BY year DESC, id DESC');
}

function getExtracurriculars($type = null) {
    global $db;

    if ($type) {
        return $db->fetchAll('SELECT * FROM extracurriculars WHERE type = ? ORDER BY name ASC', [Validator::sanitizeString($type)]);
    }

    return $db->fetchAll('SELECT * FROM extracurriculars ORDER BY name ASC');
}

/* PROGRAM UNGGULAN helpers */
function getPrograms() {
    global $db;
    return $db->fetchAll('SELECT * FROM programs ORDER BY id ASC');
}

function addProgram($title, $description) {
    global $db;
    $t = Validator::sanitizeString($title);
    $d = Validator::sanitizeString($description);
    return $db->execute(
        'INSERT INTO programs (title, description) VALUES (?, ?)',
        [$t, $d]
    );
}

function updateProgram($id, $title, $description) {
    global $db;
    $id = (int)$id;
    $t = Validator::sanitizeString($title);
    $d = Validator::sanitizeString($description);
    return $db->execute(
        'UPDATE programs SET title = ?, description = ? WHERE id = ?',
        [$t, $d, $id]
    );
}

function deleteProgram($id) {
    global $db;
    return $db->execute('DELETE FROM programs WHERE id = ?', [(int)$id]);
}

function searchArticles($keyword, $limit = 10) {
    global $db;
    $keyword = '%' . trim($keyword) . '%';
    $limit = max(1, min((int)$limit, 50));

    return $db->fetchAll(
        "SELECT a.*, c.name as category_name, u.username as author_name
            FROM articles a
            LEFT JOIN categories c ON a.category_id = c.id
            LEFT JOIN users u ON a.author_id = u.id
            WHERE a.status = 'published'
              AND (a.title LIKE ? OR a.content LIKE ? OR a.excerpt LIKE ? OR c.name LIKE ?)
            ORDER BY a.published_at DESC
            LIMIT $limit",
        [$keyword, $keyword, $keyword, $keyword]
    );
}

/* authentication helpers */

function isLoggedIn() {
    return isset($_SESSION['user_id']) && isset($_SESSION['username']);
}

function isAdmin() {
    return isLoggedIn() && (!empty($_SESSION['role']) && $_SESSION['role'] === 'admin');
}

function redirect($url) {
    header("Location: " . APP_URL . '/' . ltrim($url, '/'));
    exit;
}

// end of functions file
