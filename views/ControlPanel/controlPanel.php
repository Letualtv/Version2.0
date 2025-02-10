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
    <img src="/version2.0/public/img/2.png" alt="" width="180"  class="d-inline-block align-text-top ms-auto" >
  </div>
</nav>




    <div class="row d-flex">
        <!-- Lista de Preguntas -->
        <div class="col-12 col-lg-6">
            <div class="card ">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-stream me-2"></i>Listado de preguntas</h5>
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
            <!-- Formulario de Pregunta -->
            <div class=" card mb-2">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-list-ul me-2"></i></i>Agregar / modificar pregunta</h5>
                </div>
                <div class="bg-body p-2">
                    <form id="preguntaForm">
                        <div class="row g-3">
                            <!-- ID de la Pregunta -->
                            <div class="col-md-4">
                                <label for="preguntaId" class="form-label">ID de la pregunta</label>
                                <input type="number" class="form-control shadow-sm" id="preguntaId" name="preguntaId" required>
                            </div>
                            <!-- Número de Página -->
                            <div class="col-md-4">
                                <label for="n_pag" class="form-label">Número de página</label>
                                <input type="number" class="form-control shadow-sm" id="n_pag" required>
                            </div>
                            <!-- Tipo de Pregunta -->
                            <div class="col-md-4">
                                <label for="tipo" class="form-label">Tipo de pregunta</label>
                                <select class="form-select shadow-sm" id="tipo" required onchange="ajustarParametros()">
                                    <option value="radio">Radio</option>
                                    <option value="numberInput">Entrada numérica</option>
                                    <option value="checkbox">Checkbox</option>
                                    <option value="formSelect">Radio desplegable</option>
                                </select>
                            </div>
                        </div>

                        <!-- Título y Subtítulo -->
                        <div class="row g-3 mt-3">
                            <div class="col-md-6">
                                <label for="titulo" class="form-label">Título de la pregunta</label>
                                <input type="text" class="form-control shadow-sm" id="titulo" required>
                            </div>
                            <div class="col-md-6">
                                <label for="subTitulo" class="form-label">Subtítulo de la pregunta</label>
                                <input type="text" class="form-control shadow-sm" id="subTitulo">
                            </div>
                        </div>

                        <!-- Campos Adicionales para Entradas Numéricas -->
                        <div id="valores" class="mt-3">
                            <div id="numberInputFields" class="bg-light p-3 rounded" style="display: none;">
                                <h6 class="text-muted">Valores para la entrada numérica:</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label for="min" class="form-label">Valor mínimo</label>
                                        <input type="number" class="form-control" id="min" name="valores[min]">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="max" class="form-label">Valor máximo</label>
                                        <input type="number" class="form-control" id="max" name="valores[max]">
                                    </div>
                                    <div class="col-md-4">
                                        <label for="placeholder" class="form-label">Placeholder</label>
                                        <input type="text" class="form-control" id="placeholder" name="valores[placeholder]">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Opciones Dinámicas -->
                        <div id="opciones" class="mt-3">
                            <label class="form-label">Opciones:</label>
                            <div id="opcionesContainer"></div>
                        </div>
                        <div class="add-option-container my-2">
                            <a type="button" class="btn btn-outline-primary hover-zoom" onclick="agregarOpcion()">
                                <i class="fa-solid fa-plus"></i> Agregar opción
                            </a>
                        </div>

                        <!-- Botón Guardar -->
                        <div class="d-flex justify-content-end mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fa-solid fa-save me-2"></i>Guardar pregunta
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Variables Generales -->

            <?php include './vistasControlPanel/admin_variables.php';


            ?>

            <!-- Flujo de Encuesta -->
            <div class="col-12 card mb-2">
                <div class="card-header">
                    <h5 class="mb-0"><i class="fa-solid fa-shuffle me-2"></i></i>Flujo de Encuesta</h5>
                </div>
                <div class="bg-body p-2">
                    <form id="surveyFlowForm">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label for="flowOrder" class="form-label">Orden de las páginas</label>
                                <input type="text" class="form-control shadow-sm" id="flowOrder" placeholder="Ej: 1,2,3,4" required>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-check me-2"></i>Guardar cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmación de Borrado -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Borrado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    ¿Estás seguro de que deseas borrar esta pregunta?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDeleteButton">Borrar</button>
                </div>
            </div>
        </div>
    </div>
    <footer class="bg-light  text-lg-end mt-5">
        <div class="container-fluid p-2 text-muted">
        Desarrollado con 	&#x2764; por <a href="https://github.com/Letualtv/" target="_blank" class="">Antonio Pulido</a>
        </div>
    </footer>
    <!-- Scripts -->
    <script src="./js/agregarEliminar.js"></script>
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