<?php

namespace App\Http\Middleware;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

class Authenticate implements MiddlewareInterface
{
    public function handle(Request $request): ?Response
    {
        if (!Auth::check() && $request->path() !== '/login') {
            return Response::make('', 302, ['Location' => '/login']);
        }
        return null;
    }
}
