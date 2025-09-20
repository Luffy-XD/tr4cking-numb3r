<?php

namespace App\Core;

use App\Http\Middleware\MiddlewareInterface;
use RuntimeException;

class Router
{
    protected array $routes = [];

    public function add(string $method, string $uri, callable|array $action, array $middleware = []): void
    {
        $method = strtoupper($method);
        $this->routes[$method][] = [
            'uri' => $uri,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function get(string $uri, callable|array $action, array $middleware = []): void
    {
        $this->add('GET', $uri, $action, $middleware);
    }

    public function post(string $uri, callable|array $action, array $middleware = []): void
    {
        $this->add('POST', $uri, $action, $middleware);
    }

    public function put(string $uri, callable|array $action, array $middleware = []): void
    {
        $this->add('PUT', $uri, $action, $middleware);
    }

    public function delete(string $uri, callable|array $action, array $middleware = []): void
    {
        $this->add('DELETE', $uri, $action, $middleware);
    }

    public function dispatch(Request $request): Response
    {
        $method = $request->method();
        $path = $request->path();

        foreach ($this->routes[$method] ?? [] as $route) {
            $pattern = '#^' . preg_replace('#\{([^/]+)\}#', '(?P<$1>[^/]+)', $route['uri']) . '$#';
            if (preg_match($pattern, $path, $matches)) {
                $params = array_values(array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY));
                return $this->runRoute($route, $request, $params);
            }
        }

        return Response::make('Not Found', 404);
    }

    protected function runRoute(array $route, Request $request, array $params): Response
    {
        foreach ($route['middleware'] as $middlewareClass) {
            /** @var MiddlewareInterface $middleware */
            $middleware = new $middlewareClass();
            $result = $middleware->handle($request);
            if ($result instanceof Response) {
                return $result;
            }
        }

        $action = $route['action'];
        if (is_callable($action)) {
            $response = call_user_func_array($action, array_merge([$request], $params));
        } elseif (is_array($action) && count($action) === 2) {
            [$controllerClass, $method] = $action;
            $controller = new $controllerClass();
            $response = call_user_func_array([$controller, $method], array_merge([$request], $params));
        } else {
            throw new RuntimeException('Invalid route action.');
        }

        return $response instanceof Response ? $response : Response::make($response);
    }
}
