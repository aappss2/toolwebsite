<?php
// Stub for future Stripe integration - same interface

class StripeGateway implements PaymentGatewayInterface {
    public function createCustomer(array $user): array {
        throw new Exception("Stripe gateway not yet implemented");
    }

    public function createOrder(int $amount, string $currency, string $receipt, array $notes = []): array {
        throw new Exception("Stripe gateway not yet implemented - use Razorpay");
    }

    public function createSubscription(string $planId, string $customerId, array $notes = []): array {
        throw new Exception("Stripe gateway not yet implemented");
    }

    public function verifyPayment(string $paymentId, string $orderId, string $signature): bool {
        // Stripe uses different verification
        return false;
    }

    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool {
        // Implement Stripe signature verification when needed
        return false;
    }

    public function cancelSubscription(string $subscriptionId): bool {
        throw new Exception("Stripe not implemented");
    }

    public function getSubscription(string $subscriptionId): array {
        throw new Exception("Stripe not implemented");
    }

    public function getPayment(string $paymentId): array {
        throw new Exception("Stripe not implemented");
    }
}
