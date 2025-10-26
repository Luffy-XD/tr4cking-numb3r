<?php

namespace App\Core;

class Application
{
    protected Router $router;

    public function __construct()
    {
        $this->router = new Router();
    }

    public function boot(): void
    {
        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        $router = $this->router;
        require base_path('routes/web.php');
        require base_path('routes/api.php');
    }

    public function router(): Router
    {
        return $this->router;
    }

    public function handle(Request $request): Response
    {
        return $this->router->dispatch($request);
    }
}
