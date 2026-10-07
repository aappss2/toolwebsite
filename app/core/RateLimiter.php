<?php
class RateLimiter {
    private $storagePath;

    public function __construct() {
        $this->storagePath = STORAGE_PATH . '/cache/ratelimit';
        if (!file_exists($this->storagePath)) {
            mkdir($this->storagePath, 0755, true);
        }
    }

    public function isAllowed(string $key, int $maxAttempts, int $windowSeconds): bool {
        $file = $this->storagePath . '/' . md5($key) . '.json';
        $now = time();
        
        $data = ['attempts' => [], 'blocked_until' => 0];
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $data = json_decode($content, true) ?: $data;
        }

        // Clean old attempts
        $data['attempts'] = array_filter($data['attempts'], fn($t) => $t > $now - $windowSeconds);

        if ($data['blocked_until'] > $now) {
            return false;
        }

        if (count($data['attempts']) >= $maxAttempts) {
            $data['blocked_until'] = $now + $windowSeconds;
            file_put_contents($file, json_encode($data));
            return false;
        }

        $data['attempts'][] = $now;
        file_put_contents($file, json_encode($data));
        return true;
    }

    public function check(string $key, int $maxAttempts, int $windowSeconds): void {
        if (!$this->isAllowed($key, $maxAttempts, $windowSeconds)) {
            http_response_code(429);
            die(json_encode(['error' => 'Too many requests. Please try again later.']));
        }
    }

    public function reset(string $key): void {
        $file = $this->storagePath . '/' . md5($key) . '.json';
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // DB-based rate limiting for more persistent tracking (alternative)
    public static function checkDb(string $identifier, string $action, int $maxAttempts, int $windowSeconds): bool {
        try {
            $db = Database::getInstance();
            $since = date('Y-m-d H:i:s', time() - $windowSeconds);
            $count = $db->fetchOne(
                "SELECT COUNT(*) as cnt FROM api_usage WHERE ip_address = ? AND endpoint = ? AND created_at > ?",
                [$identifier, $action, $since]
            );
            $cnt = $count['cnt'] ?? 0;
            if ($cnt >= $maxAttempts) {
                return false;
            }
            // Log this attempt
            $db->query(
                "INSERT INTO api_usage (user_id, endpoint, ip_address, user_agent, created_at) VALUES (NULL, ?, ?, ?, NOW())",
                [$action, $identifier, Security::getUserAgent()]
            );
            return true;
        } catch (Exception $e) {
            // If DB fails, allow (fail open for rate limiting, but log)
            error_log("RateLimiter DB error: " . $e->getMessage());
            return true;
        }
    }
}
