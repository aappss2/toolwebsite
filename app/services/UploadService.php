<?php
class UploadService {
    public static function validateFile(array $file): array {
        $errors = [];

        if ($file['error'] !== UPLOAD_ERR_OK) {
            $errors[] = "Upload error: " . $file['error'];
            return $errors;
        }

        if ($file['size'] > UPLOAD_MAX_SIZE) {
            $errors[] = "File too large. Max " . (UPLOAD_MAX_SIZE / 1024 / 1024) . "MB";
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, UPLOAD_ALLOWED_EXT)) {
            $errors[] = "File type .$ext not allowed";
        }

        // MIME check
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime = finfo_file($finfo, $file['tmp_name']);
        finfo_close($finfo);

        if (!in_array($mime, UPLOAD_ALLOWED_MIME)) {
            // Allow some flexibility - check if mime starts with image/, audio/, video/
            $allowedPrefixes = ['image/', 'audio/', 'video/', 'application/pdf', 'text/'];
            $allowed = false;
            foreach ($allowedPrefixes as $prefix) {
                if (strpos($mime, $prefix) === 0) {
                    $allowed = true;
                    break;
                }
            }
            if (!$allowed) {
                $errors[] = "MIME type $mime not allowed";
            }
        }

        // Check for executable content
        $content = file_get_contents($file['tmp_name'], false, null, 0, 1024);
        if (strpos($content, '<?php') !== false || strpos($content, '<?=') !== false) {
            $errors[] = "File contains executable code";
        }

        // Check file signature for images
        if (in_array($ext, ['jpg','jpeg','png','gif','webp'])) {
            $imageInfo = @getimagesize($file['tmp_name']);
            if ($imageInfo === false) {
                $errors[] = "Invalid image file";
            }
        }

        return $errors;
    }

    public static function storeFile(array $file, string $subDir = 'general'): string {
        $errors = self::validateFile($file);
        if (!empty($errors)) {
            throw new Exception(implode(', ', $errors));
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $randomName = bin2hex(random_bytes(16)) . '.' . $ext;
        
        $uploadDir = STORAGE_PATH . '/uploads_tmp/' . $subDir;
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $dest = $uploadDir . '/' . $randomName;
        if (!move_uploaded_file($file['tmp_name'], $dest)) {
            throw new Exception("Failed to store file");
        }

        return $dest;
    }

    public static function cleanOldFiles(int $olderThanSeconds = 3600): int {
        $dir = STORAGE_PATH . '/uploads_tmp';
        if (!file_exists($dir)) return 0;
        
        $count = 0;
        $now = time();
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS));
        foreach ($iterator as $file) {
            if ($file->isFile() && $now - $file->getMTime() > $olderThanSeconds) {
                unlink($file->getPathname());
                $count++;
            }
        }
        return $count;
    }

    public static function getSecurePublicUrl(string $storedPath): string {
        // For files that need to be served, copy to public/uploads with randomized name and deny php execution via .htaccess
        $publicDir = PUBLIC_PATH . '/uploads';
        if (!file_exists($publicDir)) {
            mkdir($publicDir, 0755, true);
        }
        $ext = pathinfo($storedPath, PATHINFO_EXTENSION);
        $publicName = bin2hex(random_bytes(8)) . '.' . $ext;
        $publicPath = $publicDir . '/' . $publicName;
        copy($storedPath, $publicPath);
        return '/uploads/' . $publicName;
    }
}
