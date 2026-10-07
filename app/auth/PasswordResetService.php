<?php
class PasswordResetService {
    public static function createToken(string $email): string {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if (!$user) {
            // Don't reveal if email exists
            return '';
        }

        $token = Security::generateRandomToken(64);
        $hashedToken = hash('sha256', $token);
        $expires = date('Y-m-d H:i:s', time() + PASSWORD_RESET_EXPIRY);

        $db->query("DELETE FROM password_resets WHERE email = ?", [$email]);
        $db->query(
            "INSERT INTO password_resets (email, token, created_at, expires_at) VALUES (?, ?, NOW(), ?)",
            [$email, $hashedToken, $expires]
        );

        // In production, send email here
        // For now, log and return token for testing
        error_log("Password reset token for $email: $token");
        return $token;
    }

    public static function validateToken(string $token, string $email): bool {
        $db = Database::getInstance();
        $hashed = hash('sha256', $token);
        $row = $db->fetchOne(
            "SELECT * FROM password_resets WHERE email = ? AND token = ? AND expires_at > NOW() AND used_at IS NULL",
            [$email, $hashed]
        );
        return $row !== false && $row !== null;
    }

    public static function resetPassword(string $email, string $token, string $newPassword): bool {
        if (!self::validateToken($token, $email)) {
            throw new Exception("Invalid or expired token");
        }

        $pwErrors = Validator::validatePassword($newPassword);
        if (!empty($pwErrors)) {
            throw new Exception(implode(', ', $pwErrors));
        }

        $db = Database::getInstance();
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $db->query("UPDATE users SET password_hash = ?, updated_at = NOW() WHERE email = ?", [$hash, $email]);
        
        $hashed = hash('sha256', $token);
        $db->query("UPDATE password_resets SET used_at = NOW() WHERE email = ? AND token = ?", [$email, $hashed]);

        return true;
    }
}
