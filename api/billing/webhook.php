<?php
require_once __DIR__ . '/../../app/config/config.php';
Security::setSecurityHeaders();
header('Content-Type: application/json');

// Webhook should not require auth, but must verify signature
$payload = file_get_contents('php://input');
$signature = $_SERVER['HTTP_X_RAZORPAY_SIGNATURE'] ?? '';

if (!$payload || !$signature) {
    http_response_code(400);
    die(json_encode(['error' => 'Missing payload or signature']));
}

try {
    $data = json_decode($payload, true);
    if (!$data) {
        throw new Exception("Invalid JSON payload");
    }

    $success = WebhookHandler::handleRazorpay($data, $signature);
    
    if ($success) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Webhook handling failed']);
    }
} catch (Exception $e) {
    error_log("Webhook error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
