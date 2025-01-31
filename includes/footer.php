<footer class=" mt-auto">
    <hr class="border border-success rounded">
    <p class="text-center">
        <a href="https://www.iesa.csic.es/" target="_blank" class="text-decoration-none">IESA-CSIC</a> C/ Campo Santo de los Mártires, 7 <span class="d-none d-sm-inline">-</span> <span class="d-block d-sm-inline">14004 Córdoba</span>
    </p>

    <?php if (strpos($_SERVER["REQUEST_URI"], "inicio") !== false): ?>
        <div class="my-2 text-center">
            Este sitio solo utiliza cookies para funcionar correctamente. Todos los datos recogidos son anónimos. Si continúa navegando, consideramos que acepta su uso. 
            <a href="cookie">LEER MÁS</a>
        </div>
    <?php endif; ?>

    <div class="text-center py-2" style="background-color: #e0e0e0;">
        <a href="cookie" class=" text-decoration-none">Política de Cookies</a> 
        <span class="text-dark mx-3">|</span> 
        <a href="privacidad" class=" text-decoration-none">Política de privacidad</a>
    </div>
</footer>
