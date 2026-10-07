<?php
class WebhookHandler {
    public static function handleRazorpay(array $payload, string $signature): bool {
        $secret = RAZORPAY_WEBHOOK_SECRET;
        $gateway = new RazorpayGateway();
        
        $rawPayload = json_encode($payload);
        if (!$gateway->verifyWebhookSignature($rawPayload, $signature, $secret)) {
            error_log("Webhook signature verification failed");
            return false;
        }

        $db = Database::getInstance();

        // Idempotency check
        $eventId = $payload['event'] ?? '' . '_' . ($payload['payload']['payment']['entity']['id'] ?? uniqid());
        $existing = $db->fetchOne("SELECT id FROM payment_events WHERE gateway_event_id = ?", [$eventId]);
        if ($existing) {
            // Already processed
            return true;
        }

        $eventType = $payload['event'] ?? '';

        try {
            $db->beginTransaction();

            // Log event
            $db->query(
                "INSERT INTO payment_events (gateway, gateway_event_id, event_type, payload, created_at) VALUES (?, ?, ?, ?, NOW())",
                ['razorpay', $eventId, $eventType, $rawPayload]
            );

            switch ($eventType) {
                case 'payment.captured':
                case 'payment.authorized':
                    $payment = $payload['payload']['payment']['entity'] ?? [];
                    $paymentId = $payment['id'] ?? '';
                    $orderId = $payment['order_id'] ?? '';
                    $notes = $payment['notes'] ?? [];
                    $userId = $notes['user_id'] ?? null;
                    $planId = $notes['plan_id'] ?? null;

                    if ($userId && $planId) {
                        // Activate if not already active
                        $db->query(
                            "INSERT INTO payments (user_id, plan_id, gateway, gateway_payment_id, amount, currency, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, NOW()) ON DUPLICATE KEY UPDATE status = VALUES(status)",
                            [$userId, $planId, 'razorpay', $paymentId, ($payment['amount'] ?? 0)/100, $payment['currency'] ?? 'INR', 'success']
                        );
                    }
                    break;

                case 'subscription.activated':
                    $sub = $payload['payload']['subscription']['entity'] ?? [];
                    $subId = $sub['id'] ?? '';
                    $notes = $sub['notes'] ?? [];
                    $userId = $notes['user_id'] ?? null;
                    $planId = $notes['plan_id'] ?? null;
                    if ($userId && $planId) {
                        SubscriptionService::activateSubscription((int)$userId, (int)$planId, 'razorpay', $subId);
                    }
                    break;

                case 'subscription.cancelled':
                    $sub = $payload['payload']['subscription']['entity'] ?? [];
                    $subId = $sub['id'] ?? '';
                    $existingSub = $db->fetchOne("SELECT * FROM subscriptions WHERE gateway_subscription_id = ?", [$subId]);
                    if ($existingSub) {
                        $db->query("UPDATE subscriptions SET status = ?, cancelled_at = NOW() WHERE id = ?", [SUB_STATUS_CANCELLED, $existingSub['id']]);
                        $db->query("UPDATE users SET subscription_status = ? WHERE id = ?", [SUB_STATUS_CANCELLED, $existingSub['user_id']]);
                    }
                    break;

                case 'subscription.completed':
                case 'subscription.charged':
                    // Renewal
                    $sub = $payload['payload']['subscription']['entity'] ?? [];
                    $subId = $sub['id'] ?? '';
                    $existingSub = $db->fetchOne("SELECT * FROM subscriptions WHERE gateway_subscription_id = ?", [$subId]);
                    if ($existingSub) {
                        $plan = SubscriptionService::getPlanById($existingSub['plan_id']);
                        $interval = $plan['billing_interval'] ?? 'month';
                        $count = (int)($plan['billing_interval_count'] ?? 1);
                        $newEnd = $interval === 'year' ? date('Y-m-d H:i:s', strtotime("+$count year")) : date('Y-m-d H:i:s', strtotime("+$count month"));
                        $db->query("UPDATE subscriptions SET current_period_start = NOW(), current_period_end = ?, updated_at = NOW() WHERE id = ?", [$newEnd, $existingSub['id']]);
                        $db->query("UPDATE users SET subscription_expires_at = ?, subscription_status = ? WHERE id = ?", [$newEnd, SUB_STATUS_ACTIVE, $existingSub['user_id']]);
                    }
                    break;

                case 'payment.failed':
                    $payment = $payload['payload']['payment']['entity'] ?? [];
                    $notes = $payment['notes'] ?? [];
                    $userId = $notes['user_id'] ?? null;
                    if ($userId) {
                        $db->query(
                            "INSERT INTO billing_logs (user_id, action, details, created_at) VALUES (?, ?, ?, NOW())",
                            [$userId, 'payment_failed', json_encode($payment)]
                        );
                    }
                    break;
            }

            $db->commit();
            return true;
        } catch (Exception $e) {
            $db->rollBack();
            error_log("WebhookHandler error: " . $e->getMessage());
            return false;
        }
    }
}
