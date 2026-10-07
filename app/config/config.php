<?php
// Main config - loads constants, env, database

require_once __DIR__ . '/constants.php';

// Load .env if exists (outside public root)
$envPath = ROOT_PATH . '/.env';
if (file_exists($envPath)) {
    $lines = file($envPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) continue;
        if (!strpos($line, '=')) continue;
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        $value = trim($value, '"\'');
        if (!defined($key)) {
            define($key, $value);
        }
        $_ENV[$key] = $value;
    }
}

// Default config values (use placeholders, never hardcode real secrets)
if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'prompttai_tools');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') ?: '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

if (!defined('RAZORPAY_KEY_ID')) define('RAZORPAY_KEY_ID', getenv('RAZORPAY_KEY_ID') ?: 'rzp_test_placeholder');
if (!defined('RAZORPAY_KEY_SECRET')) define('RAZORPAY_KEY_SECRET', getenv('RAZORPAY_KEY_SECRET') ?: 'secret_placeholder');
if (!defined('RAZORPAY_WEBHOOK_SECRET')) define('RAZORPAY_WEBHOOK_SECRET', getenv('RAZORPAY_WEBHOOK_SECRET') ?: 'webhook_secret_placeholder');

if (!defined('APP_ENV')) define('APP_ENV', getenv('APP_ENV') ?: 'production');
if (!defined('APP_DEBUG')) define('APP_DEBUG', getenv('APP_DEBUG') ? filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN) : false);

// Site settings defaults
if (!defined('SITE_ADSENSE_CLIENT')) define('SITE_ADSENSE_CLIENT', getenv('SITE_ADSENSE_CLIENT') ?: 'ca-pub-8977523182593956');
if (!defined('SITE_GTAG_ID')) define('SITE_GTAG_ID', getenv('SITE_GTAG_ID') ?: 'G-DPSML6XTSC');

// Error handling
if (APP_DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED);
}

// Timezone
date_default_timezone_set('UTC');

// Autoload helpers
require_once APP_PATH . '/helpers/helpers.php';
require_once APP_PATH . '/helpers/slug.php';
require_once APP_PATH . '/helpers/url.php';

// Core classes
require_once APP_PATH . '/core/Database.php';
require_once APP_PATH . '/core/Security.php';
require_once APP_PATH . '/core/Csrf.php';
require_once APP_PATH . '/core/RateLimiter.php';
require_once APP_PATH . '/core/Auth.php';
require_once APP_PATH . '/core/Validator.php';
require_once APP_PATH . '/core/Seo.php';
require_once APP_PATH . '/core/Router.php';

// Services
require_once APP_PATH . '/services/ToolService.php';
require_once APP_PATH . '/services/CategoryService.php';
require_once APP_PATH . '/services/SearchService.php';
require_once APP_PATH . '/services/AdService.php';
require_once APP_PATH . '/services/UploadService.php';
require_once APP_PATH . '/usage/UsageService.php';
require_once APP_PATH . '/auth/UserService.php';
require_once APP_PATH . '/auth/PasswordResetService.php';
require_once APP_PATH . '/billing/PaymentGatewayInterface.php';
require_once APP_PATH . '/billing/RazorpayGateway.php';
require_once APP_PATH . '/billing/StripeGateway.php';
require_once APP_PATH . '/billing/SubscriptionService.php';
require_once APP_PATH . '/billing/WebhookHandler.php';
require_once APP_PATH . '/admin/AdminService.php';
