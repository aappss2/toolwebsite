<?php
// Install / Diagnostic check - delete after setup
echo "<h1>PromptTai Tools - Install Check</h1>";
echo "<style>body{font-family:system-ui;padding:20px} .ok{color:green} .fail{color:red} pre{background:#f5f5f5;padding:10px;border-radius:8px}</style>";

$checks = [];

// PHP version
$checks['PHP Version >=8.0'] = version_compare(PHP_VERSION, '8.0.0', '>=') ? 'OK '.PHP_VERSION : 'FAIL '.PHP_VERSION;

// Extensions
$checks['PDO'] = extension_loaded('pdo') ? 'OK' : 'FAIL';
$checks['PDO MySQL'] = extension_loaded('pdo_mysql') ? 'OK' : 'MISSING (will use SQLite fallback)';
$checks['PDO SQLite'] = extension_loaded('pdo_sqlite') ? 'OK' : 'FAIL';
$checks['curl'] = extension_loaded('curl') ? 'OK' : 'FAIL';
$checks['mbstring'] = extension_loaded('mbstring') ? 'OK' : 'FAIL';
$checks['json'] = extension_loaded('json') ? 'OK' : 'FAIL';

// Files
$checks['.env exists'] = file_exists(__DIR__ . '/../.env') ? 'OK' : 'FAIL - Copy .env.example to .env';
$checks['tools.json exists'] = file_exists(__DIR__ . '/../tools.json') ? 'OK' : 'FAIL';
$checks['storage/ writable'] = is_writable(__DIR__ . '/../storage') ? 'OK' : 'FAIL - chmod 755 storage/';
$checks['storage/logs writable'] = is_writable(__DIR__ . '/../storage/logs') ? 'OK' : 'FAIL - chmod 755 storage/logs';
$checks['public/uploads writable'] = is_writable(__DIR__ . '/uploads') ? 'OK' : 'FAIL - chmod 755 public/uploads';
$checks['tools/ exists'] = is_dir(__DIR__ . '/../tools') ? 'OK '.count(glob(__DIR__.'/../tools/*', GLOB_ONLYDIR)).' tools' : 'FAIL';

// Mod rewrite (approx)
$checks['mod_rewrite'] = function_exists('apache_get_modules') ? (in_array('mod_rewrite', apache_get_modules()) ? 'OK' : 'FAIL') : 'UNKNOWN (check manually)';

// Config load
try {
    require __DIR__ . '/../app/config/config.php';
    $checks['config.php load'] = 'OK';
} catch (Throwable $e) {
    $checks['config.php load'] = 'FAIL: '.$e->getMessage();
}

// DB test
try {
    $db = Database::getInstance();
    $pdo = $db->getPdo();
    if ($pdo) {
        $driver = $pdo->getAttribute(PDO::ATTR_DRIVER_NAME);
        $checks['DB connection'] = "OK driver=$driver fallback=".($db->isFallback()?'yes':'no');
        $cnt = $db->fetchOne("SELECT COUNT(*) as c FROM tools");
        $checks['Tools in DB'] = $cnt ? 'OK '.$cnt['c'] : 'FAIL 0 - import schema.sql or check tools.json';
    } else {
        $checks['DB connection'] = 'FAIL - pdo null, will use filesystem fallback';
        $checks['Tools in DB'] = 'Fallback to filesystem: '.count(glob(__DIR__.'/../tools/*', GLOB_ONLYDIR));
    }
} catch (Throwable $e) {
    $checks['DB connection'] = 'FAIL: '.$e->getMessage();
}

foreach ($checks as $k=>$v) {
    $class = strpos($v,'FAIL')!==false ? 'fail' : 'ok';
    echo "<div><strong>$k:</strong> <span class='$class'>$v</span></div>";
}

echo "<hr><h2>Next Steps if any FAIL:</h2>";
echo "<ol>";
echo "<li>Copy .env.example to .env and set DB_HOST, DB_NAME, DB_USER, DB_PASS</li>";
echo "<li>cPanel > MySQL Databases > Create DB + User > Add user to DB</li>";
echo "<li>phpMyAdmin > Import database/schema.sql</li>";
echo "<li>chmod 755 storage/ storage/logs storage/cache storage/uploads_tmp public/uploads</li>";
echo "<li>If still 500, set APP_DEBUG=true in .env to see detailed error</li>";
echo "<li>Check cPanel > Errors > Error Log</li>";
echo "<li>Ensure DocumentRoot is /public or use root .htaccess forwarding</li>";
echo "</ol>";

echo "<p><a href='/'>Go Home</a> | <a href='/tools/age-calculator/'>Test Tool</a></p>";
echo "<p style='color:#888'>Delete this file after setup: public/install.php</p>";
