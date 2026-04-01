<?php
declare(strict_types=1);

class FileUpload
{
    public static function validate(array $file): array
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return ['success' => false, 'message' => 'File upload gagal'];
        }

        if (!is_uploaded_file($file['tmp_name'])) {
            return ['success' => false, 'message' => 'Sumber file tidak valid'];
        }

        if ($file['size'] > MAX_UPLOAD_SIZE) {
            return ['success' => false, 'message' => 'Ukuran file melebihi 2MB'];
        }

        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mimeType = $finfo->file($file['tmp_name']);

        if (!in_array($mimeType, ALLOWED_MIME_TYPES, true)) {
            return ['success' => false, 'message' => 'Tipe file tidak diizinkan'];
        }

        return ['success' => true, 'mime' => $mimeType];
    }

    public static function store(array $file, string $folder = 'uploads'): array
    {
        if (!in_array($folder, ALLOWED_UPLOAD_FOLDERS, true)) {
            throw new InvalidArgumentException('The upload folder is not allowed.');
        }

        $validation = self::validate($file);
        if (!$validation['success']) {
            return $validation;
        }

        $basePath = rtrim(UPLOAD_BASE_PATH, '/\\') . '/' . $folder;
        if (!is_dir($basePath) && !mkdir($basePath, 0755, true) && !is_dir($basePath)) {
            return ['success' => false, 'message' => 'Tidak dapat membuat direktori upload'];
        }

        $extension = self::getExtension($validation['mime']);
        $filename = bin2hex(random_bytes(18)) . '.' . $extension;
        $destination = $basePath . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            return ['success' => false, 'message' => 'Gagal menyimpan file'];
        }

        return ['success' => true, 'path' => $folder . '/' . $filename, 'mime' => $validation['mime']];
    }

    private static function getExtension(string $mimeType): string
    {
        $map = [
            'image/jpeg' => 'jpg',
            'image/png' => 'png',
            'application/pdf' => 'pdf',
        ];

        return $map[$mimeType] ?? 'bin';
    }
}
