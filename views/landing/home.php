<body class="d-flex flex-column min-vh-100">

    <?php
    $pageTitle = "Inicio";
    include './../includes/navigation.php';
 
    session_start();  // Iniciar sesión
    session_unset();  // Elimina todas las variables de sesión
    session_destroy();  // Destruye la sesión completamente
    
    // Redirigir al landing page
    header("Location: Version2.0/public/inicio");  // Ajusta esta URL a tu página de aterrizaje

    
    ?>

    <div class="container  mb-3">
        <div class="card mx-auto ">
            <div class="card-header p-md-4">
            <h5 class="lh-3">
                El Consejo Superior de Investigaciones Científicas, a través del Instituto de Estudios Sociales
                Avanzados, está realizando un estudio sobre la calidad institucional en las universidades,
                los organismos públicos de investigación y otros centros de I+D y tecnología.
            </h5></div>
            <div class="card-body  p-md-4">
                <p class="pb-2">
                    El objetivo es ayudar a diagnosticar adecuadamente la situación actual de las instituciones en estos sectores.
                    Para lograrlo es importante incorporar el conocimiento y la experiencia de profesionales como usted.
                </p>
                <p class="pb-2">
                    Le invitamos a responder el siguiente cuestionario cuya duración aproximada es de 14 minutos.
                </p>
                <p class="pb-2">
                    En esta página web se incluye información detallada sobre el estudio, el equipo de trabajo y el procedimiento
                    de la encuesta. No dude en ponerse en contacto con nosotros cuando lo considere necesario.
                </p>
                <div class=" text-center ">

                    <p class="fw-bold text-center">Muchas gracias por su tiempo y colaboración.</p>
                    <a href="encuesta"><button 
                        class="btn btn-primary my-3">
                        Comenzar encuesta
                    </button></a>
                </div>
            </div>
        </div>
    </div>

    <?php include './../includes/footer.php'; ?>

</body>