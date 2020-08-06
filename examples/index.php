<?php

require __DIR__ . '/../vendor/autoload.php';

new \PHPExpress\Http\Cors([
    'origin'      => '*',
    'credentials' => true,
    'max-age'     => 86400,
    'headers'     => ['Content-Type', 'Accept', 'Origin', 'Authorization'],
    'methods'     => ['GET', 'PUT', 'DELETE', 'POST', 'PATCH', 'OPTIONS']
]);

$request = new \PHPExpress\Http\Request();
$response = new \PHPExpress\Http\Response();

$headers = $request->query;
return $response->json([
    'ip' => $request->ip,
    'host' => $request->hostname,
    'method' => $request->method,
    'path' => $request->path
]);
die;

$app = new App($config);

$app->use();

$app->get('/', function () {
    echo 'Hello World';
});

$app->group('/admin', function () {
    $this->get('/dashboard', function (Request $request, Response $response, $params) {
        echo "Página DASHBOARD do grupo ADMIN";
    });
})->use(function (Request $request, Response $response, $next) {
    echo "EXECUTANDO MIDDLEWARE";

    return $next($request, $response);
});

$app->run();

/*

$request->params;
$request->params->id; - quando houver parametros na url;
$request->baseUrl;
$request->body;
$request->hostname;
$request->ip;
$request->method;
$request->path;
$request->protocol;
$request->query;
$request->route; - tras a info da rota atual ex:
{
    path: '/admin/dasboard',
    stack: [
        handle: fn,
        name: '',
        params: undefined,
        keys: [],
        regexp://,
        method:'',
    ],
    methods: {
        get: true
    }
}
$request->secure() - verifica se é https
$request->xhr; - request’s X-Requested-With header field is “XMLHttpRequest”
$request->accepts($type) - checa se existe um content type
$request->get($header) - pega algo no header


$response->status($code);
$response->send($content);
$response->json($array);


*/
