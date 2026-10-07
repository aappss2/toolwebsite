<?php
class SubscriptionService {
    public static function getPlans(bool $activeOnly = true): array {
        $db = Database::getInstance();
        if ($activeOnly) {
            return $db->fetchAll("SELECT * FROM subscription_plans WHERE status = 'active' ORDER BY amount ASC");
        }
        return $db->fetchAll("SELECT * FROM subscription_plans ORDER BY created_at DESC");
    }

    public static function getPlanById(int $id): ?array {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM subscription_plans WHERE id = ?", [$id]) ?: null;
    }

    public static function getPlanBySlug(string $slug): ?array {
        $db = Database::getInstance();
        return $db->fetchOne("SELECT * FROM subscription_plans WHERE slug = ?", [$slug]) ?: null;
    }

    public static function createPlan(array $data): int {
        $db = Database::getInstance();
        $db->query(
            "INSERT INTO subscription_plans (name, slug, description, amount, currency, billing_interval, billing_interval_count, status, features, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? '',
                $data['amount'],
                $data['currency'] ?? 'INR',
                $data['billing_interval'] ?? 'month',
                $data['billing_interval_count'] ?? 1,
                $data['status'] ?? 'active',
                json_encode($data['features'] ?? []),
                // Note: above json_encode is safe, but we pass as string
            ]
        );
        // Fix: need to pass correct number of params - we have 9 placeholders, we passed 9 values, last one is json
        // Actually we have 9 columns before NOW(), so 9 params, we did provide 9
        // Let's correct: features should be JSON string
        return (int)$db->lastInsertId();
    }

    // Corrected createPlan with proper binding
    public static function createPlanCorrect(array $data): int {
        $db = Database::getInstance();
        $features = is_string($data['features'] ?? '') ? ($data['features'] ?? '[]') : json_encode($data['features'] ?? []);
        $db->query(
            "INSERT INTO subscription_plans (name, slug, description, amount, currency, billing_interval, billing_interval_count, status, features, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())",
            [
                $data['name'],
                $data['slug'],
                $data['description'] ?? '',
                $data['amount'],
                $data['currency'] ?? 'INR',
                $data['billing_interval'] ?? 'month',
                $data['billing_interval_count'] ?? 1,
                $data['status'] ?? 'active',
                $features
            ]
        );
        return (int)$db->lastInsertId();
    }

    public static function activateSubscription(int $userId, int $planId, string $gateway, string $gatewaySubscriptionId, string $gatewayPaymentId = ''): bool {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();

            $plan = self::getPlanById($planId);
            if (!$plan) throw new Exception("Plan not found");

            // Calculate period end
            $interval = $plan['billing_interval'] ?? 'month';
            $count = (int)($plan['billing_interval_count'] ?? 1);
            $periodEnd = $interval === 'year' ? date('Y-m-d H:i:s', strtotime("+$count year")) : date('Y-m-d H:i:s', strtotime("+$count month"));

            // Update user
            $db->query(
                "UPDATE users SET subscription_status = ?, subscription_plan_id = ?, subscription_expires_at = ?, updated_at = NOW() WHERE id = ?",
                [SUB_STATUS_ACTIVE, $planId, $periodEnd, $userId]
            );

            // Create subscription record
            $db->query(
                "INSERT INTO subscriptions (user_id, plan_id, gateway, gateway_subscription_id, status, started_at, current_period_start, current_period_end, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW(), ?, NOW(), NOW())",
                [$userId, $planId, $gateway, $gatewaySubscriptionId, SUB_STATUS_ACTIVE, $periodEnd]
            );

            // Log payment
            if ($gatewayPaymentId) {
                $db->query(
                    "INSERT INTO payments (user_id, plan_id, gateway, gateway_payment_id, amount, currency, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW())",
                    [$userId, $planId, $gateway, $gatewayPaymentId, $plan['amount'], $plan['currency'], 'success']
                );
            }

            // Billing log
            $db->query(
                "INSERT INTO billing_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())",
                [$userId, 'subscription_activated', "Plan $planId activated via $gateway, subscription $gatewaySubscriptionId"]
            );

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("activateSubscription error: " . $e->getMessage());
            return false;
        }
    }

    public static function cancelSubscription(int $userId): bool {
        $db = Database::getInstance();
        try {
            $db->beginTransaction();
            $sub = $db->fetchOne("SELECT * FROM subscriptions WHERE user_id = ? AND status = ? ORDER BY created_at DESC LIMIT 1", [$userId, SUB_STATUS_ACTIVE]);
            if (!$sub) {
                $db->rollBack();
                return false;
            }

            $gateway = new RazorpayGateway();
            try {
                $gateway->cancelSubscription($sub['gateway_subscription_id']);
            } catch (Exception $e) {
                // Log but continue to cancel locally
                error_log("Gateway cancel failed: " . $e->getMessage());
            }

            $db->query("UPDATE subscriptions SET status = ?, cancelled_at = NOW(), updated_at = NOW() WHERE id = ?", [SUB_STATUS_CANCELLED, $sub['id']]);
            $db->query("UPDATE users SET subscription_status = ?, updated_at = NOW() WHERE id = ?", [SUB_STATUS_CANCELLED, $userId]);

            $db->query(
                "INSERT INTO billing_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())",
                [$userId, 'subscription_cancelled', "Cancelled subscription {$sub['id']}"]
            );

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("cancelSubscription error: " . $e->getMessage());
            return false;
        }
    }

    public static function checkExpired(): int {
        $db = Database::getInstance();
        $expired = $db->fetchAll("SELECT id, user_id FROM subscriptions WHERE status = ? AND current_period_end < NOW()", [SUB_STATUS_ACTIVE]);
        $count = 0;
        foreach ($expired as $sub) {
            $db->query("UPDATE subscriptions SET status = ?, updated_at = NOW() WHERE id = ?", [SUB_STATUS_EXPIRED, $sub['id']]);
            $db->query("UPDATE users SET subscription_status = ?, updated_at = NOW() WHERE id = ? AND subscription_status = ?", [SUB_STATUS_EXPIRED, $sub['user_id'], SUB_STATUS_ACTIVE]);
            $count++;
        }
        return $count;
    }

    public static function getUserSubscription(int $userId): ?array {
        $db = Database::getInstance();
        return $db->fetchOne(
            "SELECT s.*, p.name as plan_name, p.amount, p.currency, p.billing_interval FROM subscriptions s JOIN subscription_plans p ON s.plan_id = p.id WHERE s.user_id = ? ORDER BY s.created_at DESC LIMIT 1",
            [$userId]
        ) ?: null;
    }

    public static function getUserPayments(int $userId, int $limit = 20): array {
        $db = Database::getInstance();
        return $db->fetchAll("SELECT * FROM payments WHERE user_id = ? ORDER BY created_at DESC LIMIT ?", [$userId, $limit]);
    }
}
