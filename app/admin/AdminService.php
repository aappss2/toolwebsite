<?php
class AdminService {
    public static function getDashboardStats(): array {
        $db = Database::getInstance();
        try {
            $stats = [];
            $stats['total_users'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM users")['cnt'] ?? 0;
            $stats['active_users'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM users WHERE last_login_at > DATE_SUB(NOW(), INTERVAL 30 DAY)")['cnt'] ?? 0;
            $stats['total_tools'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM tools")['cnt'] ?? 0;
            $stats['active_tools'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM tools WHERE status = ?", [TOOL_STATUS_ACTIVE])['cnt'] ?? 0;
            $stats['total_subscriptions'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM subscriptions WHERE status = ?", [SUB_STATUS_ACTIVE])['cnt'] ?? 0;
            $stats['total_revenue'] = $db->fetchOne("SELECT SUM(amount) as total FROM payments WHERE status = 'success'")['total'] ?? 0;
            $stats['today_usage'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM tool_usage WHERE created_at > CURDATE()")['cnt'] ?? 0;
            $stats['free_usage_total'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM tool_usage WHERE usage_type = 'free_use'")['cnt'] ?? 0;
            $stats['failed_payments'] = $db->fetchOne("SELECT COUNT(*) as cnt FROM payments WHERE status = 'failed'")['cnt'] ?? 0;
            
            // Popular tools
            $stats['popular_tools'] = $db->fetchAll(
                "SELECT t.name, t.slug, COUNT(tu.id) as usage_count FROM tools t LEFT JOIN tool_usage tu ON t.id = tu.tool_id GROUP BY t.id ORDER BY usage_count DESC LIMIT 10"
            );

            // Recent users
            $stats['recent_users'] = $db->fetchAll("SELECT id, name, email, created_at FROM users ORDER BY created_at DESC LIMIT 5");

            // Category usage
            $stats['category_usage'] = $db->fetchAll(
                "SELECT c.name, COUNT(tu.id) as usage_count FROM categories c LEFT JOIN tools t ON c.id = t.category_id LEFT JOIN tool_usage tu ON t.id = tu.tool_id GROUP BY c.id ORDER BY usage_count DESC"
            );

            return $stats;
        } catch (Exception $e) {
            error_log("getDashboardStats error: " . $e->getMessage());
            return [
                'total_users' => 0,
                'active_users' => 0,
                'total_tools' => 0,
                'active_tools' => 0,
                'total_subscriptions' => 0,
                'total_revenue' => 0,
                'today_usage' => 0,
                'free_usage_total' => 0,
                'failed_payments' => 0,
                'popular_tools' => [],
                'recent_users' => [],
                'category_usage' => []
            ];
        }
    }

    public static function getAnalytics(int $days = 30): array {
        $db = Database::getInstance();
        try {
            $usageByDay = $db->fetchAll(
                "SELECT DATE(created_at) as date, COUNT(*) as count FROM tool_usage WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY date ASC",
                [$days]
            );

            $revenueByDay = $db->fetchAll(
                "SELECT DATE(created_at) as date, SUM(amount) as total FROM payments WHERE status = 'success' AND created_at > DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY date ASC",
                [$days]
            );

            $usersByDay = $db->fetchAll(
                "SELECT DATE(created_at) as date, COUNT(*) as count FROM users WHERE created_at > DATE_SUB(NOW(), INTERVAL ? DAY) GROUP BY DATE(created_at) ORDER BY date ASC",
                [$days]
            );

            return [
                'usage_by_day' => $usageByDay,
                'revenue_by_day' => $revenueByDay,
                'users_by_day' => $usersByDay
            ];
        } catch (Exception $e) {
            return ['usage_by_day' => [], 'revenue_by_day' => [], 'users_by_day' => []];
        }
    }
}
