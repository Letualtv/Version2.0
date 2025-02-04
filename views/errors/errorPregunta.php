<div id="alertaPregunta" class="position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-light" style="z-index: 1050; display: none;">
    <div class="alert alert-danger text-center" role="alert">
        <h4 class="alert-heading">Pregunta no encontrada</h4>
        <p>No se encontró la pregunta. Serás redirigido a la primera página.</p>
        <a href="inicio" class="btn btn-primary">Ir a la página principal</a>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', (event) => {
    const preguntasEnPagina = <?php echo json_encode($preguntasEnPagina); ?>;
    if (preguntasEnPagina.length === 0) {
        const alertaPregunta = document.getElementById('alertaPregunta');
        alertaPregunta.style.display = 'flex'; // Mostrar la alerta
        setTimeout(() => {
            window.location.href = 'inicio'; // Reemplaza con la URL de tu primera página
        }, 3000); // Espera 3 segundos antes de redirigir
    }
});
</script>
