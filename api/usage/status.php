<?php
require_once __DIR__ . '/../../app/config/config.php';
Security::setSecurityHeaders();
header('Content-Type: application/json');
Auth::initSession();

$user = Auth::user();
$status = UsageService::getUsageStatus($user);

echo json_encode($status);
