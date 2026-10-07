<?php
class UsageService {
    public static function canUseTool(?array $user, array $tool): array {
        // Returns [allowed => bool, reason => string, remaining => int]
        
        // If tool doesn't require metering, always allow
        if (($tool['is_metered'] ?? 1) == 0) {
            return ['allowed' => true, 'reason' => 'free_tool', 'remaining' => $user['free_uses_remaining'] ?? FREE_USES_DEFAULT];
        }

        // If no user (anonymous), check anon limit
        if (!$user) {
            // Allow 2 anon uses per day per IP, but encourage login
            $ip = Security::getClientIp();
            $db = Database::getInstance();
            try {
                $since = date('Y-m-d H:i:s', strtotime('-1 day'));
                $count = $db->fetchOne(
                    "SELECT COUNT(*) as cnt FROM tool_usage WHERE user_id IS NULL AND ip_address = ? AND created_at > ?",
                    [$ip, $since]
                );
                $cnt = $count['cnt'] ?? 0;
                if ($cnt >= FREE_USES_ANON_DAILY) {
                    return ['allowed' => false, 'reason' => 'anon_limit', 'remaining' => 0, 'message' => 'Anonymous limit reached. Please register for 10 free uses.'];
                }
                return ['allowed' => true, 'reason' => 'anon_allowed', 'remaining' => FREE_USES_ANON_DAILY - $cnt];
            } catch (Exception $e) {
                return ['allowed' => true, 'reason' => 'anon_fallback', 'remaining' => 1];
            }
        }

        // Check subscription
        if (self::hasActiveSubscription($user)) {
            return ['allowed' => true, 'reason' => 'subscription_active', 'remaining' => -1]; // unlimited
        }

        // Check free uses
        $remaining = (int)($user['free_uses_remaining'] ?? 0);
        if ($remaining > 0) {
            return ['allowed' => true, 'reason' => 'free_uses', 'remaining' => $remaining];
        }

        return ['allowed' => false, 'reason' => 'no_credits', 'remaining' => 0, 'message' => "You've used all " . FREE_USES_DEFAULT . " free uses."];
    }

    public static function consumeUsage(?array $user, array $tool, bool $success = true): bool {
        if (!$success) {
            // Don't consume on failure where possible
            return true;
        }

        if (($tool['is_metered'] ?? 1) == 0) {
            // Log usage but don't consume
            self::logUsage($user, $tool, 0, 'free_tool');
            return true;
        }

        if (!$user) {
            // Log anon usage
            self::logUsage(null, $tool, 0, 'anon');
            return true;
        }

        if (self::hasActiveSubscription($user)) {
            self::logUsage($user, $tool, 0, 'subscription');
            return true;
        }

        // Consume one free use
        try {
            $db = Database::getInstance();
            $db->beginTransaction();
            
            // Lock user row for update
            $current = $db->fetchOne("SELECT free_uses_remaining, free_uses_used FROM users WHERE id = ? FOR UPDATE", [$user['id']]);
            if (!$current) {
                $db->rollBack();
                return false;
            }

            $remaining = (int)$current['free_uses_remaining'];
            if ($remaining <= 0) {
                $db->rollBack();
                return false;
            }

            $db->query(
                "UPDATE users SET free_uses_remaining = free_uses_remaining - 1, free_uses_used = free_uses_used + 1, total_free_uses = total_free_uses + 1 WHERE id = ?",
                [$user['id']]
            );

            self::logUsage($user, $tool, 1, 'free_use');

            $db->commit();
            return true;
        } catch (Exception $e) {
            try {
                $db->rollBack();
            } catch (Exception $e2) {}
            error_log("consumeUsage error: " . $e->getMessage());
            return false;
        }
    }

    public static function hasActiveSubscription(?array $user): bool {
        if (!$user) return false;
        if (($user['subscription_status'] ?? '') !== SUB_STATUS_ACTIVE) return false;
        $expires = $user['subscription_expires_at'] ?? null;
        if (!$expires) return false;
        return strtotime($expires) > time();
    }

    public static function getUsageStatus(?array $user): array {
        if (!$user) {
            $ip = Security::getClientIp();
            $db = Database::getInstance();
            try {
                $since = date('Y-m-d H:i:s', strtotime('-1 day'));
                $count = $db->fetchOne(
                    "SELECT COUNT(*) as cnt FROM tool_usage WHERE user_id IS NULL AND ip_address = ? AND created_at > ?",
                    [$ip, $since]
                );
                $used = $count['cnt'] ?? 0;
                return [
                    'is_logged_in' => false,
                    'has_subscription' => false,
                    'free_uses_remaining' => max(0, FREE_USES_ANON_DAILY - $used),
                    'free_uses_used' => $used,
                    'free_uses_total' => FREE_USES_ANON_DAILY,
                    'subscription_status' => 'none'
                ];
            } catch (Exception $e) {
                return [
                    'is_logged_in' => false,
                    'has_subscription' => false,
                    'free_uses_remaining' => FREE_USES_ANON_DAILY,
                    'free_uses_used' => 0,
                    'free_uses_total' => FREE_USES_ANON_DAILY,
                    'subscription_status' => 'none'
                ];
            }
        }

        $freeDefault = (int)get_site_setting('free_uses_default', (string)FREE_USES_DEFAULT);
        return [
            'is_logged_in' => true,
            'has_subscription' => self::hasActiveSubscription($user),
            'free_uses_remaining' => (int)($user['free_uses_remaining'] ?? 0),
            'free_uses_used' => (int)($user['free_uses_used'] ?? 0),
            'free_uses_total' => $freeDefault,
            'subscription_status' => $user['subscription_status'] ?? 'free',
            'subscription_expires_at' => $user['subscription_expires_at'] ?? null,
            'subscription_plan_id' => $user['subscription_plan_id'] ?? null
        ];
    }

    public static function logUsage(?array $user, array $tool, int $creditsUsed, string $usageType): void {
        try {
            $db = Database::getInstance();
            $db->query(
                "INSERT INTO tool_usage (user_id, tool_id, usage_type, credits_used, ip_address, user_agent, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())",
                [
                    $user['id'] ?? null,
                    $tool['id'],
                    $usageType,
                    $creditsUsed,
                    Security::getClientIp(),
                    Security::getUserAgent()
                ]
            );
        } catch (Exception $e) {
            error_log("logUsage error: " . $e->getMessage());
        }
    }

    public static function grantCredits(int $userId, int $credits): bool {
        try {
            $db = Database::getInstance();
            $db->query(
                "UPDATE users SET free_uses_remaining = free_uses_remaining + ?, total_free_uses = total_free_uses + ? WHERE id = ?",
                [$credits, $credits, $userId]
            );
            log_admin_action('grant_credits', "Granted $credits credits to user $userId");
            return true;
        } catch (Exception $e) {
            error_log("grantCredits error: " . $e->getMessage());
            return false;
        }
    }
}
