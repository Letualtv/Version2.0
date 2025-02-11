<?php
session_start();
include __DIR__ . '/../../config/db.php';
include __DIR__ . '/../../controller/PreguntasController.php';

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

            // Recuperar las respuestas de la base de datos para calcular la última página completada
            $respuestasQuery = "SELECT * FROM cuestionario WHERE clave = :clave";
            $respuestasStmt = $pdo->prepare($respuestasQuery);
            $respuestasStmt->bindParam(':clave', $clave, PDO::PARAM_STR);
            $respuestasStmt->execute();
            $respuestasResult = $respuestasStmt->fetch(PDO::FETCH_ASSOC);

            $lastPage = 1; // Por defecto, redirigir a la página 1
            if ($respuestasResult) {
                // Calcular la última página completada basándose en las respuestas
                $preguntas = json_decode(file_get_contents(__DIR__ . '/../../models/Preguntas.json'), true);
                $paginas = array_unique(array_column($preguntas, 'n_pag'));
                sort($paginas);

                foreach (array_reverse($paginas) as $pagina) {
                    $preguntasEnPagina = array_filter($preguntas, fn($p) => $p['n_pag'] === $pagina);
                    $completada = true;

                    foreach ($preguntasEnPagina as $pregunta) {
                        if (!isset($respuestasResult["r{$pregunta['id']}"])) {
                            $completada = false;
                            break;
                        }
                    }

                    if ($completada) {
                        $lastPage = $pagina;
                        break;
                    }
                }
            }

            // Redirige al controlador de preguntas con la última página completada
            header("Location: cuestionario?n_pag=$lastPage");
            exit;
        } else {
            $errorMessage = "Clave incorrecta. Por favor, intenta nuevamente.";
        }
    } catch (PDOException $e) {
        $errorMessage = "Error de conexión con la base de datos: " . $e->getMessage();
    }
}
?>