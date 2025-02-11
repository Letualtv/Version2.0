<body class="d-flex flex-column min-vh-100">

<?php
    session_start();  // Iniciar sesión

// Mostrar navegación
include __DIR__ . '/../../views/auth/procesoLogin.php';
$pageTitle = "Comenzar encuesta";
include __DIR__ . '/../../includes/navigation.php';

?>

<div class="container">


    <!-- Formulario -->
    <form method="POST" action="" class="was-validated">
        <div class="card mb-4">
            <div class="card-header px-md-4 py-md-3">
                <h3>Consentimiento informado</h3>
            </div>
            <div class="card-body p-md-4">
                <p>
                    Si considera que todas las dudas han sido aclaradas y tiene la convicción de participar en este estudio, a continuación, puede prestar su consentimiento para responder a la encuesta:
                </p>
                <div class="form-check">
                <input type="checkbox" class="form-check-input" required name="rgpd" id="check" onchange="showContent()" <?php if (!empty($errorMessage)) echo 'checked'; ?>>
                <label class="form-check-label" for="check">
                        He leído la información relativa a este estudio. Comprendo que mi participación es voluntaria, por lo que puedo abandonar la encuesta en cualquier momento, así como dejar sin responder cualquier pregunta del cuestionario. Tengo al menos 18 años y doy mi consentimiento para participar en el estudio propuesto.
                    </label>
                </div>
            </div>

            <!-- Contenido oculto que se muestra cuando se marca el checkbox -->
            <div id="contpriv" >
                <div class="mb-4 col-10 col-md-5 mx-auto text-center">
                    <p>
                        <strong>Clave de acceso</strong>
                    </p>
                        <!-- Mostrar el mensaje de error si existe -->
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger text-center">
            <?php echo $errorMessage; ?>
        </div>
    <?php endif; ?>
                    <div class="col-12 col-lg-6 mx-auto">
                    <input type="text" class="form-control" placeholder="Escriba su clave aquí" required name="clave" value="<?php echo isset($_POST['clave']) ? htmlspecialchars($_POST['clave']) : ''; ?>">
                    </div>
                    <button type="submit" class="btn btn-primary mt-4">Abrir encuesta</button>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
/*     // Función para mostrar/ocultar el contenido del formulario
    function showContent() {
        var element = document.getElementById("contpriv");
        var check = document.getElementById("check");
        if (check.checked) {
            element.style.display = 'block';
        } else {
            element.style.display = 'none';
        }
    } */
</script>

<?php include __DIR__ . '/../../includes/footer.php'; ?>

</body>
