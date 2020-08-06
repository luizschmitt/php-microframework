<?php

namespace PHPExpress\Http;

class Cors
{
    public function __construct($settings, $json = false)
    {
        $origin      = !empty($settings["origin"]) ? $settings["origin"] : $_SERVER["HTTP_ORIGIN"];
        $credentials = $settings["credentials"];
        $maxAge      = $settings["max-age"];
        $headers     = implode(",", $settings["headers"]);
        $methods     = implode(",", $settings["methods"]);

        if ($json) {
            header("Content-type: application/json;");
        }

        header("Access-Control-Allow-Origin: $origin");

        if (isset($_SERVER["HTTP_ORIGIN"])) {
            header("Access-Control-Allow-Origin: {$origin}");
            header("Access-Control-Allow-Credentials: {$credentials}");
            header("Access-Control-Max-Age: {$maxAge}");
        }

        if ($_SERVER["REQUEST_METHOD"] == "OPTIONS") {
            if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_METHOD"])) {
                header("Access-Control-Allow-Methods: {$methods}");
            }

            if (isset($_SERVER["HTTP_ACCESS_CONTROL_REQUEST_HEADERS"])) {
                header("Access-Control-Allow-Headers: {$headers}");
            }

            exit(0);
        }
    }
}
