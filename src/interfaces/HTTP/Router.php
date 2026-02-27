<?php

declare(strict_types=1);

namespace Interfaces\HTTP;

use Symfony\Component\HttpFoundation\Request;

/**
 * Simple Router Implementation
 */
class Router
{
    /** @var array<array{method: string, pattern: string, callback: callable|array|string}> */
    private array $routes = [];

    public function get(string $pattern, callable|array|string $callback): void
    {
        $this->addRoute('GET', $pattern, $callback);
    }

    public function post(string $pattern, callable|array|string $callback): void
    {
        $this->addRoute('POST', $pattern, $callback);
    }

    public function put(string $pattern, callable|array|string $callback): void
    {
        $this->addRoute('PUT', $pattern, $callback);
    }

    public function delete(string $pattern, callable|array|string $callback): void
    {
        $this->addRoute('DELETE', $pattern, $callback);
    }

    private function addRoute(string $method, string $pattern, callable|array|string $callback): void
    {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $pattern,
            'callback' => $callback,
        ];
    }

    public function match(Request $request): ?array
    {
        $path = $request->getPathInfo();
        $method = $request->getMethod();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $params = $this->matchPattern($route['pattern'], $path);

            if ($params !== null) {
                return [
                    'callback' => $route['callback'],
                    'params' => $params,
                ];
            }
        }

        return null;
    }

    private function matchPattern(string $pattern, string $path): ?array
    {
        // Convert route pattern to regex
        $pattern = str_replace('/', '\/', $pattern);
        $pattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^\/]+)', $pattern);
        $pattern = '#^' . $pattern . '$#';

        if (preg_match($pattern, $path, $matches)) {
            $params = [];
            foreach ($matches as $key => $value) {
                if (is_string($key)) {
                    $params[$key] = $value;
                }
            }
            return $params;
        }

        return null;
    }

    public function getRoutes(): array
    {
        return $this->routes;
    }
}
