<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <style>
        .form-group {
            margin-bottom: 1rem;
        }

        .btn {
            margin-top: 0.5rem;
        }

        #preguntasList {
            max-height: 40vh;
            overflow-y: auto;
        }
    </style>
</head>

<body>
    <div class="container my-5">
        <h1 class="text-center mb-4">Panel de control (Beta)</h1>
        <form id="preguntaForm">
            <div class="form-group d-flex row ">
                <div class="col-12 col-md-4 ">
                    <label for="preguntaId" class="form-label">ID de la pregunta</label>
                    <input type="number" class="form-control  shadow-sm" id="preguntaId" name="preguntaId" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="n_pag" class="form-label">Número de página</label>
                    <input type="number" class="form-control  shadow-sm" id="n_pag" required>
                </div>

                <div class="col-12 col-md-4">
                    <label for="tipo" class="form-label">Tipo de pregunta</label>
                    <select class="form-control form-select  shadow-sm" id="tipo" required onchange="ajustarParametros()">
                        <option value="radio">Tipo radio</option>
                        <option value="numberInput">Entrada numérica</option>
                        <option value="checkbox">Tipo Checkbox</option>
                        <option value="formSelect">Tipo radio con desplegables</option>
                    </select>
                </div>

            </div>
            <div class="form-group">
                <label for="titulo">Título de la pregunta</label>
                <input type="text" class="form-control  shadow-sm" id="titulo" required>
            </div>
            <div class="form-group">
                <label for="subTitulo">Subtítulo de la pregunta</label>
                <input type="text" class="form-control  shadow-sm" id="subTitulo">
            </div>

            <div id="opciones">
                <div class="form-group">
                    <label>Opciones</label>
                    <div class="input-group mb-2 ">
                        <input type="text" class="form-control  shadow-sm" name="claves[]" placeholder="Clave" required>

                        <input type="text" class="form-control w-75  shadow-sm" name="opciones[]" placeholder="Opción" required>
                    </div>
                </div>
            </div>
            <div id="valores">
                <!-- Campos adicionales para entradas numéricas -->
                <div class="form-group " id="numberInputFields" style="display: none;">
                    <div class="d-flex row p-3 bg-primary-subtle justify-content-between">
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
            <div class="d-flex justify-content-between">
                <button type="button" class="btn btn-secondary" onclick="agregarOpcion()">Añadir Opción</button>
                <button type="submit" class="btn btn-primary ">Guardar Pregunta</button>
            </div>
        </form>
<hr>
        <!-- Lista de preguntas -->
        <div id="preguntasContainer" class="my-4">
            <h2>Preguntas</h2>
            <ul id="preguntasList" class="list-group"></ul>
        </div>

        <!-- Botón para exportar la web -->
        <button class="btn btn-success" onclick="exportarWeb()">Exportar Web</button>
    </div>

    <!-- Inclusión del archivo scripts.js -->
    <script src="scripts.js"></script>
</body>

</html>