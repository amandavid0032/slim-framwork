<?php

namespace App\Middleware;

class CustomMiddleware
{
    public function __invoke($request, $response, $next)
    {
        $response = $next($request, $response);

        return $response;
    }
}
