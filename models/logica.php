<?php
session_start();

class Logica
{
    private $preguntas;

    public function __construct()
    {
        // Ruta del archivo JSON
        $archivo = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/preguntas.json';

        if (file_exists($archivo)) {
            $json = file_get_contents($archivo);
            $this->preguntas = json_decode($json, true);
        } else {
            $this->preguntas = [];
        }
    }

    public function obtenerPreguntaPorPagina($n_pag)
    {
        foreach ($this->preguntas as $pregunta) {
            if ($pregunta['n_pag'] == $n_pag) {
                return $pregunta;
            }
        }
        return null;
    }

    public function procesarRespuesta($preguntaId, $respuesta)
    {
        $_SESSION['respuestas'][$preguntaId] = $respuesta;

        // Encuentra la pregunta actual
        $preguntaActual = $this->obtenerPreguntaPorPagina($_SESSION['current_page']);

        if ($preguntaActual) {
            // Encuentra la opción seleccionada
            foreach ($preguntaActual['options'] as $option) {
                if ($option['value'] == $respuesta) {
                    // Redirige a la siguiente página si existe, de lo contrario finaliza
                    if (isset($option['next_pag'])) {
                        header("Location: /cuestionario/" . $option['next_pag']);
                        exit;
                    } else {
                        header("Location: /gracias");
                        exit;
                    }
                }
            }
        }
    }
}
?>
