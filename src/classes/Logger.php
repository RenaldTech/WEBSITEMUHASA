<?php
declare(strict_types=1);

class Logger
{
    private string $logFile;

    public function __construct(string $basePath)
    {
        if (!is_dir($basePath)) {
            @mkdir($basePath, 0755, true);
        }

        $this->logFile = rtrim($basePath, '/\\') . '/security.log';
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $entry = sprintf(
            "[%s] %s: %s %s%s",
            date('Y-m-d H:i:s'),
            strtoupper($level),
            $message,
            $context ? json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) : '',
            PHP_EOL
        );

        @file_put_contents($this->logFile, $entry, FILE_APPEND | LOCK_EX);
    }

    public function info(string $message, array $context = []): void
    {
        $this->log('info', $message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->log('warning', $message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->log('error', $message, $context);
    }
}
