<?php

define('ROOT', dirname(__DIR__));
define('APP',  ROOT . '/app');

session_start();

require ROOT . '/app/Core/helpers.php';
require ROOT . '/app/Core/ApiClient.php';
require ROOT . '/app/Core/View.php';
require ROOT . '/app/Core/Controller.php';
require ROOT . '/app/Core/Router.php';

set_exception_handler(function (\Throwable $e) {
    if (!isset($_SESSION)) { session_start(); }
    http_response_code(500);
    $exceptionMessage = $e->getMessage();
    require APP . '/Views/errors/500.php';
    exit;
});

// Autoload controllers
foreach (glob(APP . '/Controllers/*.php') as $file) {
    require $file;
}

$config = require ROOT . '/config/app.php';
define('API_BASE', $config['api_base']);
define('APP_NAME', $config['app_name']);
define('BASE_URL',  $config['base_url']);

$router = new Router();
require ROOT . '/routes/web.php';
$router->dispatch();
