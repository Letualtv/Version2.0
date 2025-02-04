<?php
// Obtener la URL actual
$currentUrl = basename(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

include_once __DIR__ . '/../controller/GeneralController.php';

// Llamar a la función para obtener los datos del JSON y reemplazar variables
$variables = GeneralController();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" crossorigin="anonymous">
    <link rel="stylesheet" href="../assets/css/_buttons.css">

    <title>Encuesta IESA</title>
    <link rel="icon" href="../favicon.ico">
</head>
<body>


<?php  include __DIR__ . '/navbarPregunta.php'; ?>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" crossorigin="anonymous"></script>
</body>
</html>
