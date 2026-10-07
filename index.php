<?php
// Root index.php - for cPanel where document root is project root
// Forwards to public/index.php
// If cPanel document root is set to /public, this file is not used but kept for compatibility

// Security: deny direct access to sensitive files
$uri = $_SERVER['REQUEST_URI'] ?? '';
if (preg_match('#^/(app|database|storage|legacy|docs|\.env)#', $uri)) {
    http_response_code(403);
    die('Forbidden');
}

// Load public front controller
require __DIR__ . '/public/index.php';
