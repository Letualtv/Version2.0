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
    
        // Recuperar respuestas de la base de datos y cargarlas en la sesión
        $this->recuperarRespuestasDeBD($claveId);
    
        // Redirigir al usuario a la última página completada si no se especifica una página
        if (!isset($_GET['n_pag'])) {
            $currentPag = $_SESSION['current_page'] ?? 1;
            header("Location: ?n_pag=$currentPag");
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

    public function recuperarRespuestasDeBD($claveId): void
    {
        global $pdo;
    
        // Consulta para obtener las respuestas del usuario
        $stmt = $pdo->prepare("SELECT * FROM cuestionario WHERE clave = ?");
        $stmt->bindParam(1, $claveId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result) {
            // Limpiar $_SESSION['respuestas'] antes de cargar nuevas respuestas
            $_SESSION['respuestas'] = [];
    
            foreach ($result as $columna => $valor) {
                if (strpos($columna, 'r') === 0 && !empty($valor)) { // Solo columnas rX
                    $id = substr($columna, 1); // Eliminar el prefijo "r"
                    $_SESSION['respuestas'][$id] = $valor;
                    error_log("Respuesta recuperada: Pregunta $id, Valor $valor");
                }
            }
    
            // Registrar el progreso del usuario en la sesión
            $_SESSION['current_page'] = $this->calcularPaginaActual($_SESSION['respuestas']);
            error_log("Página actual calculada: " . $_SESSION['current_page']);
        } else {
            error_log("No se encontraron respuestas en la base de datos para la clave $claveId");
        }
    }
    public function calcularPaginaActual(array $respuestas): int
    {
        $preguntas = $this->obtenerPreguntas();
    
        // Obtener todas las páginas disponibles
        $paginas = array_unique(array_column($preguntas, 'n_pag'));
        sort($paginas);
    
        // Iterar sobre las páginas en orden inverso para encontrar la última completada
        for ($i = count($paginas) - 1; $i >= 0; $i--) {
            $pagina = $paginas[$i];
            $preguntasEnPagina = array_filter($preguntas, fn($p) => $p['n_pag'] === $pagina);
    
            // Verificar si todas las preguntas en esta página tienen respuestas
            $completada = true;
            foreach ($preguntasEnPagina as $pregunta) {
                if (!isset($respuestas[$pregunta['id']])) {
                    $completada = false;
                    break;
                }
            }
    
            if ($completada) {
                return $pagina;
            }
        }
        error_log("Respuestas recuperadas de la base de datos: " . print_r($_SESSION['respuestas'], true));        // Si no hay respuestas, devolver la primera página
        return 1;
    }

    public function guardarRespuestasEnBD(): void
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