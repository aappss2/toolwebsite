<?php
class RazorpayGateway implements PaymentGatewayInterface {
    private $keyId;
    private $keySecret;
    private $webhookSecret;

    public function __construct() {
        $this->keyId = RAZORPAY_KEY_ID;
        $this->keySecret = RAZORPAY_KEY_SECRET;
        $this->webhookSecret = RAZORPAY_WEBHOOK_SECRET;
    }

    public function createCustomer(array $user): array {
        // Razorpay customer creation via API
        $data = [
            'name' => $user['name'],
            'email' => $user['email'],
            'contact' => $user['phone'] ?? '',
            'fail_existing' => 0
        ];
        return $this->apiRequest('POST', '/customers', $data);
    }

    public function createOrder(int $amount, string $currency, string $receipt, array $notes = []): array {
        // Amount in smallest currency unit (paise for INR)
        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'receipt' => $receipt,
            'notes' => $notes
        ];
        return $this->apiRequest('POST', '/orders', $data);
    }

    public function createSubscription(string $planId, string $customerId, array $notes = []): array {
        $data = [
            'plan_id' => $planId,
            'customer_id' => $customerId,
            'total_count' => 12, // 12 months for yearly, or 1 for monthly - handled via plan
            'notes' => $notes
        ];
        return $this->apiRequest('POST', '/subscriptions', $data);
    }

    public function verifyPayment(string $paymentId, string $orderId, string $signature): bool {
        $expected = hash_hmac('sha256', $orderId . '|' . $paymentId, $this->keySecret);
        return hash_equals($expected, $signature);
    }

    public function verifyWebhookSignature(string $payload, string $signature, string $secret): bool {
        $expected = hash_hmac('sha256', $payload, $secret);
        return hash_equals($expected, $signature);
    }

    public function cancelSubscription(string $subscriptionId): bool {
        $result = $this->apiRequest('POST', "/subscriptions/$subscriptionId/cancel", ['cancel_at_cycle_end' => 0]);
        return isset($result['status']) && $result['status'] === 'cancelled';
    }

    public function getSubscription(string $subscriptionId): array {
        return $this->apiRequest('GET', "/subscriptions/$subscriptionId");
    }

    public function getPayment(string $paymentId): array {
        return $this->apiRequest('GET', "/payments/$paymentId");
    }

    private function apiRequest(string $method, string $endpoint, array $data = []): array {
        // If placeholder keys, return mock for development
        if (strpos($this->keyId, 'placeholder') !== false) {
            return $this->mockResponse($method, $endpoint, $data);
        }

        $url = 'https://api.razorpay.com/v1' . $endpoint;
        $ch = curl_init($url);
        
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_USERPWD, $this->keyId . ':' . $this->keySecret);
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        
        if ($method === 'POST') {
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        }

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode >= 200 && $httpCode < 300) {
            return json_decode($response, true) ?: [];
        } else {
            error_log("Razorpay API error $httpCode: $response");
            throw new Exception("Payment gateway error: $httpCode");
        }
    }

    private function mockResponse(string $method, string $endpoint, array $data): array {
        // Mock responses for development without real keys
        if (strpos($endpoint, '/orders') !== false) {
            return [
                'id' => 'order_mock_' . bin2hex(random_bytes(8)),
                'entity' => 'order',
                'amount' => $data['amount'] ?? 0,
                'currency' => $data['currency'] ?? 'INR',
                'receipt' => $data['receipt'] ?? '',
                'status' => 'created'
            ];
        }
        if (strpos($endpoint, '/customers') !== false) {
            return [
                'id' => 'cust_mock_' . bin2hex(random_bytes(8)),
                'entity' => 'customer',
                'name' => $data['name'] ?? '',
                'email' => $data['email'] ?? ''
            ];
        }
        if (strpos($endpoint, '/subscriptions') !== false) {
            return [
                'id' => 'sub_mock_' . bin2hex(random_bytes(8)),
                'entity' => 'subscription',
                'status' => 'created',
                'plan_id' => $data['plan_id'] ?? ''
            ];
        }
        return ['id' => 'mock_' . bin2hex(random_bytes(8)), 'status' => 'created'];
    }
}
