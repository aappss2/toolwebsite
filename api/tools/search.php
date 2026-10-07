<?php
require_once __DIR__ . '/../../app/config/config.php';
Security::setSecurityHeaders();
header('Content-Type: application/json');

$q = $_GET['q'] ?? '';
$q = trim($q);

if (strlen($q) < 2) {
    echo json_encode(['tools' => []]);
    exit;
}

try {
    $tools = SearchService::search($q, 10);
    SearchService::logSearch($q, Auth::id());
    echo json_encode(['tools' => $tools]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Search failed', 'tools' => []]);
}
