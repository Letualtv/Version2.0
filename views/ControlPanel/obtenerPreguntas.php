<?php
$archivo = __DIR__ . '/../../models/Preguntas.json';

if (file_exists($archivo)) {
    $preguntas = json_decode(file_get_contents($archivo), true);
    echo json_encode($preguntas);
} else {
    echo json_encode([]);
}
?>
