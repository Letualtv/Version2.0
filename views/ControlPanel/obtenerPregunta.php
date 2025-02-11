<?php
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $archivo = __DIR__ . '/../models/Preguntas.json';
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
