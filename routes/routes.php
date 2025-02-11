<?php
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

// Manejo de errores
function handleError($errorCode, $message)
{
    http_response_code($errorCode);
    logError($message);
    require_once __DIR__ . "/../views/errors/$errorCode.php";
    exit;
}

// Registro de errores
function logError($message)
{
    $logFile = __DIR__ . '/../logs/errors.log';
    $time = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$time] $message" . PHP_EOL, FILE_APPEND);
}

// Obtener la URI actual y eliminar cualquier parámetro GET
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Eliminar el prefijo /Version2.0/ de la URI
$base_path = '/Version2.0'; // Cambia esto si usas otro prefijo
if (strpos($uri, $base_path) === 0) {
    $uri = substr($uri, strlen($base_path));
}

// Si la URI resultante es una cadena vacía, establecerla como '/'
if ($uri === '') {
    $uri = '/';
}

// Enrutamiento
if (isset($routes[$uri])) {
    $path = __DIR__ . '/../' . ltrim($routes[$uri], '/');
    if (file_exists($path)) {
        require_once $path;
    } else {
        handleError(404, "Archivo no encontrado: $path");
    }
} else {
    handleError(404, "Ruta no definida: $uri");
}