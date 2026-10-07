<?php
class CategoryService {
    public static function getAll(): array {
        try {
            $db = Database::getInstance();
            if ($db->isFallback() && $db->getPdo() === null) {
                return self::getAllFromFallback();
            }
            return $db->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM tools WHERE category_id = c.id AND status = ?) as tool_count FROM categories c WHERE c.status = ? ORDER BY c.sort_order ASC, c.name ASC", [TOOL_STATUS_ACTIVE, TOOL_STATUS_ACTIVE]);
        } catch (Exception $e) {
            return self::getAllFromFallback();
        }
    }

    public static function getAllWithChildren(): array {
        try {
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
        } catch (Exception $e) {
            return self::getAllFromFallback();
        }
    }

    public static function getBySlug(string $slug): ?array {
        try {
            $db = Database::getInstance();
            $cat = $db->fetchOne("SELECT * FROM categories WHERE slug = ? AND status = ?", [$slug, TOOL_STATUS_ACTIVE]);
            if ($cat) return $cat;
            // Fallback
            foreach (self::getAllFromFallback() as $c) {
                if ($c['slug'] === $slug) return $c;
            }
            return null;
        } catch (Exception $e) {
            foreach (self::getAllFromFallback() as $c) {
                if ($c['slug'] === $slug) return $c;
            }
            return null;
        }
    }

    public static function getById(int $id): ?array {
        try {
            $db = Database::getInstance();
            return $db->fetchOne("SELECT * FROM categories WHERE id = ?", [$id]) ?: null;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function getForAdmin(): array {
        try {
            $db = Database::getInstance();
            return $db->fetchAll("SELECT c.*, (SELECT COUNT(*) FROM tools WHERE category_id = c.id) as tool_count FROM categories c ORDER BY c.sort_order ASC");
        } catch (Exception $e) {
            return self::getAllFromFallback();
        }
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO categories (name, slug, description, parent_id, icon, image, meta_title, meta_description, status, sort_order, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))",
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
        $db->query("UPDATE categories SET " . implode(', ', $fields) . ", updated_at = datetime('now') WHERE id = ?", $params);
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

    private static function getAllFromFallback(): array {
        return [
            ['id'=>1, 'name'=>'AI Tools', 'slug'=>'ai-tools', 'description'=>'AI-powered tools', 'icon'=>'🤖', 'tool_count'=>12],
            ['id'=>2, 'name'=>'PDF Tools', 'slug'=>'pdf-tools', 'description'=>'PDF tools', 'icon'=>'📄', 'tool_count'=>13],
            ['id'=>3, 'name'=>'Image Tools', 'slug'=>'image-tools', 'description'=>'Image tools', 'icon'=>'🖼️', 'tool_count'=>11],
            ['id'=>4, 'name'=>'Audio & Video Tools', 'slug'=>'audio-video-tools', 'description'=>'Audio & Video', 'icon'=>'🎬', 'tool_count'=>8],
            ['id'=>5, 'name'=>'Calculators', 'slug'=>'calculators', 'description'=>'Calculators', 'icon'=>'🧮', 'tool_count'=>20],
            ['id'=>6, 'name'=>'Financial Calculators', 'slug'=>'financial-calculators', 'description'=>'Financial', 'icon'=>'💰', 'tool_count'=>39],
            ['id'=>7, 'name'=>'Health & Fitness', 'slug'=>'health-fitness', 'description'=>'Health', 'icon'=>'🏋️', 'tool_count'=>25],
            ['id'=>8, 'name'=>'Educational Tools', 'slug'=>'educational-tools', 'description'=>'Educational', 'icon'=>'📚', 'tool_count'=>7],
            ['id'=>9, 'name'=>'Sports', 'slug'=>'sports', 'description'=>'Sports', 'icon'=>'⚾', 'tool_count'=>25],
            ['id'=>10, 'name'=>'Webmaster Tools', 'slug'=>'webmaster-tools', 'description'=>'Webmaster', 'icon'=>'🌐', 'tool_count'=>7],
            ['id'=>11, 'name'=>'Text & Document Tools', 'slug'=>'text-document-tools', 'description'=>'Text', 'icon'=>'📝', 'tool_count'=>2],
            ['id'=>12, 'name'=>'Utility Tools', 'slug'=>'utility-tools', 'description'=>'Utility', 'icon'=>'🛠️', 'tool_count'=>8],
            ['id'=>13, 'name'=>'Compression Tools', 'slug'=>'compression-tools', 'description'=>'Compression', 'icon'=>'🗜️', 'tool_count'=>2],
            ['id'=>14, 'name'=>'Fun & Games', 'slug'=>'fun-games', 'description'=>'Fun', 'icon'=>'🎮', 'tool_count'=>2],
        ];
    }
}
