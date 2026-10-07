<?php
interface PaymentGatewayInterface {
    public function createCustomer(array $user): array;
    public function createOrder(int $amount, string $currency, string $receipt, array $notes = []): array;
    public function createSubscription(string $planId, string $customerId, array $notes = []): array;
    public function verifyPayment(string $paymentId, string $orderId, string $signature): bool;
    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool;
    public function cancelSubscription(string $subscriptionId): bool;
    public function getSubscription(string $subscriptionId): array;
    public function getPayment(string $paymentId): array;
}
