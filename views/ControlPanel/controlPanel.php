<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="style.css">

</head>

<body>
    <div class="container-fluid my-5 d-flex row">
        <h1 class="text-center">Panel de control (Pre-Alpha)</h1>
        <hr class="my-5">
        <div class="col-12 col-lg-6 order-2 order-lg-1">
        <div id="preguntasContainer">
    <ul id="preguntasList" class="list-group"></ul>
    <!-- Botón desplegable para exportar en diferentes formatos -->
    <div class="btn-group my-3">
      <button type="button" class="btn btn-success dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Exportar
      </button>
      <ul class="dropdown-menu">
        <li><a class="dropdown-item" href="#" onclick="exportarJSON()">Exportar JSON</a></li>
        <li><a class="dropdown-item" href="#" onclick="exportarCSV()">Exportar CSV</a></li>
        <li><a class="dropdown-item" href="#" onclick="exportarExcel()">Exportar Excel</a></li>
        <li><a class="dropdown-item" href="#" onclick="exportarPDF()">Exportar PDF</a></li>
      </ul>
    </div>
  </div>
        </div>


        <div class="col-12 col-lg-6 order-1 order-lg-2">
            <form id="preguntaForm">
                <div class="form-group d-flex row">
                    <div class="col-12 col-md-4">
                        <label for="preguntaId" class="form-label">ID de la pregunta</label>
                        <input type="number" class="form-control shadow-sm" id="preguntaId" name="preguntaId" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="n_pag" class="form-label">Número de página</label>
                        <input type="number" class="form-control shadow-sm" id="n_pag" required>
                    </div>

                    <div class="col-12 col-md-4">
                        <label for="tipo" class="form-label">Tipo de pregunta</label>
                        <select class="form-control form-select shadow-sm" id="tipo" required onchange="ajustarParametros()">
                            <option value="radio">Tipo radio</option>
                            <option value="numberInput">Entrada numérica</option>
                            <option value="checkbox">Tipo Checkbox</option>
                            <option value="formSelect">Tipo radio con desplegables</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="titulo">Título de la pregunta</label>
                    <input type="text" class="form-control shadow-sm" id="titulo" required>
                </div>

                <div class="form-group">
                    <label for="subTitulo">Subtítulo de la pregunta</label>
                    <input type="text" class="form-control shadow-sm" id="subTitulo">
                </div>
                <div id="valores">
                    <!-- Campos adicionales para entradas numéricas -->
                    <div class="form-group" id="numberInputFields" style="display: none;">
                        <div class="d-flex row bg-primary-subtle py-2 justify-content-between">
                            <h6>Valores para la entrada numérica:</h6>
                            <div class="col-12 col-md-auto">
                                <label for="min">Valor mínimo</label>
                                <input type="number" class="form-control" id="min" name="valores[min]">
                            </div>
                            <div class="col-12 col-md-auto">
                                <label for="max">Valor máximo</label>
                                <input type="number" class="form-control" id="max" name="valores[max]">
                            </div>
                            <div class="col-12 col-md-auto">
                                <label for="placeholder">Placeholder</label>
                                <input type="text" class="form-control" id="placeholder" name="valores[placeholder]">
                            </div>
                        </div>
                    </div>
                </div>

                <div id="opciones add-option-container">
                    <div class="form-group">
                        <label>Opciones</label>
                        <div class="input-group my-2">
                            <button type="button" class="btn btn-outline-danger btn-sm shadow-sm icon-change" onclick="eliminarOpcion(this)"><i class="fa-solid fa-trash"></i><i class="fa-solid fa-trash-arrow-up"></i></button>
                            <input type="text" class="form-control shadow-sm" name="claves[]" placeholder="Clave" required>
                            <input type="text" class="form-control w-75 shadow-sm" name="opciones[]" placeholder="Opción" required>
                        </div>
                        <div id="opciones">
                            <!-- Opciones se añadirán aquí dinámicamente -->
                        </div>

                    </div>
                    <div class="add-option-container my-2">
                        <a type="button" class=" hover-zoom" onclick="agregarOpcion()"><i class="fa-xl fa-solid fa-circle-plus"></i></a>
                    </div>
                </div>


                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-success " onclick="mostrarFormularioNuevaPregunta()">
                        Nueva pregunta
                    </button>
                    <button type="submit" class="btn btn-primary">Guardar pregunta</button>
                </div>
            </form>
        </div>
    </div>




    <!-- Modal de confirmación de borrado -->
    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmDeleteModalLabel">Confirme borrado de pregunta</h5>
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


    <!-- Scripts de JS y kit de iconos -->
    <script src="https://kit.fontawesome.com/5b9c44b176.js" crossorigin="anonymous"></script>
    <script src="./js/mostrarPregunta"></script>
    <script src="./js/agregarEliminar.js"></script>
    <script src="./js/cargarEditarBorrar.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>

    <!-- Bibliotecas para exportar archivos -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://unpkg.com/jspdf@latest/dist/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.25/jspdf.plugin.autotable.min.js"></script>

    <script src="./js/exportar.js"></script>

</body>

</html>