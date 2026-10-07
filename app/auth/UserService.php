<?php
class UserService {
    public static function register(string $name, string $email, string $password): array {
        $db = Database::getInstance();

        // Validate
        if (!Validator::email($email)) {
            throw new Exception("Invalid email");
        }
        if (!Validator::required($name) || !Validator::minLength($name, 2)) {
            throw new Exception("Name must be at least 2 characters");
        }
        $pwErrors = Validator::validatePassword($password);
        if (!empty($pwErrors)) {
            throw new Exception(implode(', ', $pwErrors));
        }

        // Check exists
        $existing = $db->fetchOne("SELECT id FROM users WHERE email = ?", [$email]);
        if ($existing) {
            throw new Exception("Email already registered");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $freeUses = (int)get_site_setting('free_uses_default', (string)FREE_USES_DEFAULT);

        $db->query(
            "INSERT INTO users (name, email, password_hash, role, status, free_uses_remaining, total_free_uses, free_uses_used, subscription_status, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [$name, $email, $hash, ROLE_USER, 'active', $freeUses, $freeUses, 0, 'free']
        );

        $userId = (int)$db->lastInsertId();
        return $db->fetchOne("SELECT * FROM users WHERE id = ?", [$userId]);
    }

    public static function login(string $email, string $password): array {
        $db = Database::getInstance();
        $user = $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]);
        if (!$user) {
            throw new Exception("Invalid credentials");
        }
        if ($user['status'] !== 'active') {
            throw new Exception("Account is suspended");
        }
        if (!password_verify($password, $user['password_hash'])) {
            throw new Exception("Invalid credentials");
        }

        // Rehash if needed
        if (password_needs_rehash($user['password_hash'], PASSWORD_DEFAULT)) {
            $newHash = password_hash($password, PASSWORD_DEFAULT);
            $db->query("UPDATE users SET password_hash = ? WHERE id = ?", [$newHash, $user['id']]);
        }

        return $user;
    }

    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM users WHERE id = ?", [$id]) ?: null;
    }

    public static function getByEmail(string $email): ?array {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM users WHERE email = ?", [$email]) ?: null;
    }

    public static function update(int $id, array $data): void {
        $db = Database::getInstance();
        $fields = [];
        $params = [];
        foreach ($data as $k => $v) {
            if ($k === 'password') {
                $fields[] = "password_hash = ?";
                $params[] = password_hash($v, PASSWORD_DEFAULT);
            } else {
                $fields[] = "$k = ?";
                $params[] = $v;
            }
        }
        if (empty($fields)) return;
        $params[] = $id;
        $db->query("UPDATE users SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?", $params);
    }

    public static function getAll(int $page = 1, int $perPage = 20, string $search = ''): array {
        $db = Database::getInstance();
        $offset = ($page - 1) * $perPage;
        if ($search) {
            $like = '%' . $search . '%';
            $users = $db->fetchAll(
                "SELECT * FROM users WHERE name LIKE ? OR email LIKE ? ORDER BY created_at DESC LIMIT ? OFFSET ?",
                [$like, $like, $perPage, $offset]
            );
            $total = $db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE name LIKE ? OR email LIKE ?", [$like, $like]);
        } else {
            $users = $db->fetchAll("SELECT * FROM users ORDER BY created_at DESC LIMIT ? OFFSET ?", [$perPage, $offset]);
            $total = $db->fetchOne("SELECT COUNT(*) as cnt FROM users");
        }
        return ['users' => $users, 'total' => $total['cnt'] ?? 0, 'page' => $page, 'perPage' => $perPage];
    }

    public static function suspend(int $id): void {
        $db = Database::getInstance();
        $db->query("UPDATE users SET status = 'suspended' WHERE id = ?", [$id]);
        log_admin_action('suspend_user', "Suspended user $id");
    }

    public static function activate(int $id): void {
        $db = Database::getInstance();
        $db->query("UPDATE users SET status = 'active' WHERE id = ?", [$id]);
        log_admin_action('activate_user', "Activated user $id");
    }
}
