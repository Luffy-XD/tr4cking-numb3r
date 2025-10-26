<?php

namespace App\Core;

class Request
{
    private array $query;
    private array $body;
    private array $files;
    private string $method;
    private string $path;

    public function __construct()
    {
        $this->query = $_GET;
        $this->body = $_POST;
        $this->files = $_FILES;
        $this->method = strtoupper($_SERVER['REQUEST_METHOD'] ?? 'GET');
        $uri = strtok($_SERVER['REQUEST_URI'] ?? '/', '?');
        $this->path = '/' . trim($uri, '/');
        if ($this->path === '//') {
            $this->path = '/';
        }
    }

    public function method(): string
    {
        return $this->method;
    }

    public function path(): string
    {
        return $this->path === '' ? '/' : $this->path;
    }

    public function input(string $key, $default = null)
    {
        return $this->body[$key] ?? $this->query[$key] ?? $default;
    }

    public function all(): array
    {
        return array_merge($this->query, $this->body);
    }

    public function query(string $key, $default = null)
    {
        return $this->query[$key] ?? $default;
    }

    public function file(string $key)
    {
        return $this->files[$key] ?? null;
    }
}
