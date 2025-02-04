<?php
session_start();

class PreguntasController
{
    public function mostrarPreguntasPorPagina(int $n_pag): array
    {
        // Verificar si la encuesta ya ha sido finalizada
        $claveId = $_SESSION['clave_id'];
        if ($this->verificarEncuestaFinalizada($claveId)) {
            header('Location: encuestafinalizada');
            exit;
        }

        // Obtener las preguntas y filtrar por página actual
        $preguntas = $this->obtenerPreguntas();
        $preguntasEnPagina = array_filter($preguntas, fn($p) => $p['n_pag'] === $n_pag);

        // Si no hay preguntas para esta página, devolver un error
        if (empty($preguntasEnPagina)) {
            return [
                'error' => true,
                'view' => $_SERVER['DOCUMENT_ROOT'] . '/version2.0/views/errors/errorPregunta.php',
            ];
        }

        // Recuperar respuestas de la base de datos y cargarlas en la sesión
        $this->recuperarRespuestasDeBD($claveId);

        // Procesar respuestas si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->guardarRespuestas($_POST);
            $this->guardarRespuestasEnBD($claveId);

            // Calcular la paginación
            $paginacion = $this->calcularPaginacion($preguntas, $n_pag);

            // Si no hay más páginas, marcar la encuesta como finalizada
            if (is_null($paginacion['nextPag'])) {
                $this->marcarEncuestaComoFinalizada($claveId);
                header('Location: gracias');
                exit;
            }

            // Redirigir al usuario a la siguiente página
            header("Location: ?n_pag={$paginacion['nextPag']}");
            exit;
        }

        // Calcular el progreso
        $totalPaginas = max(array_column($preguntas, 'n_pag'));
        $progreso = round(($n_pag / $totalPaginas) * 100, 2);
        $_SESSION['current_page'] = $n_pag;

        // Calcular la paginación
        $paginacion = $this->calcularPaginacion($preguntas, $n_pag);

        return [
            'error' => false,
            'data' => [
                'preguntasEnPagina' => $preguntasEnPagina,
                'prevPag' => $paginacion['prevPag'],
                'nextPag' => $paginacion['nextPag'],
                'progreso' => $progreso,
            ],
            'view' => $_SERVER['DOCUMENT_ROOT'] . '/version2.0/views/survey/cuestionario.php',
        ];
    }

    private function verificarEncuestaFinalizada(int $claveId): bool
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT terminada FROM claves WHERE id = ?");
        $stmt->bindParam(1, $claveId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result && $result['terminada'] == 1;
    }

    private function marcarEncuestaComoFinalizada(int $claveId): void
    {
        global $pdo;
        $stmt = $pdo->prepare("UPDATE claves SET terminada = 1 WHERE id = ?");
        $stmt->bindParam(1, $claveId, PDO::PARAM_INT);
        $stmt->execute();
    }

    public function obtenerPreguntas(): array
    {
        $archivo = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/preguntas.json';
        if (!file_exists($archivo)) {
            return [];
        }
        $json = file_get_contents($archivo);
        require_once $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/general.php';
        if (!isset($variables) || !is_array($variables)) {
            throw new Exception("Las variables no están definidas correctamente.");
        }
        $json = strtr($json, $variables);
        return json_decode($json, true);
    }

    private function guardarRespuestas(array $respuestas): void
    {
        foreach ($respuestas as $key => $respuesta) {
            $_SESSION['respuestas'][$key] = is_array($respuesta) ? implode(', ', $respuesta) : $respuesta;
        }
    }

    private function calcularPaginacion(array $preguntas, int $n_pag): array
    {
        $prevPag = $n_pag > 1 ? $n_pag - 1 : null;
        $nextPag = count(array_filter($preguntas, fn($p) => $p['n_pag'] === $n_pag + 1)) > 0 ? $n_pag + 1 : null;
        return [
            'prevPag' => $prevPag,
            'nextPag' => $nextPag,
        ];
    }

    private function recuperarRespuestasDeBD($claveId): void
    {
        global $pdo;
        $stmt = $pdo->prepare("SELECT * FROM cuestionario WHERE clave = ?");
        $stmt->bindParam(1, $claveId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($result) {
            foreach ($result as $columna => $valor) {
                if (strpos($columna, 'r') === 0 && !empty($valor)) { // Solo columnas rX
                    $id = substr($columna, 1); // Eliminar el prefijo "r"
                    $_SESSION['respuestas'][$id] = $valor;
                }
            }
        }
    }

    private function guardarRespuestasEnBD($claveId): void
    {
        global $pdo;

        // Verificar que haya respuestas para guardar
        if (empty($_SESSION['respuestas'])) {
            return;
        }

        try {
            // Obtener la clave de usuario y fecha actual
            $clave = $_SESSION['clave'] ?? null;
            if (!$clave) {
                throw new Exception("Error: La clave de usuario no está definida.");
            }
            $fecha = date('Y-m-d H:i:s');

            // Construir la consulta SQL dinámicamente
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

            // Mostrar mensaje de éxito para depuración
            error_log("Respuestas guardadas correctamente en la base de datos.");
        } catch (Exception $e) {
            // Registrar el error en el log del servidor
            error_log("Error al guardar las respuestas en la base de datos: " . $e->getMessage());
        }
    }
}
?>