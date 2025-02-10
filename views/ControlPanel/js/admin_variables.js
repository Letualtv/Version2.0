document.addEventListener("DOMContentLoaded", () => {
    const globalParamsForm = document.getElementById("globalParamsForm");
    const variablesList = document.getElementById("variablesList");

    // Función para cargar las variables
    function cargarVariables() {
        variablesList.innerHTML = "<p class='text-muted text-center'>Cargando variables...</p>";
        fetch("./vistasControlPanel/admin_variables.php?accion=listar")
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    variablesList.innerHTML = ""; // Limpiar la lista
                    if (Object.keys(data.variables).length === 0) {
                        variablesList.innerHTML =
                            "<p class='text-muted text-center'>No hay variables disponibles.</p>";
                        return;
                    }
                    Object.entries(data.variables).forEach(([clave, valor]) => {
                        agregarVariable(clave, valor);
                    });
                } else {
                    variablesList.innerHTML =
                        "<p class='text-danger text-center'>Error al cargar las variables.</p>";
                }
            })
            .catch(error => {
                console.error("Error al cargar las variables:", error);
                variablesList.innerHTML =
                    "<p class='text-danger text-center'>Error al cargar las variables.</p>";
            });
    }

    // Función para mostrar notificaciones
    function mostrarNotificacion(mensaje, tipo = "success", duracion = 5000) {
        // Crear el contenedor si no existe
        let notificationContainer = document.getElementById("notificationContainer");
        if (!notificationContainer) {
            notificationContainer = document.createElement("div");
            notificationContainer.id = "notificationContainer";
            notificationContainer.className = "toast-container position-fixed top-0 end-0 p-3";
            document.body.appendChild(notificationContainer);
        }
    
        // Crear el elemento de notificación
        const notification = document.createElement("div");
        notification.classList.add("toast", "align-items-center", "text-white", `bg-${tipo}`, "border-0", "mb-3");
        notification.setAttribute("role", "alert");
        notification.setAttribute("aria-live", "assertive");
        notification.setAttribute("aria-atomic", "true");
    
        // Contenido de la notificación
        notification.innerHTML = `
            <div class="d-flex">
                <div class="toast-body">${mensaje}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        `;
    
        // Agregar la notificación al contenedor
        notificationContainer.appendChild(notification);
    
        // Inicializar el toast de Bootstrap
        const toast = new bootstrap.Toast(notification, { delay: duracion });
        toast.show();
    
        // Eliminar la notificación después de que se oculte
        notification.addEventListener("hidden.bs.toast", () => {
            notification.remove();
        });
    }

    // Función para agregar una variable a la vista
    function agregarVariable(clave = "", valor = "") {
        const nuevaVariable = document.createElement("div");
        nuevaVariable.classList.add(
            "d-flex",
            "align-items-center",
            "input-group",
            "shadow-sm",
            "mb-2"
        );

        nuevaVariable.innerHTML = `
            <button type="button" class="btn btn-outline-danger" onclick="eliminarVariable(this)">
                <i class="fa-solid fa-trash"></i>
            </button>
            <input type="text" class="form-control clave-input " placeholder="Clave (ej. $nombre)" value="${clave}" readonly>
            <input type="text" class="form-control valor-input " placeholder="Valor disabled" value="${valor}" readonly>
            <button type="button" class="btn btn-primary" onclick="editarVariable('${clave}', '${valor}')">Editar</button>
        `;

        variablesList.appendChild(nuevaVariable);
    }

    // Función para guardar una variable
    globalParamsForm.addEventListener("submit", function (event) {
        event.preventDefault();
        const clave = document.getElementById("clave").value.trim();
        const valor = document.getElementById("valor").value.trim();

        if (!validarClave(clave)) {
            mostrarNotificacion("La clave debe tener el formato $nombre.", "danger");
            return;
        }

        fetch("./vistasControlPanel/admin_variables.php", {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: `accion=guardar&clave=${encodeURIComponent(clave)}&valor=${encodeURIComponent(valor)}`
        })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    mostrarNotificacion(data.message, "success");
                    cargarVariables(); // Recargar la lista
                } else {
                    mostrarNotificacion(data.message, "danger");
                }
            })
            .catch(error => {
                console.error("Error al guardar la variable:", error);
                mostrarNotificacion(`Ocurrió un error al guardar la variable: ${error.message}`, "danger");
            });
    });

    // Función para eliminar una variable
    window.eliminarVariable = function (elemento) {
        const clave = elemento.parentElement.querySelector(".clave-input").value.trim();
        const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
        const confirmDeleteButton = document.getElementById('confirmDeleteButton');

        confirmDeleteButton.onclick = function () {
            fetch("./vistasControlPanel/admin_variables.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `accion=borrar&clave=${encodeURIComponent(clave)}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarNotificacion(data.message, "success");
                        cargarVariables(); // Recargar la lista
                    } else {
                        mostrarNotificacion(data.message, "danger");
                    }
                })
                .catch(error => {
                    console.error("Error al borrar la variable:", error);
                    mostrarNotificacion(`Ocurrió un error al borrar la variable: ${error.message}`, "danger");
                })
                .finally(() => {
                    confirmDeleteModal.hide(); // Ocultar el modal después de la acción
                });
        };

        confirmDeleteModal.show(); // Mostrar el modal
    };

    // Función para editar una variable
    window.editarVariable = function (clave, valor) {
        const editClaveInput = document.getElementById("editClave");
        const editValorInput = document.getElementById("editValor");
        const saveEditButton = document.getElementById("saveEditButton");

        // Rellenar los campos del modal con los valores actuales
        editClaveInput.value = clave;
        editValorInput.value = valor;

        const editVariableModal = new bootstrap.Modal(document.getElementById('editVariableModal'));
        editVariableModal.show();

        saveEditButton.onclick = function () {
            const nuevaClave = editClaveInput.value.trim();
            const nuevoValor = editValorInput.value.trim();

            if (!validarClave(nuevaClave)) {
                mostrarNotificacion(`La clave "${nuevaClave}" debe tener el formato $nombre.`, "danger");
                return;
            }

            fetch("./vistasControlPanel/admin_variables.php", {
                method: "POST",
                headers: { "Content-Type": "application/x-www-form-urlencoded" },
                body: `accion=actualizar&clave=${encodeURIComponent(clave)}&nuevaClave=${encodeURIComponent(nuevaClave)}&valor=${encodeURIComponent(nuevoValor)}`,
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        mostrarNotificacion(data.message, "success");
                        cargarVariables(); // Recargar la lista
                        editVariableModal.hide(); // Ocultar el modal
                    } else {
                        mostrarNotificacion(data.message, "danger");
                    }
                })
                .catch(error => {
                    console.error("Error al actualizar la variable:", error);
                    mostrarNotificacion(`Ocurrió un error al actualizar la variable: ${error.message}`, "danger");
                });
        };
    };

    // Función para validar el formato de la clave
    function validarClave(clave) {
        const regex = /^\$[a-zA-Z0-9_]+$/;
        return regex.test(clave);
    }

    // Cargar las variables al cargar la página
    cargarVariables();
});