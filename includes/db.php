<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/../src/classes/Database.php';
require_once __DIR__ . '/../src/classes/Logger.php';

try {
    $db = new Database(DB_DSN, DB_USER, DB_PASS);
} catch (Throwable $e) {
    $logger = new Logger(LOG_BASE_PATH);
    $logger->error('Database connection failed', ['message' => $e->getMessage()]);
    if (APP_ENV === 'development') {
        die('Database connection failed: ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8'));
    }
    http_response_code(500);
    header('Location: /500.php');
    exit;
}
