<?php
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $archivo =  __DIR__ . '/../models/Preguntas.json';

    if (file_exists($archivo)) {
        $preguntas = json_decode(file_get_contents($archivo), true);

        $preguntas = array_filter($preguntas, function($pregunta) use ($id) {
            return $pregunta['id'] != $id;
        });

        if (file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT)) !== false) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al escribir el archivo.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Archivo no encontrado.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida o falta el ID.']);
}
?>
