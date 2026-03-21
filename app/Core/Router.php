<?php

class Router {
    private array $routes = [];

    public function get(string $path, string $action): void {
        $this->routes['GET'][$path] = $action;
    }

    public function post(string $path, string $action): void {
        $this->routes['POST'][$path] = $action;
    }

    public function dispatch(): void {
        $method = $_SERVER['REQUEST_METHOD'];
        $url    = trim($_GET['url'] ?? '', '/');

        // Try exact match first, then pattern match with {id}
        $action = $this->routes[$method][$url] ?? null;

        if (!$action) {
            foreach ($this->routes[$method] ?? [] as $route => $act) {
                $pattern = preg_replace('/\{[^}]+\}/', '([^/]+)', $route);
                if (preg_match('#^' . $pattern . '$#', $url, $matches)) {
                    array_shift($matches);
                    $_GET['id'] = $matches[0] ?? null;
                    $action = $act;
                    break;
                }
            }
        }

        if (!$action) {
            require APP . '/Views/errors/404.php';
            return;
        }

        [$controllerName, $methodName] = explode('@', $action);
        $controller = new $controllerName();
        $controller->$methodName();
    }
}
