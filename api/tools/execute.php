<?php
require_once __DIR__ . '/../../app/config/config.php';
Security::setSecurityHeaders();
header('Content-Type: application/json');
Auth::initSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

$limiter = new RateLimiter();
$ip = Security::getClientIp();
$limiter->check("tool_execute:$ip", 30, 60);

// Parse JSON or form
$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$toolSlug = $input['tool_slug'] ?? '';

if (!$toolSlug) {
    http_response_code(400);
    die(json_encode(['error' => 'Missing tool_slug']));
}

try {
    $db = Database::getInstance();
    $tool = $db->fetchOne("SELECT t.*, c.name as category_name FROM tools t LEFT JOIN categories c ON t.category_id = c.id WHERE t.slug = ? AND t.status = ?", [$toolSlug, TOOL_STATUS_ACTIVE]);
    if (!$tool) {
        http_response_code(404);
        die(json_encode(['error' => 'Tool not found']));
    }

    $user = Auth::user();
    $check = UsageService::canUseTool($user, $tool);
    
    if (!$check['allowed']) {
        http_response_code(402);
        die(json_encode([
            'error' => 'upgrade_required',
            'reason' => $check['reason'],
            'message' => $check['message'] ?? "You've used all free uses. Please upgrade.",
            'remaining' => $check['remaining']
        ]));
    }

    // Simulate tool processing - in real migrated tools, this would contain actual logic
    // For now, we just validate and return success
    // The frontend will handle actual client-side processing for many tools
    $result = $input['input'] ?? 'Processed successfully';
    
    // For calculators, we could add server-side validation
    // Here we just log and consume usage
    
    $success = true; // Assume success, real tools would have actual success/failure
    
    if ($success) {
        UsageService::consumeUsage($user, $tool, true);
    }

    // Get updated usage status
    $newUser = $user ? UserService::getById($user['id']) : null;
    $usageStatus = UsageService::getUsageStatus($newUser);

    echo json_encode([
        'success' => true,
        'result' => "Tool {$tool['name']} executed successfully. Input length: " . strlen($result),
        'tool' => $tool['slug'],
        'usage' => $usageStatus
    ]);

} catch (Exception $e) {
    error_log("Tool execute error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Execution failed: ' . $e->getMessage()]);
}
