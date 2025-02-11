<?php
session_start();

// Autoload para los controladores
spl_autoload_register(function($class) {
    include_once __DIR__ . "/controller/{$class}.php";
});

require_once __DIR__ . '/config/db.php';

// Procesar la URI
$uri = filter_var(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH), FILTER_SANITIZE_URL);
$scriptName = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME']));
$baseDir = rtrim($scriptName, '/');
$uri = str_replace($baseDir, '', $uri);



// Incluir el archivo de rutas
require_once __DIR__ . '/routes/routes.php';
