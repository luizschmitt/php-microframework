<?php

namespace PHPExpress;

class App
{
    protected $settings;

    public function __construct($settings = null)
    {
        $this->settings = $settings;
        $this->router   = new \PHPExpress\Router\Router();

        new \PHPExpress\Http\Cors([
            'origin'      => '*',
            'credentials' => true,
            'max-age'     => 86400,
            'headers'     => ['Content-Type', 'Accept', 'Origin', 'Authorization'],
            'methods'     => ['GET', 'PUT', 'DELETE', 'POST', 'PATCH', 'OPTIONS']
        ]);
    }

    public function __call($method, $arguments)
    {
        if (in_array(strtoupper($method), ['GET', 'POST', 'PUT', 'DELETE', 'PATCH', 'OPTIONS'])) {
            $method   = strtoupper($method);
            $route    = !empty($arguments[0]) ? $arguments[0] : false;
            $callback = !empty($arguments[1]) ? $arguments[1] : false;

            return $this->router->map($method, $route, $callback);
        }
    }

    public function group($name, $callback)
    {
        return $this->router->group($name, $callback);
    }

    public function use()
    {
    }

    public function add()
    {
    }

    public function set()
    {
    }

    public function run()
    {
        $this->router->dispatch();
    }
}
