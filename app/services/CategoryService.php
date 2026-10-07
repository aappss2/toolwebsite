<?php
class CategoryService {
    public static function getAll(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM tools WHERE category_id = c.id AND status = ?) as tool_count FROM categories c WHERE c.status = ? ORDER BY c.sort_order ASC, c.name ASC", [TOOL_STATUS_ACTIVE, TOOL_STATUS_ACTIVE]);
    }

    public static function getAllWithChildren(): array {
        $db = Database::getInstance();
        $cats = $db->fetchAll("SELECT * FROM categories WHERE status = ? ORDER BY sort_order ASC", [TOOL_STATUS_ACTIVE]);
        $tree = [];
        $byId = [];
        foreach ($cats as $cat) {
            $cat['children'] = [];
            $byId[$cat['id']] = $cat;
        }
        foreach ($byId as $id => $cat) {
            if ($cat['parent_id'] && isset($byId[$cat['parent_id']])) {
                $byId[$cat['parent_id']]['children'][] = &$byId[$id];
            } else {
                $tree[] = &$byId[$id];
            }
        }
        return $tree;
    }

    public static function getBySlug(string $slug): ?array {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM categories WHERE slug = ? AND status = ?", [$slug, TOOL_STATUS_ACTIVE]) ?: null;
    }

    public static function getById(int $id): ?array {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]) ?: null;
    }

    public static function getForAdmin(): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM tools WHERE category_id = c.id) as tool_count FROM categories c ORDER BY c.sort_order ASC");
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO categories (name, slug, description, parent_id, icon, image, meta_title, meta_description, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? '',
                $data['parent_id'] ?? null,
                $data['icon'] ?? '',
                $data['image'] ?? '',
                $data['meta_title'] ?? $data['name'],
                $data['meta_description'] ?? '',
                $data['status'] ?? TOOL_STATUS_ACTIVE,
                $data['sort_order'] ?? 0
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
        $db->query("UPDATE categories SET " . implode(', ', $fields) . ", updated_at = NOW() WHERE id = ?", $params);
    }

    public static function delete(int $id): void {
        $db = Database::getInstance();
        $db->query("DELETE FROM categories WHERE id = ?", [$id]);
    }

    public static function normalizeRawCategory(string $raw): string {
        $map = [
            '🤖 AI Tools' => 'AI Tools',
            '📚 Educational Tools' => 'Educational Tools',
            'CALCULATOR' => 'Calculators',
            'UTILITY TOOLS' => 'Utility Tools',
            'IMAGE TOOLS' => 'Image Tools',
            'PDF TOOLS' => 'PDF Tools',
            'Audio & Video Tools' => 'Audio & Video Tools',
            'FINANCIAL CALCULATORS' => 'Financial Calculators',
            'HEALTH AND FITNESS' => 'Health & Fitness',
            'SPORTS-BASEBALL' => 'Sports',
            'SPORTS-BASKETBALL' => 'Sports',
            'SPORTS-CRICKET' => 'Sports',
            'WEBMASTER' => 'Webmaster Tools',
            'DOCUMENTS' => 'Text & Document Tools',
            'FUN' => 'Fun & Games',
            'HIGH COMPRESSOR' => 'Compression Tools',
            'CRYPTO TAX CALCULATOR' => 'Financial Calculators',
            'EDUCATIONAL CONTENT' => 'Educational Tools',
        ];
        return $map[$raw] ?? ucwords(strtolower($raw));
    }
}
