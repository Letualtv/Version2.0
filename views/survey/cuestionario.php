<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>

<?php
session_start();



include $_SERVER['DOCUMENT_ROOT'] . '/version2.0/controller/PreguntasController.php';
include $_SERVER['DOCUMENT_ROOT'] . '/version2.0/config/db.php';

if (!isset($_SESSION['respuestas'])) {
    $_SESSION['respuestas'] = [];
}

// Asegúrate de que el `clave_id` esté disponible en la sesión
if (!isset($_SESSION['clave_id'])) {
    header('Location: /version2.0/views/errors/errorClave.php');
    exit;
}



$controller = new PreguntasController();
$resultado = $controller->mostrarPreguntasPorPagina($_GET['n_pag'] ?? 1);

if ($resultado['error']) {
    include_once $resultado['view'];
} else {
    extract($resultado['data']);
    include_once $resultado['view'];
}
?>

<body class="d-flex flex-column min-vh-100">
    <?php include './../includes/navigationPregunta.php'; ?>
    <div class="container my-4 col-12 col-lg-10">
        <form class="card" action="?n_pag=<?= htmlspecialchars($_GET['n_pag'] ?? 1) ?>" method="POST" style="min-height: 75vh;">
            <?php include 'vistaCuestionario.php'; ?>
        </form>
    </div>
    <?php include './../includes/footerPregunta.php'; ?>
</body>
</html>
