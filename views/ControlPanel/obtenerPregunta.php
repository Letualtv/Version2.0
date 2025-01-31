<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $archivo = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/preguntas.json';
    if (file_exists($archivo)) {
        $preguntas = json_decode(file_get_contents($archivo), true);
        foreach ($preguntas as $pregunta) {
            if ($pregunta['id'] == $id) {
                echo json_encode($pregunta);
                exit;
            }
        }
    }
}
echo json_encode(null);
?>
