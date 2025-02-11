<div class="my-4">
        <!-- Logos -->
        <div class="text-center container-fluid mb-4">
            <?php

            

$dir = __DIR__ . '/../assets/img/';

if ($handle = opendir($dir)) {
    while (false !== ($file = readdir($handle))) {
        if (in_array(strtolower(pathinfo($file, PATHINFO_EXTENSION)), ['png', 'jpg', 'jpeg', 'gif', 'bmp', 'webp'])) {
            echo '<img class="mx-1 my-2 my-lg-0 img-fluid img-responsive-60" src="/Version2.0/assets/img/' . $file . '" alt="' . pathinfo($file, PATHINFO_FILENAME) . '" title="' . pathinfo($file, PATHINFO_FILENAME) . '">';
        }
    }
    closedir($handle);
}

            ?>
            <style>
                .img-responsive-60 {
                    max-height: 60px;
                    width: auto;
                }

                @media (max-width: 767px) {
                    .img-responsive-60 {
                        max-height: 35px;
                    }
                }
            </style>
        </div>

        <!-- Título -->
        <div class="text-center my-4 mx-2">
            <h3><?php 
           echo $variables['$estudio']; 
           
           ?></h3>
        </div>

        <!-- Menú -->
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <div class="container">
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <a class="navbar-brand d-lg-none" href="inicio">
                    <img src="/version2.0/assets/img/2.png" alt="Logo" height="40">
                </a>

                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <?php foreach ($menuItems as $url => $title): ?>
                            <li class="nav-item me-3">
                            <a class="nav-link <?= ($currentUrl === $url || ($currentUrl === '' && $url === 'inicio')) ? 'active' : '' ?>" href="<?= $url ?>"><?= $title ?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <style>
            .navbar-nav .nav-link.active {
                font-weight: bold;
                color: #003366;
            }

            .navbar-light.bg-light {
                background-color: #f7f7f7 !important;
            }
        </style>

        <!-- Contenido dinámico -->
        <div class="container mt-3">
            <?php
            switch ($currentUrl) {
                case "":
                case "inicio":
                    echo "Bienvenido al estudio INNOQUAL, una evaluación de las características de las instituciones que contribuyen a un mejor desempeño de sus funciones.";
                    break;
                case "informacion":
                    echo "Aquí encontrará información detallada sobre el estudio";
                    break;
                case "encuesta":
                    echo "Bienvenido a la encuesta. Por favor, complete las preguntas a continuación";
                    break;
                case "faq":
                    echo "Preguntas frecuentes relacionadas con el estudio INNOQUAL.";
                    break;
                case "privacidad":
                    echo "Política de privacidad del estudio INNOQUAL.";
                    break;
                case "contactar":
                    echo "Información de contacto para consultas relacionadas con el estudio";
                    break;
                default:
                    echo "";
            }
            ?>
        </div>
    </div>