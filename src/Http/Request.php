<?php

namespace PHPExpress\Http;

class Request
{
    public $params;
    public $baseUrl;
    public $body;
    public $hostname;
    public $ip;
    public $method;
    public $path;
    public $protocol;
    public $query;
    public $route;

    public function __construct()
    {
        $this->getBody();
        $this->getQuery();

        $this->hostname = $this->getServer('http_host');
        $this->ip = $this->getServer('remote_addr');
        $this->method = strtoupper($this->getServer('request_method'));
        $this->path = explode('?', $this->getServer('request_uri'))[0];
        $this->protocol = $this->secure() ? 'https://' : 'http://';
    }

    public function get($name = null)
    {
        $headers = null;

        if (!is_null($name)) {
            foreach (getallheaders() as $key => $value) {
                if (strtolower($key) === strtolower($name)) {
                    $headers = $value;
                }
            }
        } else {
            $headers = getallheaders();
        }

        return $headers;
    }

    public function secure()
    {
        return ($this->getServer('https')) ? true : false;
    }

    public function xhr()
    {
        return ($this->get('x-requested-with') == 'XMLHttpRequest') ? true : false;
    }

    public function accepts($type)
    {
    }

    protected function getServer($name)
    {
        foreach ($_SERVER as $key => $value) {
            if (strtolower($key) === strtolower($name)) {
                return $value;
            }
        }

        return null;
    }

    protected function getBody()
    {
        $data = json_decode(file_get_contents("php://input"), true);

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = $value;
            } else {
                $data[$key] = addslashes(trim($value));
            }
        }

        $this->body = $data;
    }

    protected function getQuery($field = null)
    {
        $request = $_REQUEST;

        foreach ($request as $key => $value) {
            $request[$key] = addslashes(trim($value));
        }

        $this->query = isset($request[$field]) ? $request[$field] : $request;
    }
}
