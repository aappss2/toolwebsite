<?php
require_once __DIR__ . '/../../app/config/config.php';
Security::setSecurityHeaders();
Auth::initSession();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die(json_encode(['error' => 'Method not allowed']));
}

// Rate limiting
$limiter = new RateLimiter();
$ip = Security::getClientIp();
$email = $_POST['email'] ?? '';
$limiter->check("login:$ip", 5, 900);
$limiter->check("login:$email", 5, 900);

Csrf::checkRequest();

try {
    $user = UserService::login($_POST['email'] ?? '', $_POST['password'] ?? '');
    Auth::login($user);
    
    // Redirect handling
    if (!empty($_POST['redirect'])) {
        redirect($_POST['redirect']);
    }
    if (Auth::isApiRequest()) {
        echo json_encode(['success' => true, 'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email']]]);
    } else {
        redirect('/dashboard');
    }
} catch (Exception $e) {
    if (Auth::isApiRequest()) {
        http_response_code(401);
        echo json_encode(['error' => $e->getMessage()]);
    } else {
        redirect('/login?error=' . urlencode($e->getMessage()));
    }
}
