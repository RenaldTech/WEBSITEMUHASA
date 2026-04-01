<?php
declare(strict_types=1);

class CSRFToken
{
    private const SESSION_KEY = '_csrf_token';

    public static function generate(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::SESSION_KEY];
    }

    public static function regenerate(): string
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        $_SESSION[self::SESSION_KEY] = bin2hex(random_bytes(32));
        return $_SESSION[self::SESSION_KEY];
    }

    public static function validate(?string $token): bool
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            session_start();
        }

        if (empty($_SESSION[self::SESSION_KEY]) || empty($token)) {
            return false;
        }

        $valid = hash_equals($_SESSION[self::SESSION_KEY], $token);

        if ($valid) {
            self::regenerate();
        }

        return $valid;
    }

    public static function inputField(): string
    {
        return '<input type="hidden" name="_csrf_token" value="' . htmlspecialchars(self::generate(), ENT_QUOTES, 'UTF-8') . '">';
    }
}
