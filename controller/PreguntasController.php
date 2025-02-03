<?php
session_start();

class PreguntasController
{
    public function mostrarPreguntasPorPagina(int $n_pag): array
    {
        $claveId = $_SESSION['clave_id'];

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
            $this->guardarEstadoSesion($claveId);

            $paginacion = $this->calcularPaginacion($preguntas, $n_pag);
            if (is_null($paginacion['nextPag'])) {
                $this->marcarEncuestaComoFinalizada($claveId);
                header('Location: gracias');
                exit;
            }
        }

        // Calcular el progreso
        $totalPaginas = max(array_column($preguntas, 'n_pag'));
        $progreso = ($n_pag / $totalPaginas) * 100;
        $progreso = round($progreso, 2);

        $_SESSION['current_page'] = $n_pag;

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

    private function guardarEstadoSesion($claveId): void
    {
        global $pdo;

        $estadoSesion = serialize($_SESSION);
        $stmt = $pdo->prepare("UPDATE claves SET estado_sesion = :estado_sesion WHERE id = :clave_id");
        $stmt->bindParam(':estado_sesion', $estadoSesion, PDO::PARAM_STR);
        $stmt->bindParam(':clave_id', $claveId, PDO::PARAM_INT);
        $stmt->execute();
    }
}

?>
