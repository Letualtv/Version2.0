<?php
session_start();

class PreguntasController
{
    public function mostrarPreguntasPorPagina(int $n_pag): array
    {
        $claveId = $_SESSION['clave_id'];

        // Verificar si la encuesta ya está finalizada
        if ($this->verificarEncuestaFinalizada($claveId)) {
            header('Location: encuestafinalizada');
            exit;
        }

        $preguntas = $this->obtenerPreguntas();
        $preguntasEnPagina = array_filter($preguntas, fn($p) => $p['n_pag'] === $n_pag);

        if (empty($preguntasEnPagina)) {
            return [
                'error' => true,
                'view' => $_SERVER['DOCUMENT_ROOT'] . '/version2.0/views/errors/errorPregunta.php',
            ];
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->guardarRespuestas($_POST);

            // Guardar estado de la sesión
            $this->guardarEstadoSesion($claveId);

            // Si no hay una siguiente página, marcar la encuesta como finalizada y redirigir a gracias.php
            $paginacion = $this->calcularPaginacion($preguntas, $n_pag);
            if (is_null($paginacion['nextPag'])) {
                $this->marcarEncuestaComoFinalizada($claveId);
                header('Location: gracias');
                exit;
            }
        }

        // Guarda la página actual en la sesión
        $_SESSION['current_page'] = $n_pag;

        $paginacion = $this->calcularPaginacion($preguntas, $n_pag);
        return [
            'error' => false,
            'data' => [
                'preguntasEnPagina' => $preguntasEnPagina,
                'prevPag' => $paginacion['prevPag'],
                'nextPag' => $paginacion['nextPag'],
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

    private function obtenerPreguntas(): array
    {
        $archivo = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/preguntas.json';

        if (!file_exists($archivo)) {
            return [];
        }

        // Leer el archivo JSON
        $json = file_get_contents($archivo);

        // Incluir el archivo que define las variables
        require_once $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/general.php';

        // Verificar que $variables esté definido correctamente
        if (!isset($variables) || !is_array($variables)) {
            throw new Exception("Las variables no están definidas correctamente.");
        }

        // Reemplazar las variables en el JSON
        $json = strtr($json, $variables);

        // Decodificar el JSON
        return json_decode($json, true); // true para convertirlo en un array asociativo
    }

    private function guardarRespuestas(array $respuestas): void
    {
        foreach ($respuestas as $key => $respuesta) {
            // Suponiendo que $key es el ID de la pregunta
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

    private function guardarEstadoSesion($claveId): void
    {
        global $pdo;

        // Convertir la sesión en un formato serializado para guardarla en la base de datos
        $estadoSesion = serialize($_SESSION);

        // Guardar el estado de la sesión en la base de datos
        $stmt = $pdo->prepare("UPDATE claves SET estado_sesion = :estado_sesion WHERE id = :clave_id");
        $stmt->bindParam(':estado_sesion', $estadoSesion, PDO::PARAM_STR);
        $stmt->bindParam(':clave_id', $claveId, PDO::PARAM_INT);
        $stmt->execute();
    }
}
?>
