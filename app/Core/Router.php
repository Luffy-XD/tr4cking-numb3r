<?php

namespace App\Core;

use App\Core\Middleware\AuthMiddleware;
use App\Core\Middleware\RoleMiddleware;

class Router
{
    private array $routes = [];

    public function get(string $path, $action, array $middleware = []): void
    {
        $this->addRoute('GET', $path, $action, $middleware);
    }

    public function post(string $path, $action, array $middleware = []): void
    {
        $this->addRoute('POST', $path, $action, $middleware);
    }

    public function put(string $path, $action, array $middleware = []): void
    {
        $this->addRoute('PUT', $path, $action, $middleware);
    }

    public function delete(string $path, $action, array $middleware = []): void
    {
        $this->addRoute('DELETE', $path, $action, $middleware);
    }

    private function addRoute(string $method, string $path, $action, array $middleware): void
    {
        $normalizedPath = $path === '/' ? '/' : '/' . trim($path, '/');
        $pattern = '#^' . preg_replace('#\{([a-zA-Z0-9_]+)\}#', '(?P<$1>[^/]+)', $normalizedPath) . '$#';

        $this->routes[$method][] = [
            'pattern' => $pattern,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request)
    {
        $method = $request->method();
        $uri = $request->path();
        if ($uri !== '/') {
            $uri = '/' . trim($uri, '/');
        }

        $routes = $this->routes[$method] ?? [];
        foreach ($routes as $route) {
            if (preg_match($route['pattern'], $uri, $matches)) {
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                $this->handleMiddleware($route['middleware']);
                return $this->callAction($route['action'], $request, $params);
            }
        }

        http_response_code(404);
        echo 'Halaman tidak ditemukan';
        return null;
    }

    private function handleMiddleware(array $middlewares): void
    {
        foreach ($middlewares as $middleware) {
            if ($middleware === 'auth') {
                (new AuthMiddleware())->handle();
            } elseif (str_starts_with($middleware, 'role:')) {
                $roles = array_map('trim', explode(',', substr($middleware, 5)));
                (new RoleMiddleware($roles))->handle();
            }
        }
    }

    private function callAction($action, Request $request, array $params)
    {
        if (is_callable($action)) {
            return $action($request, $params);
        }

        if (is_string($action) && str_contains($action, '@')) {
            [$controller, $method] = explode('@', $action);
            $controllerClass = 'App\\Controllers\\' . $controller;
            if (class_exists($controllerClass)) {
                $instance = new $controllerClass($request);
                return call_user_func_array([$instance, $method], $params);
            }
        }

        throw new \RuntimeException('Route action tidak valid');
    }
}
