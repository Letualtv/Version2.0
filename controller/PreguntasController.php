<?php
session_start();
include_once __DIR__ . '/../config/db.php';

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
        $respuestas = $this->recuperarRespuestasDeBD($claveId);


        
        // Redirigir al usuario a la última página completada si no se especifica una página
        if (!isset($_GET['n_pag'])) {
            $currentPag = $this->calcularPaginaActual($respuestas);
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
                'view' => __DIR__ . '/../views/errors/errorPregunta.php',
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
            'view' => __DIR__ . '/../views/survey/cuestionario.php',
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
        // Ruta al archivo de preguntas
        $archivo = __DIR__ . '/../models/Preguntas.json';

        // Verificar si el archivo existe
        if (!file_exists($archivo)) {
            error_log("El archivo de preguntas no existe.");
            return [];
        }

        // Leer el contenido del archivo JSON
        $json = file_get_contents($archivo);

        // Cargar las variables globales desde variables.php
        $variablesFile = __DIR__ . '/../models/variables.php';
        if (!file_exists($variablesFile)) {
            throw new Exception("El archivo de variables no existe.");
        }
        $variables = include $variablesFile;

        // Validar que las variables sean un array
        if (!is_array($variables)) {
            throw new Exception("Las variables no están definidas correctamente.");
        }

        // Reemplazar las variables globales en el contenido del JSON
        $json = strtr($json, $variables);

        // Decodificar el JSON a un array asociativo
        $preguntas = json_decode($json, true);

        // Validar que el JSON sea válido
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error al decodificar el archivo JSON: " . json_last_error_msg());
        }

        return $preguntas;
    }
    private function guardarRespuestas(array $respuestas): void
    {
        foreach ($respuestas as $key => $respuesta) {
            $_SESSION['respuestas'][$key] = is_array($respuesta) ? implode(', ', $respuesta) : $respuesta;
        }
    }

    private function calcularPaginacion(array $preguntas, int $n_pag): array
    {
        // Obtener las respuestas actuales
        $respuestas = $_SESSION['respuestas'] ?? [];
    
        // Buscar la pregunta actual
        $preguntaActual = null;
        foreach ($preguntas as $pregunta) {
            if ($pregunta['n_pag'] === $n_pag) {
                $preguntaActual = $pregunta;
                break;
            }
        }
    
        // Calcular la página siguiente (nextPag)
        $nextPag = null;
    
        // Aplicar reglas de salto si existen
        if ($preguntaActual && isset($preguntaActual['jump_rules'])) {
            $idPreguntaActual = $preguntaActual['id'];
            if (isset($respuestas[$idPreguntaActual])) {
                $respuestaSeleccionada = $respuestas[$idPreguntaActual];
                error_log("Respuesta seleccionada para la pregunta {$idPreguntaActual}: {$respuestaSeleccionada}");
    
                // Verificar si la respuesta es numérica
                if (is_numeric($respuestaSeleccionada)) {
                    $respuestaSeleccionada = (int)$respuestaSeleccionada; // Convertir a entero
                    foreach ($preguntaActual['jump_rules'] as $rango => $paginaDestino) {
                        // Interpretar el rango
                        if (strpos($rango, '-') !== false) {
                            // Rango del tipo "X-Y"
                            list($min, $max) = array_map('intval', explode('-', $rango));
                            if ($respuestaSeleccionada >= $min && $respuestaSeleccionada <= $max) {
                                $nextPag = $paginaDestino;
                                error_log("Rango '{$rango}' coincide. Siguiente página: {$nextPag}");
                                break;
                            }
                        } elseif (strpos($rango, '+') !== false) {
                            // Rango del tipo "X+"
                            $min = (int)rtrim($rango, '+');
                            if ($respuestaSeleccionada >= $min) {
                                $nextPag = $paginaDestino;
                                error_log("Rango '{$rango}' coincide. Siguiente página: {$nextPag}");
                                break;
                            }
                        } else {
                            // Valor único
                            $valorUnico = (int)$rango;
                            if ($respuestaSeleccionada === $valorUnico) {
                                $nextPag = $paginaDestino;
                                error_log("Valor único '{$rango}' coincide. Siguiente página: {$nextPag}");
                                break;
                            }
                        }
                    }
                }
            }
        }
    
        // Si no hay regla de salto, calcular la siguiente página normalmente
        if (!$nextPag) {
            $nextPag = count(array_filter($preguntas, fn($p) => $p['n_pag'] === $n_pag + 1)) > 0 ? $n_pag + 1 : null;
            error_log("No hay regla de salto. Siguiente página calculada: {$nextPag}");
        }
    
        // Verificar reglas de visibilidad (filtro) para la siguiente página
        if ($nextPag) {
            $siguientePregunta = array_filter($preguntas, fn($p) => $p['n_pag'] === $nextPag);
            $siguientePregunta = reset($siguientePregunta);
    
            if (isset($siguientePregunta['filtro'])) { // Cambio aquí: visibility_rules -> filtro
                foreach ($siguientePregunta['filtro'] as $preguntaId => $respuestaRequerida) {
                    if (!isset($respuestas[$preguntaId]) || $respuestas[$preguntaId] != $respuestaRequerida) {
                        // Si no se cumple la regla de visibilidad, omitir esta página
                        error_log("La página {$nextPag} no cumple con las reglas de visibilidad.");
                        $nextPag = $this->calcularPaginaSiguienteConReglas($preguntas, $nextPag + 1);
                    }
                }
            }
        }
    
        // Calcular la página anterior (prevPag)
        $prevPag = null;
        if ($n_pag > 1) {
            // Buscar la última página respondida antes de la página actual
            $ultimaPaginaRespondida = 1;
            foreach ($respuestas as $preguntaId => $valor) {
                foreach ($preguntas as $pregunta) {
                    if ($pregunta['id'] == $preguntaId && $pregunta['n_pag'] < $n_pag) {
                        if ($pregunta['n_pag'] > $ultimaPaginaRespondida) {
                            $ultimaPaginaRespondida = $pregunta['n_pag'];
                        }
                    }
                }
            }
            $prevPag = $ultimaPaginaRespondida;
            error_log("Página anterior calculada: {$prevPag}");
        }
    
        return [
            'prevPag' => $prevPag,
            'nextPag' => $nextPag,
        ];
    }
    private function calcularPaginaSiguienteConReglas(array $preguntas, int $startPag): ?int
{
    for ($i = $startPag; $i <= max(array_column($preguntas, 'n_pag')); $i++) {
        $siguientePregunta = array_filter($preguntas, fn($p) => $p['n_pag'] === $i);
        $siguientePregunta = reset($siguientePregunta);

        if (!$siguientePregunta) {
            continue;
        }

        // Verificar si la página tiene un filtro
        if (isset($siguientePregunta['filtro'])) {
            $cumpleFiltro = true;
            foreach ($siguientePregunta['filtro'] as $preguntaId => $respuestaRequerida) {
                if (!isset($_SESSION['respuestas'][$preguntaId]) || $_SESSION['respuestas'][$preguntaId] != $respuestaRequerida) {
                    $cumpleFiltro = false;
                    break;
                }
            }
            if ($cumpleFiltro) {
                return $i;
            }
        } else {
            return $i;
        }
    }
    return null;
}
    public function recuperarRespuestasDeBD($clave): array
    {
        global $pdo;

        // Consulta para obtener las respuestas del usuario basándose en la clave
        $stmt = $pdo->prepare("SELECT * FROM cuestionario WHERE clave = ?");
        $stmt->bindParam(1, $clave, PDO::PARAM_STR);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $respuestas = [];
        if ($result) {
            foreach ($result as $columna => $valor) {
                if (strpos($columna, 'r') === 0) { // Solo columnas rX
                    $preguntaId = substr($columna, 1); // Quitar el prefijo 'r'
                    $respuestas[$preguntaId] = $valor;
                    error_log("Respuesta recuperada: Pregunta $preguntaId, Valor $valor");
                }
            }
        } else {
            error_log("No se encontraron respuestas en la base de datos para la clave $clave");
        }

        return $respuestas;
    }
    public function calcularPaginaActual(array $respuestas): int
    {
        $preguntas = $this->obtenerPreguntas();
    
        if (empty($respuestas)) {
            error_log("No hay respuestas, redirigiendo a la página 1.");
            return 1; // Si no hay respuestas, redirige a la primera página
        }
    
        // Inicializar la página por defecto
        $ultimaPagina = 1;
    
        // Buscar la última pregunta respondida y su página
        foreach ($respuestas as $preguntaId => $valor) {
            foreach ($preguntas as $pregunta) {
                if ($pregunta['id'] == $preguntaId) {
                    if ($pregunta['n_pag'] > $ultimaPagina) {
                        $ultimaPagina = $pregunta['n_pag'];
                    }
                    error_log("Pregunta encontrada. ID: {$pregunta['id']}, Página: {$pregunta['n_pag']}");
                }
            }
        }
    
        error_log("Última página calculada: {$ultimaPagina}");
        return $ultimaPagina; // Retornar la página correspondiente a la última pregunta respondida
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
