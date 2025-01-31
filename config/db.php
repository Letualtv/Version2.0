<?php
$host = 'localhost'; // o la IP del servidor MySQL
$dbname = 'phppuro'; // Nombre de tu base de datos
$username = 'root'; // Tu usuario de base de datos
$password = ''; // Tu contraseña de base de datos

try {
    // Establecer la conexión utilizando PDO
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Establecer el modo de error a excepción
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
} catch (PDOException $e) {
    // Si hay un error, muestra un mensaje y termina el script
    die("Error de conexión: " . $e->getMessage());
}

?>
