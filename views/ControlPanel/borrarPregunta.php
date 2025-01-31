<?php
if ($_SERVER['REQUEST_METHOD'] === 'DELETE' && isset($_GET['id'])) {
    $id = $_GET['id'];
    $archivo = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/preguntas.json';
    if (file_exists($archivo)) {
        $preguntas = json_decode(file_get_contents($archivo), true);
        $preguntas = array_filter($preguntas, function($pregunta) use ($id) {
            return $pregunta['id'] != $id;
        });
        file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT));
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
} else {
    echo json_encode(['success' => false]);
}
?>
