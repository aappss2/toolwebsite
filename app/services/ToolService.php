<?php
class ToolService {
    public static function getAllActive(): array {
        try {
            $db = Database::getInstance();
            if ($db->isFallback() && $db->getPdo() === null) {
                return self::getAllFromFilesystem();
            }
            return $db->fetchAll("SELECT t.*, c.name as category_name, c.slug as category_slug FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.status = ? ORDER BY t.featured DESC, t.name ASC", [TOOL_STATUS_ACTIVE]);
        } catch (Exception $e) {
            error_log("getAllActive fallback: " . $e->getMessage());
            return self::getAllFromFilesystem();
        }
    }

    public static function getBySlug(string $slug): ?array {
        try {
            $db = Database::getInstance();
            if ($db->isFallback() && $db->getPdo() === null) {
                return self::getFromFilesystem($slug);
            }
            $tool = $db->fetchOne("SELECT t.*, c.name as category_name, c.slug as category_slug FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.slug = ? AND t.status = ?", [$slug, TOOL_STATUS_ACTIVE]);
            if ($tool) return $tool;
            // Try filesystem fallback
            return self::getFromFilesystem($slug);
        } catch (Exception $e) {
            error_log("getBySlug fallback: " . $e->getMessage());
            return self::getFromFilesystem($slug);
        }
    }

    public static function getByCategory(int $categoryId): array {
        try {
            $db = Database::getInstance();
            return $db->fetchAll("SELECT * FROM tools WHERE category_id = ? AND status = ? ORDER BY featured DESC, name ASC", [$categoryId, TOOL_STATUS_ACTIVE]);
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getPopular(int $limit = 12): array {
        try {
            $db = Database::getInstance();
            if ($db->isFallback() && $db->getPdo() === null) {
                $all = self::getAllFromFilesystem();
                return array_slice($all, 0, $limit);
            }
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
        } catch (Exception $e) {
            $all = self::getAllFromFilesystem();
            return array_slice($all, 0, $limit);
        }
    }

    public static function getFeatured(int $limit = 8): array {
        try {
            $db = Database::getInstance();
            if ($db->isFallback() && $db->getPdo() === null) {
                $all = self::getAllFromFilesystem();
                return array_slice($all, 0, $limit);
            }
            return $db->fetchAll("SELECT t.*, c.name as category_name FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.status = ? AND t.featured = 1 ORDER BY t.updated_at DESC LIMIT ?", [TOOL_STATUS_ACTIVE, $limit]);
        } catch (Exception $e) {
            $all = self::getAllFromFilesystem();
            return array_slice($all, 0, $limit);
        }
    }

    public static function getRelated(int $toolId, int $categoryId, int $limit = 6): array {
        try {
            $db = Database::getInstance();
            return $db->fetchAll(
                "SELECT * FROM tools WHERE category_id = ? AND id != ? AND status = ? ORDER BY RANDOM() LIMIT ?",
                [$categoryId, $toolId, TOOL_STATUS_ACTIVE, $limit]
            );
        } catch (Exception $e) {
            $all = self::getAllFromFilesystem();
            $filtered = array_filter($all, fn($t) => ($t['id'] ?? 0) != $toolId);
            return array_slice(array_values($filtered), 0, $limit);
        }
    }

    public static function search(string $query, int $limit = 20): array {
        try {
            $db = Database::getInstance();
            if ($db->isFallback() && $db->getPdo() === null) {
                return self::searchFilesystem($query, $limit);
            }
            $like = '%' . $query . '%';
            return $db->fetchAll(
                "SELECT t.*, c.name as category_name FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.status = ? AND (t.name LIKE ? OR t.slug LIKE ? OR t.description LIKE ? OR c.name LIKE ?) ORDER BY t.featured DESC, t.name ASC LIMIT ?",
                [TOOL_STATUS_ACTIVE, $like, $like, $like, $like, $limit]
            );
        } catch (Exception $e) {
            return self::searchFilesystem($query, $limit);
        }
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
        try {
            $db = Database::getInstance();
            return $db->fetchAll("SELECT t.*, c.name as category_name FROM tools t LEFT JOIN categories c ON t.category_id = c.id ORDER BY t.updated_at DESC");
        } catch (Exception $e) {
            return self::getAllFromFilesystem();
        }
    }

    public static function create(array $data): int {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO tools (name, slug, description, category_id, source_path, status, featured, requires_login, is_metered, free_allowed, requires_subscription, icon, image, meta_title, meta_description, canonical_url, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, datetime('now'), datetime('now'))",
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
        $db->query("UPDATE tools SET " . implode(', ', $fields) . ", updated_at = datetime('now') WHERE id = ?", $params);
    }

    public static function delete(int $id): void {
        $db = Database::getInstance();
        $db->query("DELETE FROM tools WHERE id = ?", [$id]);
    }

    // Fallback methods - filesystem based
    private static function getAllFromFilesystem(): array {
        $tools = [];
        $toolsPath = ROOT_PATH . '/tools';
        if (!file_exists($toolsPath)) {
            // Try tools.json
            $jsonPath = ROOT_PATH . '/tools.json';
            if (file_exists($jsonPath)) {
                $data = json_decode(file_get_contents($jsonPath), true);
                if ($data) {
                    foreach ($data as $idx => $t) {
                        $title = $t['title'] ?? 'Tool';
                        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
                        $slug = trim(preg_replace('/-+/', '-', $slug), '-');
                        $tools[] = [
                            'id' => $idx + 1,
                            'name' => $title,
                            'slug' => $slug,
                            'description' => $title . ' - free online tool',
                            'category_name' => $t['category'] ?? 'Utility Tools',
                            'category_slug' => 'utility-tools',
                            'status' => 'active',
                            'featured' => 0,
                            'view_count' => 0,
                            'is_metered' => 1
                        ];
                    }
                }
            }
            return $tools;
        }

        $dirs = glob($toolsPath . '/*', GLOB_ONLYDIR);
        foreach ($dirs as $dir) {
            $metaFile = $dir . '/metadata.json';
            $slug = basename($dir);
            if (file_exists($metaFile)) {
                $meta = json_decode(file_get_contents($metaFile), true);
                $tools[] = [
                    'id' => crc32($slug),
                    'name' => $meta['title'] ?? ucwords(str_replace('-', ' ', $slug)),
                    'slug' => $slug,
                    'description' => ($meta['title'] ?? $slug) . ' - free online tool',
                    'category_name' => $meta['category'] ?? 'Utility Tools',
                    'category_slug' => 'utility-tools',
                    'status' => 'active',
                    'featured' => 0,
                    'view_count' => 0,
                    'is_metered' => $meta['is_external'] ? 0 : 1
                ];
            } else {
                $tools[] = [
                    'id' => crc32($slug),
                    'name' => ucwords(str_replace('-', ' ', $slug)),
                    'slug' => $slug,
                    'description' => ucwords(str_replace('-', ' ', $slug)) . ' - free online tool',
                    'category_name' => 'Utility Tools',
                    'category_slug' => 'utility-tools',
                    'status' => 'active',
                    'featured' => 0,
                    'view_count' => 0,
                    'is_metered' => 1
                ];
            }
        }
        return $tools;
    }

    private static function getFromFilesystem(string $slug): ?array {
        $toolsPath = ROOT_PATH . '/tools/' . $slug;
        if (file_exists($toolsPath)) {
            $metaFile = $toolsPath . '/metadata.json';
            if (file_exists($metaFile)) {
                $meta = json_decode(file_get_contents($metaFile), true);
                return [
                    'id' => crc32($slug),
                    'name' => $meta['title'] ?? ucwords(str_replace('-', ' ', $slug)),
                    'slug' => $slug,
                    'description' => ($meta['title'] ?? $slug) . ' - free online tool',
                    'category_name' => $meta['category'] ?? 'Utility Tools',
                    'category_slug' => 'utility-tools',
                    'status' => 'active',
                    'featured' => 0,
                    'view_count' => 0,
                    'is_metered' => $meta['is_external'] ? 0 : 1,
                    'category_id' => 1
                ];
            }
            return [
                'id' => crc32($slug),
                'name' => ucwords(str_replace('-', ' ', $slug)),
                'slug' => $slug,
                'description' => ucwords(str_replace('-', ' ', $slug)) . ' - free online tool',
                'category_name' => 'Utility Tools',
                'category_slug' => 'utility-tools',
                'status' => 'active',
                'featured' => 0,
                'view_count' => 0,
                'is_metered' => 1,
                'category_id' => 1
            ];
        }
        return null;
    }

    private static function searchFilesystem(string $query, int $limit): array {
        $all = self::getAllFromFilesystem();
        $query = strtolower($query);
        $filtered = array_filter($all, function($t) use ($query) {
            return strpos(strtolower($t['name']), $query) !== false || 
                   strpos(strtolower($t['slug']), $query) !== false ||
                   strpos(strtolower($t['description']), $query) !== false;
        });
        return array_slice(array_values($filtered), 0, $limit);
    }
}
