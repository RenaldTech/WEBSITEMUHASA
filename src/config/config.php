<?php
declare(strict_types=1);

// Load environment file from parent directory of webroot. In production, place .env outside webroot.
$rootDir = dirname(__DIR__, 3);
$envPath = $rootDir . '/.env';

if (!is_file($envPath) || !is_readable($envPath)) {
    throw new RuntimeException(sprintf('.env file not found or not readable at %s. Place your environment file in the parent folder of the webroot.', $envPath));
}

function loadEnvFile(string $path): array
{
    if (!is_readable($path)) {
        return [];
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $data = [];

    foreach ($lines as $line) {
        $line = trim($line);

        if ($line === '' || $line[0] === '#') {
            continue;
        }

        [$key, $value] = array_map('trim', explode('=', $line, 2) + [null, null]);

        if ($key === null || $value === null) {
            continue;
        }

        $value = trim($value, " \t\n\r\0\x0B\"'");
        $data[$key] = $value;
    }

    return $data;
}

$env = loadEnvFile($envPath);

// Application configuration
define('APP_ENV', $env['APP_ENV'] ?? 'production');
define('APP_URL', rtrim($env['APP_URL'] ?? 'http://localhost/website', '/'));
define('APP_DOMAIN', $env['APP_DOMAIN'] ?? parse_url(APP_URL, PHP_URL_HOST));
define('SITE_URL', rtrim(APP_URL, '/') . '/');
define('SITE_NAME', $env['SITE_NAME'] ?? 'SMP Muhammadiyah Tahfidz Salatiga');

define('TRUSTED_PROXY_IPS', array_filter(array_map('trim', explode(',', $env['TRUSTED_PROXY_IPS'] ?? ''))));

// Database configuration
define('DB_HOST', $env['DB_HOST'] ?? 'localhost');
define('DB_NAME', $env['DB_NAME'] ?? 'smp_muhammadiyah');
define('DB_USER', $env['DB_USER'] ?? 'app_user');
define('DB_PASS', $env['DB_PASS'] ?? 'secret_password');
define('DB_CHARSET', $env['DB_CHARSET'] ?? 'utf8mb4');
define('DB_DSN', sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET));

// Paths
$documentRoot = $_SERVER['DOCUMENT_ROOT'] ?? $rootDir;
$baseDocumentRoot = dirname($documentRoot);
$defaultUploadPath = $baseDocumentRoot . '/uploads';
$defaultLogPath = $baseDocumentRoot . '/logs';

define('UPLOAD_BASE_PATH', rtrim($env['UPLOAD_PATH'] ?? $defaultUploadPath, '/\\'));
define('LOG_BASE_PATH', rtrim($env['LOG_PATH'] ?? $defaultLogPath, '/\\'));

// Session and security
define('SESSION_NAME', $env['SESSION_NAME'] ?? 'school_website_session');
define('SESSION_TIMEOUT', (int)($env['SESSION_TIMEOUT'] ?? 1800));
define('MAX_UPLOAD_SIZE', (int)($env['MAX_UPLOAD_SIZE'] ?? 2097152));
define('UPLOAD_DIR', UPLOAD_BASE_PATH . '/');
define('MAX_FILE_SIZE', MAX_UPLOAD_SIZE);
define('ALLOWED_MIME_TYPES', array_filter(array_map('trim', explode(',', $env['ALLOWED_MIME_TYPES'] ?? 'image/jpeg,image/png,application/pdf'))));
define('ALLOWED_UPLOAD_FOLDERS', ['foto_siswa', 'dokumen', 'tugas', 'avatars', 'articles', 'gallery', 'spmb']);

define('RECAPTCHA_SITE_KEY', $env['RECAPTCHA_SITE_KEY'] ?? '');
define('RECAPTCHA_SECRET_KEY', $env['RECAPTCHA_SECRET_KEY'] ?? '');

define('CSP_NONCE', bin2hex(random_bytes(16)));

define('DB_OPTIONS', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::ATTR_STRINGIFY_FETCHES => false,
]);

// Ensure log and upload directories exist
foreach ([UPLOAD_BASE_PATH, LOG_BASE_PATH] as $path) {
    if (!is_dir($path)) {
        @mkdir($path, 0755, true);
    }
}

ini_set('display_errors', APP_ENV === 'development' ? '1' : '0');
ini_set('display_startup_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', LOG_BASE_PATH . '/php_error.log');
error_reporting(E_ALL);

// Session hardening
$secureCookie = isHttps();
session_name(SESSION_NAME);
session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => APP_DOMAIN,
    'secure' => $secureCookie,
    'httponly' => true,
    'samesite' => 'Strict',
]);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > SESSION_TIMEOUT)) {
    session_unset();
    session_destroy();
    session_start();
}

$_SESSION['last_activity'] = time();

// Send HTTP security headers
function sendSecurityHeaders(): void
{
    if (headers_sent()) {
        return;
    }

    header('X-Content-Type-Options: nosniff');
    header('X-Frame-Options: DENY');
    header('X-XSS-Protection: 1; mode=block');
    header('Referrer-Policy: strict-origin-when-cross-origin');
    header("Content-Security-Policy: default-src 'self'; script-src 'self' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/ 'nonce-" . CSP_NONCE . "'; style-src 'self' 'nonce-" . CSP_NONCE . "'; img-src 'self' data:; font-src 'self'; connect-src 'self' https://www.google.com/recaptcha/;");
    header('Permissions-Policy: geolocation=(), microphone=()');
    if (isHttps()) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
}

sendSecurityHeaders();

function isHttps(): bool
{
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        return true;
    }

    $remoteAddr = $_SERVER['REMOTE_ADDR'] ?? '';
    if (!in_array($remoteAddr, TRUSTED_PROXY_IPS, true)) {
        return false;
    }

    $forwardedProto = $_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '';
    if ($forwardedProto !== '') {
        $protocol = strtolower(trim(explode(',', $forwardedProto)[0]));
        return $protocol === 'https';
    }

    return false;
}

// Redirect to HTTPS when APP_URL uses https://
if (strpos(APP_URL, 'https://') === 0 && !isHttps()) {
    $httpsUrl = 'https://' . ($_SERVER['HTTP_HOST'] ?? '') . ($_SERVER['REQUEST_URI'] ?? '/');
    header('Location: ' . $httpsUrl, true, 301);
    exit;
}
