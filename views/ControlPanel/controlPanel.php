<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Estilos Personalizados -->
    <link rel="stylesheet" href="style.css">
</head>

<body class="container-fluid d-flex flex-column min-vh-100">
    <nav class="navbar navbar-light my-2">
        <div class="container-fluid">
            <div class="navbar-brand" href="#">
                <span class="h4">Panel de control</span> - <span class="font-monospace"><?php include 'visualizadorVersion.php'; ?></span>
            </div>
            <img src="/version2.0/public/img/2.png" alt="" width="180" class="d-inline-block align-text-top ms-auto">
        </div>
    </nav>




    <div class="row d-flex">
        <!-- Lista de Preguntas -->
        <div class="col-12 col-lg-6">
            <div class="card ">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-stream me-2"></i>Listado de preguntas</h5>
                </div>
                <!-- Barra de Búsqueda -->
<div class="input-group p-3">
    <input type="text" class="form-control shadow-sm" id="searchQuestions" placeholder="Buscar pregunta por título, ID o subtítulo...">
    <button class="btn btn-outline-secondary" type="button" onclick="buscarPregunta()">Buscar</button>
</div>
                <ul id="preguntasList" class="list-group list-group-flush"></ul>
                <div class="bg-body py-2 justify-content-end d-flex">
                    <!-- Botón Exportar -->
                    <button type="button" class="btn btn-success m-2 dropdown-toggle ms-auto " data-bs-toggle="dropdown">
                        Exportar
                    </button>
                    <ul class="dropdown-menu ">
                        <li><a class="dropdown-item" href="#" onclick="exportarJSON()">Exportar JSON</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarCSV()">Exportar CSV</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarExcel()">Exportar Excel</a></li>
                        <li><a class="dropdown-item" href="#" onclick="exportarPDF()">Exportar PDF</a></li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-12 ">

            <?php include './vistasControlPanel/modificar_pregunta.php';   ?>


            <?php include './vistasControlPanel/admin_variables.php';


            ?>

            
        </div>
    </div>

    <!-- Modal de Confirmación de Borrado -->
    
            <?php include './vistasControlPanel/modal_borrado.php';   ?>

    <footer class="bg-light  text-lg-end mt-5">
        <div class="container-fluid p-2 text-muted">
            Desarrollado con &#x2764; por <a href="https://github.com/Letualtv/" target="_blank" class="">Antonio Pulido</a>
        </div>
    </footer>
    <!-- Scripts -->
    <script src="./js/agregarEliminar.js"></script>
    <script src="./js/jumpRules.js"></script>
    <script src="./js/admin_variables.js"></script>
    <script src="./js/confBorrarCargPreguntas.js"></script>
    <script src="./js/cargarEditarGuardar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>
    <script src="./js/exportar.js"></script>

</body>

</html>