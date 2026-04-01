<?php
declare(strict_types=1);

class Validator
{
    public static function sanitizeString(string $value): string
    {
        $value = strip_tags($value);
        $value = preg_replace('/[\x00-\x1F\x7F]/u', '', $value);
        return trim($value);
    }

    public static function sanitizeRichText(string $input): string
    {
        $allowedTags = '<p><br><b><i><strong><em><ul><ol><li><a><img><h2><h3><blockquote>';
        $clean = strip_tags($input, $allowedTags);

        $clean = preg_replace_callback('~<a\s+([^>]+)>~i', function ($matches) {
            $href = '';
            if (preg_match('~href\s*=\s*([\"\'])(.*?)\1~i', $matches[1], $hrefMatch)) {
                $value = trim($hrefMatch[2]);
                $value = filter_var($value, FILTER_SANITIZE_URL);
                if (preg_match('~^(https?:|/|#)~i', $value)) {
                    $href = ' href="' . htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
                }
            }
            return '<a' . $href . '>';
        }, $clean);

        $clean = preg_replace_callback('~<img\s+([^>]*?)\s*(?:/?)>~i', function ($matches) {
            $src = '';
            $alt = '';
            if (preg_match('~src\s*=\s*([\"\'])(.*?)\1~i', $matches[1], $srcMatch)) {
                $value = trim($srcMatch[2]);
                $value = filter_var($value, FILTER_SANITIZE_URL);
                if (preg_match('~^(https?:|/|data:image/)~i', $value)) {
                    $src = ' src="' . htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
                }
            }
            if (preg_match('~alt\s*=\s*([\"\'])(.*?)\1~i', $matches[1], $altMatch)) {
                $value = trim($altMatch[2]);
                $alt = ' alt="' . htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') . '"';
            }
            return '<img' . $src . $alt . '>';
        }, $clean);

        $clean = preg_replace('~on[a-z]+\s*=\s*([\"\']).*?\1~i', '', $clean);
        $clean = preg_replace('~javascript:~i', '', $clean);

        return trim($clean);
    }

    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function validatePhone(string $phone): bool
    {
        return (bool)preg_match('/^\+?[0-9]{7,15}$/', trim($phone));
    }

    public static function validateName(string $name): bool
    {
        return (bool)preg_match('/^[\p{L}\s\'\-]+$/u', trim($name));
    }

    public static function validateDate(string $date, string $format = 'Y-m-d'): bool
    {
        $dt = DateTime::createFromFormat($format, $date);
        return $dt !== false && $dt->format($format) === $date;
    }

    public static function validateNumeric($value): bool
    {
        return is_numeric($value);
    }

    public static function escapeHtml(string $value): string
    {
        return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }

    public static function stripTags(string $value): string
    {
        return strip_tags($value);
    }
}
