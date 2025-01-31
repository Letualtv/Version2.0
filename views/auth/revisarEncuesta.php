<?php
session_start();
include $_SERVER['DOCUMENT_ROOT'] . '/version2.0/config/db.php';

if (!isset($_SESSION['clave_id'])) {
    // Redirige a la primera pregunta del cuestionario si no hay clave_id en la sesión
    header('Location: cuestionario?n_pag=1');
    exit;
}

$claveId = $_SESSION['clave_id'];

try {
    // Recuperar el estado de la sesión desde la base de datos
    $stmt = $pdo->prepare("SELECT estado_sesion FROM claves WHERE id = ?");
    $stmt->bindParam(1, $claveId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['estado_sesion']) {
        // Deserializar el estado de la sesión y restaurarlo
        $_SESSION = unserialize($result['estado_sesion']);
    }

    // Verifica si la encuesta ya está finalizada
    $stmt = $pdo->prepare("SELECT terminada FROM claves WHERE id = ?");
    $stmt->bindParam(1, $claveId, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($result && $result['terminada'] == 1) {
        // Si la encuesta está finalizada, redirige a la página yaFinalizada
        header('Location: yaFinalizada');
        exit;
    } else {
        // Recupera la página actual desde la sesión o redirige a la última página visitada
        $n_pag = $_SESSION['current_page'] ?? 1;

        // Redirige al usuario a la página en la que se quedó
        header("Location: cuestionario?n_pag=$n_pag");
        exit;
    }
} catch (PDOException $e) {
    // Maneja el error de conexión con la base de datos
    echo "Error de conexión con la base de datos: " . $e->getMessage();
}
?>
