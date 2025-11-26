<?php
class Router {
    private array $routes = [];

    public function get(string $path, $handler): void {
        $this->add('GET', $path, $handler);
    }

    public function post(string $path, $handler): void {
        $this->add('POST', $path, $handler);
    }

    private function add(string $method, string $path, $handler): void {
        $this->routes[] = [$method, $this->compile($path), $handler];
    }

    private function compile(string $path): string {
        $regex = preg_replace('#\{[a-zA-Z_][a-zA-Z0-9_]*\}#', '([\\w-]+)', $path);
        return '#^' . rtrim($regex, '/') . '/?$#';
    }

    public function dispatch(Request $request): void {
        $uri = $request->uri();
        $method = $request->method();
        foreach ($this->routes as [$m, $pattern, $handler]) {
            if ($m === $method && preg_match($pattern, $uri, $matches)) {
                array_shift($matches);
                $this->runHandler($handler, $matches);
                return;
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }

    private function runHandler($handler, array $params): void {
        if (is_callable($handler)) {
            call_user_func_array($handler, $params);
            return;
        }
        if (is_string($handler) && strpos($handler, '@') !== false) {
            [$controller, $method] = explode('@', $handler);
            $controllerFile = __DIR__ . '/../controllers/' . $controller . '.php';
            if (!file_exists($controllerFile)) {
                // thử folder admin
                $adminFile = __DIR__ . '/../controllers/admin/' . $controller . '.php';
                if (file_exists($adminFile)) {
                    $controllerFile = $adminFile;
                } else {
                    http_response_code(500);
                    echo 'Controller missing: ' . htmlspecialchars($controllerFile);
                    return;
                }
            }
            require_once $controllerFile;
            $className = basename(str_replace('\\', '/', $controller));
            if (!class_exists($className)) {
                http_response_code(500);
                echo 'Class not found';
                return;
            }
            $instance = new $className();
            call_user_func_array([$instance, $method], $params);
            return;
        }
        http_response_code(500);
        echo 'Bad handler';
    }
}
