<?php
session_start();
// Este archivo se puede agregar al principio de tu proyecto
spl_autoload_register(function($class) {
    include $_SERVER['DOCUMENT_ROOT'] . "/Version2.0/controller/{$class}.php";
});

require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../config/db.php';

$uri = filter_var(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), FILTER_SANITIZE_URL);
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseDir = rtrim($scriptName, '/');
$uri = str_replace($baseDir, '', $uri);

$routes = [
    '/' => 'views/landing/home.php',
    '/inicio' => 'views/landing/home.php',
    '/informacion' => 'views/landing/info.php',
    '/faq' => 'views/landing/faq.php',
    '/cookie' => 'views/landing/cookie.php',
    '/privacidad' => 'views/landing/privacy.php',
    '/contactar' => 'views/landing/contact.php',
    '/encuesta' => 'views/landing/login.php',
    
    '/404' => 'views/errors/404.php',
    '/cuestionario' => 'views/survey/cuestionario.php',
    '/encuestafinalizada' => 'views/survey/yaFinalizada.php',
    '/gracias' => 'views/survey/gracias.php',


];

function handleError($errorCode, $message)
{
    http_response_code($errorCode);
    logError($message);
    require_once __DIR__ . "/../views/errors/$errorCode.php";
    exit;
}

function logError($message)
{
    $logFile = __DIR__ . '/../logs/errors.log';
    $time = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$time] $message" . PHP_EOL, FILE_APPEND);
}

if (isset($routes[$uri])) {
    $path = __DIR__ . '/../' . $routes[$uri];
    if (file_exists($path)) {
        require_once $path;
    } else {
        handleError(404, "Archivo no encontrado: $path");
    }
} else {
    handleError(404, "Ruta no definida: $uri");
}





