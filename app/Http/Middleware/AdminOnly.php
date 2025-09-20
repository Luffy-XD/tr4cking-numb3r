<?php

namespace App\Http\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class AdminOnly implements MiddlewareInterface
{
    public function handle(Request $request): ?Response
    {
        if (!Auth::isAdmin()) {
            return Response::make(view('errors.403'), 403);
        }
        return null;
    }
}
