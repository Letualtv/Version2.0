<!-- En tu archivo de vista (cuestionario.php) -->
<nav class="navbar sticky-top navbar-light bg-light ">
    <div class="d-flex row"></div>
    <div class="col-12 col-md-6  text-center">
        <h6 class="navbar-text px-2 px-md-0 align-middle">
        <?php 
// Accede e imprime un valor específico del array
echo $variables['$estudio']; 
?>

        </h6>
    </div>
    <div class="col-12 col-md-6 text-center">
    <?php if (strpos($currentUrl, 'gracias') !== false): ?>
                    <div class="badge text-bg-success align-middle px-3 py-2 d-none d-md-inline">Encuesta terminada</div>
            <div class="text-bg-success d-flex d-md-none p-2 mx-auto">
                <div class="badge text-bg-success align-middle fs-6">Encuesta terminada</div>
            </div>
        <?php else: ?>
            <div class="badge text-bg-secondary align-middle px-3 py-2 d-none d-md-inline"><?php echo $progreso . '%'; ?> Completado</div>
            <div class="text-bg-secondary d-flex d-md-none p-2 mx-auto">
                <div class="badge text-bg-secondary align-middle fs-6"><?php echo $progreso . '%'; ?> Completado</div>
            </div>
        <?php endif; ?>
    </div>
</nav><?php

?>