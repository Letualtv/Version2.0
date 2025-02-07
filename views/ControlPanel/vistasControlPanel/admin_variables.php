<?php
// Ruta al archivo de variables
$variablesFile = $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/variables.php';

// Cargar las variables actuales
if (!file_exists($variablesFile)) {
    file_put_contents($variablesFile, "<?php\nreturn [];");
}
$variables = include $variablesFile;

// Función para validar el formato de la clave
function validarClave($clave) {
    return preg_match('/^\$[a-zA-Z0-9_]+$/', $clave);
}

// Acción para listar las variables actuales
if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['accion']) && $_GET['accion'] === 'listar') {
    // Devolver las variables como JSON
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'variables' => $variables]);
    exit;
}

// Acción para agregar o actualizar una variable
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion'])) {
    $accion = $_POST['accion'];

    // Guardar una nueva variable o actualizar una existente
    if ($accion === 'guardar') {
        $clave = trim($_POST['clave']);
        $valor = trim($_POST['valor']);

        if (!empty($clave) && !empty($valor)) {
            // Validar que la clave tenga el formato correcto
            if (validarClave($clave)) {
                $variables[$clave] = $valor;

                // Guardar las variables actualizadas en el archivo PHP
                $contenido = "<?php\nreturn " . var_export($variables, true) . ";";
                file_put_contents($variablesFile, $contenido);

                echo json_encode(['success' => true, 'message' => 'Variable guardada correctamente.']);
                exit;
            } else {
                echo json_encode(['success' => false, 'message' => 'La clave debe tener el formato $nombre.']);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Clave o valor no pueden estar vacíos.']);
            exit;
        }
    }

    // Actualizar una variable existente
    if ($accion === 'actualizar') {
        $claveOriginal = htmlspecialchars(trim($_POST['clave']));
        $nuevaClave = htmlspecialchars(trim($_POST['nuevaClave']));
        $nuevoValor = htmlspecialchars(trim($_POST['valor']));

        if (!empty($claveOriginal) && array_key_exists($claveOriginal, $variables)) {
            // Validar que la nueva clave tenga el formato correcto
            if (!validarClave($nuevaClave)) {
                echo json_encode(['success' => false, 'message' => 'La nueva clave debe tener el formato $nombre.']);
                exit;
            }

            // Eliminar la clave original y agregar la nueva clave con el nuevo valor
            unset($variables[$claveOriginal]);
            $variables[$nuevaClave] = $nuevoValor;

            // Guardar las variables actualizadas en el archivo PHP
            $contenido = "<?php\nreturn " . var_export($variables, true) . ";";
            file_put_contents($variablesFile, $contenido);

            echo json_encode(['success' => true, 'message' => 'Variable actualizada correctamente.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'La clave original no existe.']);
            exit;
        }
    }

    // Borrar una variable
    if ($accion === 'borrar') {
        $clave = htmlspecialchars(trim($_POST['clave']));

        if (array_key_exists($clave, $variables)) {
            unset($variables[$clave]);

            // Guardar las variables actualizadas en el archivo PHP
            $contenido = "<?php\nreturn " . var_export($variables, true) . ";";
            file_put_contents($variablesFile, $contenido);

            echo json_encode(['success' => true, 'message' => 'Variable eliminada correctamente.']);
            exit;
        } else {
            echo json_encode(['success' => false, 'message' => 'La clave no existe.']);
            exit;
        }
    }
}
?>

<link rel="stylesheet" href="../style.css">

<!-- Formulario para agregar variables -->
<div class="col-12 card mb-2">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-globe me-2"></i>Parámetros Globales</h5>
            </div>
            <div class="bg-body p-2">
                <form id="globalParamsForm">
                    <div class="row justify-content-between align-items-end">
                        <div class="col">
                            <label for="clave" class="form-label">Clave</label>
                            <input type="text" class="form-control shadow-sm" id="clave" placeholder="Ej: $institucion" required>
                        </div>
                        <div class="col-8">
                            <label for="valor" class="form-label">Valor</label>
                            <input type="text" class="form-control shadow-sm" id="valor" placeholder="Ej: Universidad de Madrid" required>
                        </div>
                        <div class="col d-flex align-items-end">
                            <button type="submit" class="btn btn-success">
                                <i class="fa-solid fa-check me-2"></i>Insertar
                            </button>
                        </div>
                    </div>
                </form>

                <!-- Lista de variables disponibles -->
                <div class="mt-4">
                    <h6 class="mb-3">Variables Disponibles:</h6>
                    <div id="variablesList" class="list-group"></div>
                </div>
            </div>
        </div>

        <!-- Modal de Confirmación de Borrado -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirmar Borrado</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        ¿Estás seguro de que deseas borrar esta variable?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Borrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal de Edición -->
        <div class="modal fade" id="editVariableModal" tabindex="-1" aria-labelledby="editVariableModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVariableModalLabel">Editar Variable</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editVariableForm">
                            <div class="mb-3">
                                <label for="editClave" class="form-label">Clave</label>
                                <input type="text" class="form-control" id="editClave" placeholder="Clave (ej. $nombre)" required>
                            </div>
                            <div class="mb-3">
                                <label for="editValor" class="form-label">Valor</label>
                                <input type="text" class="form-control" id="editValor" placeholder="Valor" required>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="button" class="btn btn-primary" id="saveEditButton">Guardar Cambios</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Contenedor de Notificaciones -->
        <div id="notificationContainer" class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050;"></div>
    
<script src="../js/admin_variables.js"></script>