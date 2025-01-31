<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $id = isset($data['id']) && !empty($data['id']) ? $data['id'] : time();
    $titulo = $data['titulo'];
    $n_pag = $data['n_pag'];
    $tipo = $data['tipo'];
    $subTitulo = $data['subTitulo'];
    $opciones = $data['opciones'];
    $next_pag = $data['next_pag'];
    $valores = isset($data['valores']) ? $data['valores'] : [];

    $nuevaPregunta = [
        'id' => $id,
        'n_pag' => (int)$n_pag,
        'tipo' => $tipo,
        'titulo' => $titulo,
        'subTitulo' => $subTitulo,
        'opciones' => []
    ];

    foreach ($opciones as $index => $opcion) {
        $nuevaPregunta['opciones'][$index + 1] = $opcion;
    }

    if ($tipo === 'numberInput') {
        $nuevaPregunta['valores'] = [
            'min' => isset($valores['min']) ? (int)$valores['min'] : 1950,
            'max' => isset($valores['max']) ? (int)$valores['max'] : date('Y'),
            'placeholder' => isset($valores['placeholder']) ? $valores['placeholder'] : 'AAAA'
        ];
    }

    $archivo = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/preguntas.json';
    if (file_exists($archivo)) {
        $preguntas = json_decode(file_get_contents($archivo), true);

        // Buscar y actualizar la pregunta si ya existe
        $found = false;
        foreach ($preguntas as &$pregunta) {
            if ($pregunta['id'] == $id) {
                $pregunta = $nuevaPregunta;
                $found = true;
                break;
            }
        }
        
        // Si no se encontró, añadir la nueva pregunta
        if (!$found) {
            $preguntas[] = $nuevaPregunta;
        }

        file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT));
    } else {
        // Crear un nuevo archivo JSON con la primera pregunta
        $preguntas = [$nuevaPregunta];
        file_put_contents($archivo, json_encode($preguntas, JSON_PRETTY_PRINT));
    }

    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
