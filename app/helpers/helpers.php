<?php
function e(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function config(string $key, $default = null) {
    return defined($key) ? constant($key) : ($default ?? $_ENV[$key] ?? null);
}

function asset(string $path): string {
    return APP_DOMAIN . '/assets/' . ltrim($path, '/');
}

function tool_url(string $slug): string {
    return APP_DOMAIN . '/tools/' . $slug . '/';
}

function category_url(string $slug): string {
    return APP_DOMAIN . '/category/' . $slug . '/';
}

function old_url_to_new(string $oldLink): string {
    // Convert old jdiro link to new slug
    $slug = slugify(basename($oldLink, '.html'));
    return tool_url($slug);
}

function is_active_subscription(?array $user): bool {
    if (!$user) return false;
    if (($user['subscription_status'] ?? '') === SUB_STATUS_ACTIVE) {
        $expires = $user['subscription_expires_at'] ?? null;
        if ($expires && strtotime($expires) > time()) {
            return true;
        }
    }
    return false;
}

function get_site_setting(string $key, $default = null) {
    try {
        $db = Database::getInstance();
        $row = $db->fetchOne("SELECT setting_value FROM site_settings WHERE setting_key = ?", [$key]);
        return $row ? $row['setting_value'] : $default;
    } catch (Exception $e) {
        return $default;
    }
}

function set_site_setting(string $key, string $value): void {
    try {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO site_settings (setting_key, setting_value, updated_at) VALUES (?, ?, NOW()) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value), updated_at = NOW()",
            [$key, $value]
        );
    } catch (Exception $e) {
        error_log("set_site_setting error: " . $e->getMessage());
    }
}

function log_admin_action(string $action, string $details = ''): void {
    try {
        $db = Database::getInstance();
        $userId = Auth::id();
        $db->query(
            "INSERT INTO admin_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())",
            [$userId, $action, $details, Security::getClientIp()]
        );
    } catch (Exception $e) {
        error_log("log_admin_action error: " . $e->getMessage());
    }
}
