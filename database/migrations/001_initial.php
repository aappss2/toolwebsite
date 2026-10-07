<?php
// Migration script to seed DB from tools.json
require_once __DIR__ . '/../../app/config/config.php';

$db = Database::getInstance();

// Ensure SQLite file exists
$sqlitePath = STORAGE_PATH . '/database.sqlite';
if (!file_exists($sqlitePath)) {
    touch($sqlitePath);
    chmod($sqlitePath, 0644);
}

// For SQLite, we need to adapt schema
$isSqlite = $db->getPdo()->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite';

if ($isSqlite) {
    echo "Using SQLite - creating tables...\n";
    $db->getPdo()->exec("
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
        CREATE TABLE IF NOT EXISTS categories (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            slug TEXT NOT NULL UNIQUE,
            description TEXT,
            parent_id INTEGER NULL,
            icon TEXT NULL,
            image TEXT NULL,
            meta_title TEXT NULL,
            meta_description TEXT NULL,
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
            image TEXT NULL,
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
        CREATE TABLE IF NOT EXISTS contact_messages (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL,
            subject TEXT NULL,
            message TEXT NOT NULL,
            status TEXT DEFAULT 'new',
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
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
    ");
} else {
    // MySQL - run schema.sql
    $schema = file_get_contents(__DIR__ . '/../schema.sql');
    // Split by ; and execute
    $statements = array_filter(array_map('trim', explode(';', $schema)));
    foreach ($statements as $stmt) {
        if (empty($stmt)) continue;
        try {
            $db->getPdo()->exec($stmt);
        } catch (Exception $e) {
            // Ignore duplicate errors
            echo "Schema exec warning: " . $e->getMessage() . "\n";
        }
    }
}

echo "Tables created\n";

// Seed categories
$categories = [
    ['AI Tools', 'ai-tools', 'AI-powered tools for writing, summarizing, and productivity', null, '🤖', 1],
    ['PDF Tools', 'pdf-tools', 'Merge, split, compress, convert PDF files', null, '📄', 2],
    ['Image Tools', 'image-tools', 'Edit, compress, convert, and enhance images', null, '🖼️', 3],
    ['Audio & Video Tools', 'audio-video-tools', 'Convert, trim, and edit audio and video', null, '🎬', 4],
    ['Calculators', 'calculators', 'All types of calculators for daily use', null, '🧮', 5],
    ['Financial Calculators', 'financial-calculators', 'Loan, investment, tax, and finance calculators', 5, '💰', 6],
    ['Health & Fitness', 'health-fitness', 'BMI, calorie, fitness calculators', 5, '🏋️', 7],
    ['Educational Tools', 'educational-tools', 'Tools for students and teachers', 5, '📚', 8],
    ['Sports', 'sports', 'Sports statistics calculators', 5, '⚾', 9],
    ['Webmaster Tools', 'webmaster-tools', 'SEO, domain, and webmaster utilities', null, '🌐', 10],
    ['Text & Document Tools', 'text-document-tools', 'Text editing and document utilities', null, '📝', 11],
    ['Utility Tools', 'utility-tools', 'General utility tools', null, '🛠️', 12],
    ['Compression Tools', 'compression-tools', 'Compress PDF, images, and files', null, '🗜️', 13],
    ['Fun & Games', 'fun-games', 'Fun tools and games', null, '🎮', 14],
];

foreach ($categories as $cat) {
    try {
        $existing = $db->fetchOne("SELECT id FROM categories WHERE slug = ?", [$cat[1]]);
        if (!$existing) {
            $db->query(
                "INSERT INTO categories (name, slug, description, parent_id, icon, status, sort_order) VALUES (?, ?, ?, ?, ?, 'active', ?)",
                [$cat[0], $cat[1], $cat[2], $cat[3], $cat[4], $cat[5]]
            );
            echo "Inserted category {$cat[0]}\n";
        }
    } catch (Exception $e) {
        echo "Category error {$cat[0]}: " . $e->getMessage() . "\n";
    }
}

// Seed plans
$plans = [
    ['Free', 'free', '10 free uses per account', 0, 'INR', 'month', 1, 'active', json_encode(["10 free tool uses","Basic support","Standard tools"])],
    ['Pro Monthly', 'pro-monthly', 'Unlimited tool usage billed monthly', 299, 'INR', 'month', 1, 'active', json_encode(["Unlimited tool uses","Priority support","All tools including premium","Ad-free experience","Fast processing"])],
    ['Pro Yearly', 'pro-yearly', 'Unlimited tool usage billed yearly - Save 40%', 1999, 'INR', 'year', 1, 'active', json_encode(["Unlimited tool uses","Priority support","All tools including premium","Ad-free experience","Fast processing","40% savings"])],
];

foreach ($plans as $plan) {
    try {
        $existing = $db->fetchOne("SELECT id FROM subscription_plans WHERE slug = ?", [$plan[1]]);
        if (!$existing) {
            $db->query(
                "INSERT INTO subscription_plans (name, slug, description, amount, currency, billing_interval, billing_interval_count, status, features) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)",
                $plan
            );
            echo "Inserted plan {$plan[0]}\n";
        }
    } catch (Exception $e) {
        echo "Plan error {$plan[0]}: " . $e->getMessage() . "\n";
    }
}

// Seed tools from tools.json
$toolsJson = json_decode(file_get_contents(ROOT_PATH . '/tools.json'), true);
$catMap = [
    '🤖 AI Tools' => 'ai-tools',
    '📚 Educational Tools' => 'educational-tools',
    'CALCULATOR' => 'calculators',
    'UTILITY TOOLS' => 'utility-tools',
    'IMAGE TOOLS' => 'image-tools',
    'PDF TOOLS' => 'pdf-tools',
    'Audio & Video Tools' => 'audio-video-tools',
    'FINANCIAL CALCULATORS' => 'financial-calculators',
    'HEALTH AND FITNESS' => 'health-fitness',
    'SPORTS-BASEBALL' => 'sports',
    'SPORTS-BASKETBALL' => 'sports',
    'SPORTS-CRICKET' => 'sports',
    'WEBMASTER' => 'webmaster-tools',
    'DOCUMENTS' => 'text-document-tools',
    'FUN' => 'fun-games',
    'HIGH COMPRESSOR' => 'compression-tools',
    'CRYPTO TAX CALCULATOR' => 'financial-calculators',
    'EDUCATIONAL CONTENT' => 'educational-tools',
];

function slugify_local($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'n-a';
}

$slugCounts = [];
foreach ($toolsJson as $idx => $t) {
    $title = trim($t['title']);
    $link = trim($t['link']);
    $rawCat = trim($t['category']);
    $normSlug = $catMap[$rawCat] ?? 'utility-tools';
    
    $slug = slugify_local($title);
    // Handle duplicates
    if (isset($slugCounts[$slug])) {
        $slugCounts[$slug]++;
        $slug = $slug . '-' . $slugCounts[$slug];
    } else {
        $slugCounts[$slug] = 1;
    }
    // Special handling for known duplicates
    if ($slug === 'english-grammer-voice' && $idx > 65) {
        $slug = 'english-grammar-voice-' . ($idx+1);
    }
    if ($slug === 'batting-average-calculator' && $slugCounts['batting-average-calculator'] > 1) {
        $slug = 'batting-average-calculator-cricket';
    }

    try {
        $existing = $db->fetchOne("SELECT id FROM tools WHERE slug = ?", [$slug]);
        if ($existing) {
            echo "Tool exists $slug\n";
            continue;
        }
        $catRow = $db->fetchOne("SELECT id FROM categories WHERE slug = ?", [$normSlug]);
        $catId = $catRow ? $catRow['id'] : null;

        $desc = $title . " - free online tool. Fast, secure, easy to use.";
        $isExternal = strpos($link, 'http') === 0 ? 1 : 0;
        $requiresLogin = 0;
        $isMetered = 1;
        if ($isExternal) {
            $isMetered = 0; // External AI tools not metered initially
        }

        $db->query(
            "INSERT INTO tools (name, slug, description, category_id, source_path, status, featured, requires_login, is_metered, free_allowed, requires_subscription, meta_title, meta_description, canonical_url) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)",
            [
                $title,
                $slug,
                $desc,
                $catId,
                $link,
                'active',
                0,
                $requiresLogin,
                $isMetered,
                1,
                0,
                $title . " - Free Online Tool | PromptTai",
                $desc,
                "https://tool.prompttai.com/tools/$slug/"
            ]
        );
        $toolId = $db->lastInsertId();
        echo "Inserted tool $title => $slug (ID $toolId)\n";

        // Insert alias for old URL
        $oldUrl = $isExternal ? $link : "https://jdiro.com/$link";
        $db->query(
            "INSERT INTO tool_aliases (tool_id, alias, alias_type) VALUES (?, ?, ?)",
            [$toolId, $slugify_local($link), 'old_slug']
        );

        // Migration map
        $newUrl = "https://tool.prompttai.com/tools/$slug/";
        $db->query(
            "INSERT INTO migration_map (old_url, new_url, redirect_type, status, notes) VALUES (?, ?, 301, 'migrated', ?)",
            [$oldUrl, $newUrl, "$rawCat -> $normSlug"]
        );

    } catch (Exception $e) {
        echo "Tool error $title: " . $e->getMessage() . "\n";
    }
}

echo "Migration complete\n";
