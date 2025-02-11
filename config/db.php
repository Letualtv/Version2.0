<?php
$host = 'PMYSQL187.dns-servicio.com:3306'; // o la IP del servidor MySQL
$dbname = '10796594_encuestas_IESA'; // Nombre de tu base de datos
$username = 'AP_admin'; // Tu usuario de base de datos
$password = 'L37u4l*11'; // Tu contraseña de base de datos

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
