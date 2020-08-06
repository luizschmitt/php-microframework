<?php

namespace PHPExpress\Http;

class Response
{
    public function status($code)
    {
        http_response_code($code);
        return $this;
    }

    public function send($content)
    {
        echo $content;
    }

    public function json($array)
    {
        header("Content-Type: application/json");
        echo json_encode($array);
    }
}
