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

$limiter = new RateLimiter();
$limiter->check("billing:" . Auth::id(), 5, 60);

$input = json_decode(file_get_contents('php://input'), true) ?: $_POST;
$planId = $input['plan_id'] ?? 0;

try {
    $db = Database::getInstance();
    $plan = $db->fetchOne("SELECT * FROM subscription_plans WHERE id = ? AND status = 'active'", [$planId]);
    if (!$plan) {
        throw new Exception("Plan not found");
    }

    if ($plan['amount'] == 0) {
        throw new Exception("Cannot create order for free plan");
    }

    $user = Auth::user();
    $gateway = new RazorpayGateway();

    // Create customer if needed
    $customer = $gateway->createCustomer($user);

    // Amount in paise for INR
    $amount = (int)($plan['amount'] * 100);
    $receipt = 'order_' . $user['id'] . '_' . time();
    $notes = [
        'user_id' => (string)$user['id'],
        'plan_id' => (string)$plan['id'],
        'email' => $user['email']
    ];

    $order = $gateway->createOrder($amount, $plan['currency'] ?? 'INR', $receipt, $notes);

    // Log order attempt
    $db->query(
        "INSERT INTO billing_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())",
        [$user['id'], 'order_created', json_encode(['plan_id' => $plan['id'], 'order_id' => $order['id'], 'amount' => $plan['amount']])]
    );

    echo json_encode([
        'success' => true,
        'order_id' => $order['id'],
        'amount' => $order['amount'],
        'currency' => $order['currency'],
        'key_id' => RAZORPAY_KEY_ID,
        'plan_name' => $plan['name'],
        'customer_id' => $customer['id'] ?? null
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['error' => $e->getMessage()]);
}
