<?php

namespace Middleware;

class AuthMiddleware
{
    public function handler($request, $response, $next)
    {
        echo "auth middleware";

        return $next($request, $response);
    }
}
