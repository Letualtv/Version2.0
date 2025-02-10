<?php
// El nombre del repositorio y el propietario
$owner = 'Letualtv';
$repo = 'Version2.0';
$tag = 'encuesta';

// URL de la API de GitHub para obtener la información del release específico
$url = "https://api.github.com/repos/$owner/$repo/releases/tags/$tag";

// Configurar las opciones de la solicitud HTTP
$options = [
    'http' => [
        'header' => "User-Agent: PHP\r\n",
        'method' => 'GET'
    ]
];

// Crear el contexto de la solicitud HTTP
$context = stream_context_create($options);

// Hacer la solicitud a la API de GitHub
$response = file_get_contents($url, false, $context);

// Decodificar la respuesta JSON
$release = json_decode($response, true);

// Obtener la versión del release específico
$releaseName = $release['name'] ?? 'No release found';

// Imprimir la versión del release específico
echo "($releaseName)";
?>