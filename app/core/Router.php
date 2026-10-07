<?php
class Router {
    private $routes = [];

    public function get(string $pattern, callable $handler): void {
        $this->addRoute('GET', $pattern, $handler);
    }

    public function post(string $pattern, callable $handler): void {
        $this->addRoute('POST', $pattern, $handler);
    }

    public function any(string $pattern, callable $handler): void {
        $this->addRoute('GET', $pattern, $handler);
        $this->addRoute('POST', $pattern, $handler);
    }

    private function addRoute(string $method, string $pattern, callable $handler): void {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'handler' => $handler
        ];
    }

    public function dispatch(): void {
        $uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        $method = $_SERVER['REQUEST_METHOD'];
        
        // Remove trailing slash except for root
        if ($uri !== '/' && substr($uri, -1) === '/') {
            $uri = rtrim($uri, '/');
            if ($uri === '') $uri = '/';
        }

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method && $route['method'] !== 'ANY') continue;
            
            $pattern = $route['pattern'];
            // Convert {param} to regex
            $regex = preg_replace('/\{([a-zA-Z_][a-zA-Z0-9_]*)\}/', '(?P<$1>[^/]+)', $pattern);
            $regex = '#^' . $regex . '$#';
            
            if (preg_match($regex, $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                call_user_func($route['handler'], $params);
                return;
            }
        }

        // No route matched - 404
        http_response_code(404);
        include TEMPLATE_PATH . '/404.php';
    }

    public static function redirect(string $url, int $code = 302): void {
        http_response_code($code);
        header("Location: $url");
        exit;
    }
}
