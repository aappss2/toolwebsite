<?php
class Database {
    private static $instance = null;
    private $pdo;
    private $isFallback = false;

    private function __construct() {
        $dbType = getenv('DB_TYPE') ?: (defined('DB_TYPE') ? DB_TYPE : 'mysql');
        
        try {
            if ($dbType === 'sqlite') {
                $this->initSqlite();
            } else {
                $this->initMysql();
            }
        } catch (PDOException $e) {
            error_log("Database MySQL failed: " . $e->getMessage() . " - trying SQLite fallback");
            try {
                $this->initSqlite();
                $this->isFallback = true;
            } catch (Exception $e2) {
                error_log("SQLite fallback also failed: " . $e2->getMessage());
                // Don't throw here, allow app to run in degraded mode (file-based)
                $this->pdo = null;
                $this->isFallback = true;
            }
        } catch (Exception $e) {
            error_log("Database init failed: " . $e->getMessage());
            $this->pdo = null;
            $this->isFallback = true;
        }
    }

    private function initSqlite(): void {
        $sqlitePath = STORAGE_PATH . '/database.sqlite';
        $dir = dirname($sqlitePath);
        if (!file_exists($dir)) {
            @mkdir($dir, 0755, true);
        }
        // Create empty file if not exists
        if (!file_exists($sqlitePath)) {
            @touch($sqlitePath);
            @chmod($sqlitePath, 0644);
        }
        $this->pdo = new PDO('sqlite:' . $sqlitePath);
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        
        // Check if tables exist, if not, create minimal tables
        try {
            $this->pdo->query("SELECT 1 FROM tools LIMIT 1");
        } catch (Exception $e) {
            // Tables don't exist, create them
            $this->createSqliteTables();
        }
    }

    private function initMysql(): void {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];
        $this->pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    }

    private function createSqliteTables(): void {
        $sql = "
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password_hash TEXT NOT NULL,
            role TEXT DEFAULT 'user',
            status TEXT DEFAULT 'active',
            free_uses_remaining INTEGER DEFAULT 10,
            total_free_uses INTEGER DEFAULT 10,
            free_uses_used INTEGER DEFAULT 0,
            subscription_status TEXT DEFAULT 'free',
            subscription_plan_id INTEGER NULL,
            subscription_expires_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            last_login_at DATETIME NULL
        );
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            parent_id INTEGER NULL,
            icon TEXT NULL,
            status TEXT DEFAULT 'active',
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS tools (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            category_id INTEGER NULL,
            source_path TEXT NULL,
            status TEXT DEFAULT 'active',
            featured INTEGER DEFAULT 0,
            requires_login INTEGER DEFAULT 0,
            is_metered INTEGER DEFAULT 1,
            free_allowed INTEGER DEFAULT 1,
            requires_subscription INTEGER DEFAULT 0,
            icon TEXT NULL,
            meta_title TEXT NULL,
            meta_description TEXT NULL,
            canonical_url TEXT NULL,
            view_count INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS tool_aliases (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            tool_id INTEGER NOT NULL,
            alias TEXT NOT NULL UNIQUE,
            alias_type TEXT DEFAULT 'old_slug',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS tool_usage (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NULL,
            tool_id INTEGER NOT NULL,
            usage_type TEXT DEFAULT 'free_use',
            credits_used INTEGER DEFAULT 1,
            ip_address TEXT,
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS subscription_plans (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            amount REAL NOT NULL,
            currency TEXT DEFAULT 'INR',
            billing_interval TEXT DEFAULT 'month',
            billing_interval_count INTEGER DEFAULT 1,
            status TEXT DEFAULT 'active',
            features TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS subscriptions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            plan_id INTEGER NOT NULL,
            gateway TEXT DEFAULT 'razorpay',
            gateway_subscription_id TEXT NULL,
            status TEXT DEFAULT 'pending',
            started_at DATETIME NULL,
            current_period_start DATETIME NULL,
            current_period_end DATETIME NULL,
            cancelled_at DATETIME NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS payments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            plan_id INTEGER NULL,
            gateway TEXT DEFAULT 'razorpay',
            gateway_payment_id TEXT NULL,
            gateway_order_id TEXT NULL,
            amount REAL NOT NULL,
            currency TEXT DEFAULT 'INR',
            status TEXT DEFAULT 'pending',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS payment_events (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            gateway TEXT DEFAULT 'razorpay',
            gateway_event_id TEXT NOT NULL UNIQUE,
            event_type TEXT,
            payload TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS billing_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NULL,
            action TEXT NOT NULL,
            details TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS admin_logs (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NULL,
            action TEXT NOT NULL,
            details TEXT,
            ip_address TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS site_settings (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            setting_key TEXT NOT NULL UNIQUE,
            setting_value TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS api_usage (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NULL,
            endpoint TEXT NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS migration_map (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            old_url TEXT NOT NULL,
            new_url TEXT NOT NULL,
            redirect_type INTEGER DEFAULT 301,
            status TEXT DEFAULT 'pending',
            notes TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );
        CREATE TABLE IF NOT EXISTS user_sessions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            user_id INTEGER NOT NULL,
            session_id TEXT NOT NULL,
            ip_address TEXT,
            user_agent TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME
        );
        CREATE TABLE IF NOT EXISTS password_resets (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email TEXT NOT NULL,
            token TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            expires_at DATETIME NOT NULL,
            used_at DATETIME NULL
        );
        ";
        $this->pdo->exec($sql);
        
        // Seed minimal data if empty
        $count = $this->pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
        if ($count == 0) {
            $this->seedMinimalData();
        }
    }

    private function seedMinimalData(): void {
        try {
            $this->pdo->exec("
                INSERT OR IGNORE INTO categories (name, slug, description, icon, sort_order) VALUES
                ('AI Tools', 'ai-tools', 'AI tools', '🤖', 1),
                ('PDF Tools', 'pdf-tools', 'PDF tools', '📄', 2),
                ('Image Tools', 'image-tools', 'Image tools', '🖼️', 3),
                ('Calculators', 'calculators', 'Calculators', '🧮', 5),
                ('Utility Tools', 'utility-tools', 'Utility', '🛠️', 12);
                
                INSERT OR IGNORE INTO subscription_plans (name, slug, description, amount, currency, billing_interval, status, features) VALUES
                ('Free', 'free', '10 free uses', 0, 'INR', 'month', 'active', '[\"10 free uses\"]'),
                ('Pro Monthly', 'pro-monthly', 'Unlimited monthly', 299, 'INR', 'month', 'active', '[\"Unlimited\"]'),
                ('Pro Yearly', 'pro-yearly', 'Unlimited yearly', 1999, 'INR', 'year', 'active', '[\"Unlimited\",\"40% savings\"]');
                
                INSERT OR IGNORE INTO site_settings (setting_key, setting_value) VALUES
                ('free_uses_default', '10'),
                ('ads_enabled', '1');
            ");
            
            // Try to seed tools from tools.json if exists
            $jsonPath = ROOT_PATH . '/tools.json';
            if (file_exists($jsonPath)) {
                $tools = json_decode(file_get_contents($jsonPath), true);
                if ($tools) {
                    $stmt = $this->pdo->prepare("INSERT OR IGNORE INTO tools (name, slug, description, status, is_metered, canonical_url) VALUES (?,?,?,?,?,?)");
                    foreach ($tools as $t) {
                        $title = $t['title'] ?? 'Tool';
                        $slug = strtolower(preg_replace('/[^a-z0-9]+/', '-', $title));
                        $slug = trim(preg_replace('/-+/', '-', $slug), '-');
                        $desc = $title . ' - free online tool';
                        $url = 'https://tool.prompttai.com/tools/' . $slug . '/';
                        $metered = strpos($t['link'] ?? '', 'http') === 0 ? 0 : 1;
                        $stmt->execute([$title, $slug, $desc, 'active', $metered, $url]);
                    }
                }
            }
        } catch (Exception $e) {
            error_log("Seed minimal data failed: " . $e->getMessage());
        }
    }

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function getPdo(): ?PDO {
        return $this->pdo;
    }

    public function isFallback(): bool {
        return $this->isFallback || $this->pdo === null;
    }

    public function query(string $sql, array $params = []): PDOStatement {
        if ($this->pdo === null) {
            throw new Exception("Database not available");
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public function fetchOne(string $sql, array $params = []) {
        if ($this->pdo === null) {
            throw new Exception("Database not available");
        }
        return $this->query($sql, $params)->fetch();
    }

    public function fetchAll(string $sql, array $params = []): array {
        if ($this->pdo === null) {
            throw new Exception("Database not available");
        }
        return $this->query($sql, $params)->fetchAll();
    }

    public function lastInsertId(): string {
        if ($this->pdo === null) return '0';
        return $this->pdo->lastInsertId();
    }

    public function beginTransaction(): bool {
        if ($this->pdo === null) return false;
        return $this->pdo->beginTransaction();
    }

    public function commit(): bool {
        if ($this->pdo === null) return false;
        return $this->pdo->commit();
    }

    public function rollBack(): bool {
        if ($this->pdo === null) return false;
        return $this->pdo->rollBack();
    }
}
