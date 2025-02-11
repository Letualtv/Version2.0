<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<?php
session_start();

include __DIR__ . '/../../controller/PreguntasController.php';
include_once  __DIR__ . '/../../config/db.php';
$variables = include_once  __DIR__ . '/../../models/variables.php';

// Asegúrate de que el `clave_id` esté disponible en la sesión
if (!isset($_SESSION['clave'])) {
    header('Location: /../views/errors/errorClave.php');
    exit;
}

// Recupera las respuestas del controlador
$controller = new PreguntasController();
$resultado = $controller->mostrarPreguntasPorPagina($_GET['n_pag'] ?? 1);

// Mover la recuperación de respuestas dentro de la verificación de `clave_id`
$respuestas = $controller->recuperarRespuestasDeBD($_SESSION['clave'] ?? '');

if (!isset($_SESSION['respuestas'])) {
    $_SESSION['respuestas'] = [];
}

if ($resultado['error']) {
    include_once $resultado['view'];
} else {
    extract($resultado['data']);
    include_once $resultado['view'];
}



?>

<body class="d-flex flex-column min-vh-100">
    <?php include __DIR__ . '/../../includes/navigationPregunta.php'; ?>
    <div class="container my-4 col-12 col-lg-10">
        <form class="card" action="?n_pag=<?= htmlspecialchars($_GET['n_pag'] ?? 1) ?>" method="POST" style="min-height: 75vh;">
            <?php include __DIR__ . '/vistaCuestionario.php'; ?>
        </form>
    </div>
    <?php include __DIR__ . '/../../includes/footerPregunta.php'; ?>
</body>
</html>
