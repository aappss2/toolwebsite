<?php
class Validator {
    public static function email(string $email): bool {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function required($value): bool {
        return isset($value) && trim((string)$value) !== '';
    }

    public static function minLength(string $value, int $min): bool {
        return mb_strlen($value) >= $min;
    }

    public static function maxLength(string $value, int $max): bool {
        return mb_strlen($value) <= $max;
    }

    public static function slug(string $slug): bool {
        return preg_match('/^[a-z0-9-]+$/', $slug) === 1;
    }

    public static function url(string $url): bool {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }

    public static function sanitizeString(string $str): string {
        return htmlspecialchars(trim($str), ENT_QUOTES, 'UTF-8');
    }

    public static function sanitizeArray(array $data): array {
        $clean = [];
        foreach ($data as $k => $v) {
            if (is_array($v)) {
                $clean[$k] = self::sanitizeArray($v);
            } else {
                $clean[$k] = self::sanitizeString((string)$v);
            }
        }
        return $clean;
    }

    public static function validatePassword(string $password): array {
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number";
        }
        return $errors;
    }
}
