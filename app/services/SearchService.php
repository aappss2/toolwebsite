<?php
class SearchService {
    public static function search(string $query, int $limit = 20): array {
        $query = trim($query);
        if (strlen($query) < 2) return [];
        
        $db = Database::getInstance();
        $like = '%' . $query . '%';
        
        // Try fulltext if available, fallback to LIKE
        try {
            return $db->fetchAll(
                "SELECT t.id, t.name, t.slug, t.description, t.icon, c.name as category_name, c.slug as category_slug 
                 FROM tools t 
                 LEFT JOIN categories c ON t.category_id = c.id 
                 WHERE t.status = ? AND (t.name LIKE ? OR t.slug LIKE ? OR t.description LIKE ? OR c.name LIKE ?)
                 ORDER BY 
                    CASE 
                        WHEN t.name LIKE ? THEN 1
                        WHEN t.slug LIKE ? THEN 2
                        ELSE 3
                    END,
                    t.featured DESC, t.name ASC
                 LIMIT ?",
                [TOOL_STATUS_ACTIVE, $like, $like, $like, $like, $like, $like, $limit]
            );
        } catch (Exception $e) {
            return [];
        }
    }

    public static function getPopularSearches(): array {
        return [
            'PDF Merger',
            'Image Compressor',
            'Age Calculator',
            'BMI Calculator',
            'Loan Calculator',
            'Word to PDF',
            'Background Remover',
            'GST Calculator'
        ];
    }

    public static function logSearch(string $query, ?int $userId = null): void {
        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO api_usage (user_id, endpoint, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$userId, 'search:' . substr($query, 0, 100), Security::getClientIp(), Security::getUserAgent()]
            );
        } catch (Exception $e) {
            // ignore
        }
    }
}
