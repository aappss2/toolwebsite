<?php
require_once __DIR__ . '/../../app/config/config.php';
Security::setSecurityHeaders();
Auth::initSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

$limiter = new RateLimiter();
$ip = Security::getClientIp();
$limiter->check("register:$ip", 3, 900);

Csrf::checkRequest();

try {
    $user = UserService::register(
        $_POST['name'] ?? '',
        $_POST['email'] ?? '',
        $_POST['password'] ?? ''
    );
    Auth::login($user);
    
    if (Auth::isApiRequest()) {
        echo json_encode(['success' => true, 'user' => ['id' => $user['id'], 'name' => $user['name']]]);
    } else {
        $redirect = $_GET['redirect'] ?? '/dashboard';
        redirect($redirect);
    }
} catch (Exception $e) {
    if (Auth::isApiRequest()) {
        http_response_code(400);
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        redirect('/register?error=' . urlencode($e->getMessage()));
    }
}
