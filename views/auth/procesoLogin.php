<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/version2.0/config/db.php';

$errorMessage = "";
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $clave = trim($_POST['clave']);

    try {
        // Comprueba si la clave existe en la base de datos
        $query = "SELECT id, clave FROM claves WHERE clave = :clave";
        $stmt = $pdo->prepare($query);
        $stmt->bindParam(':clave', $clave, PDO::PARAM_STR);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // Obtener el ID y la clave de la base de datos
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $claveId = $result['id'];

            // La clave es válida, ahora verificamos la cookie
            $cookie = session_id();
            $checkQuery = "SELECT * FROM muestra WHERE clave = :clave AND cookie = :cookie";
            $checkStmt = $pdo->prepare($checkQuery);
            $checkStmt->bindParam(':clave', $clave, PDO::PARAM_STR);
            $checkStmt->bindParam(':cookie', $cookie, PDO::PARAM_STR);
            $checkStmt->execute();

            if ($checkStmt->rowCount() === 0) {
                // Si no existe un registro con esa clave y cookie, se inserta
                $browser = $_SERVER['HTTP_USER_AGENT'] ?? 'Desconocido';
                $lang = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'Desconocido';
                $ip = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

                $insertQuery = "INSERT INTO muestra (clave, cookie, browser, lang, ip, n_login) 
                                VALUES (:clave, :cookie, :browser, :lang, :ip, 1)";
                $insertStmt = $pdo->prepare($insertQuery);
                $insertStmt->bindParam(':clave', $clave, PDO::PARAM_STR);
                $insertStmt->bindParam(':cookie', $cookie, PDO::PARAM_STR);
                $insertStmt->bindParam(':browser', $browser, PDO::PARAM_STR);
                $insertStmt->bindParam(':lang', $lang, PDO::PARAM_STR);
                $insertStmt->bindParam(':ip', $ip, PDO::PARAM_STR);
                $insertStmt->execute();
            } else {
                // Si ya existe un registro, incrementamos el contador de n_login
                $updateQuery = "UPDATE muestra SET n_login = n_login + 1 WHERE clave = :clave AND cookie = :cookie";
                $updateStmt = $pdo->prepare($updateQuery);
                $updateStmt->bindParam(':clave', $clave, PDO::PARAM_STR);
                $updateStmt->bindParam(':cookie', $cookie, PDO::PARAM_STR);
                $updateStmt->execute();
            }

            // Guardar la clave y clave_id en la sesión
            $_SESSION['clave'] = $clave;
            $_SESSION['clave_id'] = $claveId;

            // Redirige al controlador de preguntas
            header("Location: cuestionario?n_pag=1");
            exit;
        } else {
            $errorMessage = "Clave incorrecta. Por favor, intenta nuevamente.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error de conexión con la base de datos: " . $e->getMessage();
    }
}
?>
