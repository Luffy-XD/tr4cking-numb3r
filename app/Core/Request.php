<?php

namespace App\Core;

class Request
{
    protected array $query;
    protected array $request;
    protected array $files;
    protected array $server;

    public function __construct(array $query, array $request, array $files, array $server)
    {
        $this->query = $query;
        $this->request = $request;
        $this->files = $files;
        $this->server = $server;
    }

    public static function capture(): self
    {
        return new self($_GET, $_POST, $_FILES, $_SERVER);
    }

    public function method(): string
    {
        return strtoupper($this->server['REQUEST_METHOD'] ?? 'GET');
    }

    public function path(): string
    {
        $uri = $this->server['REQUEST_URI'] ?? '/';
        $parsed = parse_url($uri, PHP_URL_PATH);
        return '/' . ltrim($parsed ?? '/', '/');
    }

    public function input(?string $key = null, $default = null)
    {
        if ($key === null) {
            return array_merge($this->query, $this->request);
        }
        return $this->request[$key] ?? $this->query[$key] ?? $default;
    }

    public function file(string $key): ?array
    {
        return $this->files[$key] ?? null;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->request);
    }

    public function has(string $key): bool
    {
        return isset($this->query[$key]) || isset($this->request[$key]);
    }
}
