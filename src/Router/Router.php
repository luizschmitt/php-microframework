<?php

namespace PHPExpress\Router;

use Exception;
use \PHPExpress\Http\Request;
use \PHPExpress\Http\Response;

class Router
{
    protected $routes = [];
    protected $middleware = [];

    public function __construct()
    {
    }

    public function __call($method, $arguments)
    {
        if (in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'])) {
            $method   = strtoupper($method);
            $route    = !empty($arguments[0]) ? $arguments[0] : false;
            $callback = !empty($arguments[1]) ? $arguments[1] : false;

            return $this->map($method, $route, $callback);
        }
    }

    public function group($group, $callback)
    {
        if (is_array($group)) {
            $middleware = $group['middleware'];
            $group = $group['prefix'];
        }
        $newRoute = new Router();
        $callback = \Closure::bind($callback, $newRoute);
        call_user_func($callback);

        foreach ($newRoute->routes as $route => $route_callback) {
            unset($newRoute->routes[$route]);
            $parts = explode('@', $route);
            $this->routes[$group.$parts[0].'@'.$parts[1]] = $route_callback;
            $this->middleware[$group.$parts[0].'@'.$parts[1]] = $middleware;
        }
    }

    public function map($method, $route, $callback)
    {
        $index = $route . '@' . $method;

        if (isset($this->routes[$index])) {
            throw new Exception("Conflito de rota: $route em $method já existe!");
        }

        $this->routes[$index] = $callback;
    }

    public function dispatch()
    {
        if (preg_match('/(.+)\.php(.+)?/', $_SERVER['PHP_SELF'], $matched)) {
            $root       = $matched[1];

            if (empty($matched[2])) {
                $requestUri = '/';
            } else {
                $requestUri = $matched[2];
            }

            foreach ($this->routes as $request => $callback) {
                $parts  = explode('@', $request);
                $route  = $parts[0];
                $method = $parts[1];

                if ($_SERVER['REQUEST_METHOD'] === $method) {
                    if ($requestUri === $route) {
                        return call_user_func_array($callback, [new Request(), new Response()]);
                        exit(0);
                    }
                }
            }
        }

        http_response_code(404);
    }
}
