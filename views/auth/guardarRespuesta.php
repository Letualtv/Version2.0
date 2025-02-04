<?php 
/* session_start();

// Incluir configuración de la base de datos
include $_SERVER['DOCUMENT_ROOT'] . '/version2.0/config/db.php';

// Verificar si el formulario fue enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (!isset($_SESSION['respuestas'])) {
        $_SESSION['respuestas'] = [];
    }

    // Procesar respuestas
    foreach ($_POST as $preguntaId => $respuesta) {
        if (is_array($respuesta)) {
            // Convertir el array en una cadena delimitada por comas
            $respuestaLimpia = implode(', ', array_map('htmlspecialchars', $respuesta));
        } else {
            // Si no es un array, limpiar la respuesta normalmente
            $respuestaLimpia = trim(htmlspecialchars($respuesta));
        }

        // Almacenar la respuesta en la sesión
        $_SESSION['respuestas'][$preguntaId] = $respuestaLimpia;
    }

    // Mostrar la sesión para depuración
    echo "<pre>Respuestas guardadas en la sesión:";
    print_r($_SESSION);
    echo "</pre>";

    // Preparar para guardar en la base de datos
    try {
        // Obtener la clave de usuario y fecha actual
        $clave = $_SESSION['clave'] ?? null;
        if (!$clave) {
            throw new Exception("Error: La clave de usuario no está definida.");
        }
        $fecha = date('Y-m-d H:i:s');

        // Construir la consulta SQL
        $columns = ['clave', 'date'];
        $values = [':clave' => $clave, ':date' => $fecha];
        $updates = ['date = VALUES(date)'];

        foreach ($_SESSION['respuestas'] as $preguntaId => $respuesta) {
            $columna = "r$preguntaId";
            $columns[] = $columna;
            $values[":$columna"] = $respuesta;
            $updates[] = "$columna = VALUES($columna)";
        }

        $columnsSQL = implode(', ', $columns);
        $placeholdersSQL = implode(', ', array_keys($values));
        $updatesSQL = implode(', ', $updates);

        $query = "
            INSERT INTO cuestionario ($columnsSQL)
            VALUES ($placeholdersSQL)

            ON DUPLICATE KEY UPDATE $updatesSQL
        ";

        // Ejecutar la consulta
        $stmt = $pdo->prepare($query);
        $stmt->execute($values);

        echo "Respuestas guardadas correctamente.";
    } catch (Exception $e) {
        echo "Error al guardar las respuestas: " . $e->getMessage();
    }
} */ ?>