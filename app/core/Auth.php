<?php
class Auth {
    public static function initSession(): void {
        if (session_status() === PHP_SESSION_NONE) {
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            session_set_cookie_params([
                'lifetime' => SESSION_TIMEOUT,
                'path' => '/',
                'domain' => '',
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Lax'
            ]);
            session_start();
        }

        // Session timeout
        if (isset($_SESSION['last_activity']) && time() - $_SESSION['last_activity'] > SESSION_TIMEOUT) {
            self::logout();
        }
        $_SESSION['last_activity'] = time();

        // Regenerate session id periodically
        if (!isset($_SESSION['created_at'])) {
            $_SESSION['created_at'] = time();
        } elseif (time() - $_SESSION['created_at'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['created_at'] = time();
        }
    }

    public static function login(array $user): void {
        self::initSession();
        session_regenerate_id(true);
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['logged_in'] = true;
        $_SESSION['login_time'] = time();

        // Update last login
        try {
            $db = Database::getInstance();
            $db->query("UPDATE users SET last_login_at = NOW() WHERE id = ?", [$user['id']]);
            // Log session
            $db->query(
                "INSERT INTO user_sessions (user_id, session_id, ip_address, user_agent, created_at, expires_at) VALUES (?, ?, ?, ?, NOW(), DATE_ADD(NOW(), INTERVAL 2 HOUR))",
                [$user['id'], session_id(), Security::getClientIp(), Security::getUserAgent()]
            );
        } catch (Exception $e) {
            error_log("Auth login DB error: " . $e->getMessage());
        }
    }

    public static function logout(): void {
        self::initSession();
        try {
            if (isset($_SESSION['user_id'])) {
                $db = Database::getInstance();
                $db->query("DELETE FROM user_sessions WHERE session_id = ?", [session_id()]);
            }
        } catch (Exception $e) {
            // ignore
        }
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
    }

    public static function check(): bool {
        self::initSession();
        return isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true && isset($_SESSION['user_id']);
    }

    public static function user(): ?array {
        if (!self::check()) return null;
        try {
            $db = Database::getInstance();
            return $db->fetchOne("SELECT * FROM users WHERE id = ?", [$_SESSION['user_id']]);
        } catch (Exception $e) {
            return null;
        }
    }

    public static function id(): ?int {
        self::initSession();
        return $_SESSION['user_id'] ?? null;
    }

    public static function isAdmin(): bool {
        self::initSession();
        return ($_SESSION['user_role'] ?? '') === ROLE_ADMIN;
    }

    public static function requireLogin(): void {
        if (!self::check()) {
            if (self::isApiRequest()) {
                http_response_code(401);
                die(json_encode(['error' => 'Authentication required', 'redirect' => '/login']));
            } else {
                header('Location: /login?redirect=' . urlencode($_SERVER['REQUEST_URI'] ?? '/'));
                exit;
            }
        }
    }

    public static function requireAdmin(): void {
        self::requireLogin();
        if (!self::isAdmin()) {
            http_response_code(403);
            die('Forbidden: Admin access required');
        }
    }

    public static function isApiRequest(): bool {
        return strpos($_SERVER['REQUEST_URI'] ?? '', '/api/') === 0 || 
               (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}
