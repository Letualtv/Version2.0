<?php
$archivo = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/preguntas.json';
if (file_exists($archivo)) {
    $preguntas = json_decode(file_get_contents($archivo), true);
    echo json_encode($preguntas);
} else {
    echo json_encode([]);
}
?>
