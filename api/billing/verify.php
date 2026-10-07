<?php
require_once __DIR__ . '/../../app/config/config.php';
Security::setSecurityHeaders();
header('Content-Type: application/json');
Auth::initSession();
Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

Csrf::checkRequest();

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$paymentId = $input['razorpay_payment_id'] ?? '';
$orderId = $input['razorpay_order_id'] ?? '';
$signature = $input['razorpay_signature'] ?? '';
$planId = $input['plan_id'] ?? 0;

if (!$paymentId || !$orderId || !$signature || !$planId) {
    http_response_code(400);
    die(json_encode(['error' => 'Missing payment details']));
}

try {
    $gateway = new RazorpayGateway();
    
    // Verify signature server-side - NEVER trust client
    if (!$gateway->verifyPayment($paymentId, $orderId, $signature)) {
        throw new Exception("Payment signature verification failed");
    }

    // Verify payment status via API
    $payment = $gateway->getPayment($paymentId);
    if (($payment['status'] ?? '') !== 'captured' && ($payment['status'] ?? '') !== 'authorized' && strpos($paymentId, 'mock') === false) {
        // For mock payments in dev, allow
        if (strpos(RAZORPAY_KEY_ID, 'placeholder') === false) {
            throw new Exception("Payment not captured");
        }
    }

    $user = Auth::user();
    
    // Activate subscription
    $success = SubscriptionService::activateSubscription(
        $user['id'],
        (int)$planId,
        'razorpay',
        $orderId, // Using order_id as subscription id for order-based flow, in real sub flow would be sub id
        $paymentId
    );

    if (!$success) {
        throw new Exception("Failed to activate subscription");
    }

    echo json_encode(['success' => true, 'message' => 'Subscription activated']);

} catch (Exception $e) {
    error_log("Payment verify error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
