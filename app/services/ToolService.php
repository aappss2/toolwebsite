<?php
class ToolService {
    public static function getAllActive(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT t.*, c.name as category_name, c.slug as category_slug FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.status = ? ORDER BY t.featured DESC, t.name ASC", [TOOL_STATUS_ACTIVE]);
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();
        $tool = $db->fetchOne("SELECT t.*, c.name as category_name, c.slug as category_slug FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.slug = ? AND t.status = ?", [$slug, TOOL_STATUS_ACTIVE]);
        return $tool ?: null;
    }

    public static function getByCategory(int $categoryId): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM tools WHERE category_id = ? AND status = ? ORDER BY featured DESC, name ASC", [$categoryId, TOOL_STATUS_ACTIVE]);
    }

    public static function getPopular(int $limit = 12): array {
        $db = Database::getInstance();
        // Popular by usage count
        return $db->fetchAll(
            "SELECT t.*, c.name as category_name, COUNT(tu.id) as usage_count 
             FROM tools t 
             LEFT JOIN categories c ON t.category_id = c.id 
             LEFT JOIN tool_usage tu ON t.id = tu.tool_id 
             WHERE t.status = ? 
             GROUP BY t.id 
             ORDER BY usage_count DESC, t.featured DESC 
             LIMIT ?",
            [TOOL_STATUS_ACTIVE, $limit]
        );
    }

    public static function getFeatured(int $limit = 8): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT t.*, c.name as category_name FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.status = ? AND t.featured = 1 ORDER BY t.updated_at DESC LIMIT ?", [TOOL_STATUS_ACTIVE, $limit]);
    }

    public static function getRelated(int $toolId, int $categoryId, int $limit = 6): array {
        $db = Database::getInstance();
        return $db->fetchAll(
            "SELECT * FROM tools WHERE category_id = ? AND id != ? AND status = ? ORDER BY RAND() LIMIT ?",
            [$categoryId, $toolId, TOOL_STATUS_ACTIVE, $limit]
        );
    }

    public static function search(string $query, int $limit = 20): array {
        $db = Database::getInstance();
        $like = '%' . $query . '%';
        return $db->fetchAll(
            "SELECT t.*, c.name as category_name FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.status = ? AND (t.name LIKE ? OR t.slug LIKE ? OR t.description LIKE ? OR c.name LIKE ?) ORDER BY t.featured DESC, t.name ASC LIMIT ?",
            [TOOL_STATUS_ACTIVE, $like, $like, $like, $like, $limit]
        );
    }

    public static function incrementView(int $toolId): void {
        try {
            $db = Database::getInstance();
            $db->query("UPDATE tools SET view_count = view_count + 1 WHERE id = ?", [$toolId]);
        } catch (Exception $e) {
            // ignore
        }
    }

    public static function getAllForAdmin(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT t.*, c.name as category_name FROM tools t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.updated_at DESC");
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO tools (name, slug, description, category_id, source_path, status, featured, requires_login, is_metered, free_allowed, requires_subscription, icon, image, meta_title, meta_description, canonical_url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? '',
                $data['category_id'] ?? null,
                $data['source_path'] ?? '',
                $data['status'] ?? TOOL_STATUS_ACTIVE,
                $data['featured'] ?? 0,
                $data['requires_login'] ?? 0,
                $data['is_metered'] ?? 1,
                $data['free_allowed'] ?? 1,
                $data['requires_subscription'] ?? 0,
                $data['icon'] ?? '',
                $data['image'] ?? '',
                $data['meta_title'] ?? $data['name'],
                $data['meta_description'] ?? '',
                $data['canonical_url'] ?? (APP_DOMAIN . '/tools/' . $data['slug'] . '/')
            ]
        );
        return (int)$db->lastInsertId();
    }

    public static function update(int $id, array $data): void {
        $db = Database::getInstance();
        $fields = [];
        $params = [];
        foreach ($data as $k => $v) {
            $fields[] = "$k = ?";
            $params[] = $v;
        }
        $params[] = $id;
        $db->query("UPDATE tools SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?", $params);
    }

    public static function delete(int $id): void {
        $db = Database::getInstance();
        $db->query("DELETE FROM tools WHERE id = ?", [$id]);
    }
}
