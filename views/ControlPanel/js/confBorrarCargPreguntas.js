// Función para cargar preguntas en la lista
function cargarPreguntas() {
    fetch("../obtenerPreguntas.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la solicitud: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            const preguntasList = document.getElementById("preguntasList");
            preguntasList.innerHTML = ""; // Limpiar la lista

            // Si no hay preguntas disponibles, mostrar un mensaje
            if (data.length === 0) {
                preguntasList.innerHTML = "<p class='text-muted text-center'>No hay preguntas disponibles.</p>";
                return;
            }

            // Crear tabla
            const table = document.createElement("table");
            table.classList.add("table", "table-bordered",  "table-sm");

            // Encabezado de la tabla
            const thead = document.createElement("thead");
            thead.innerHTML = `
                <tr class="table-primary text-center align-middle">
                    <th class="col fw-bold">ID</th>
                    <th class="col fw-bold">Título</th>
                    <th class="col fw-bold">Nº página</th>
                    <th class="col fw-bold">Tipo</th>
                    <th class="col fw-bold">Acciones</th>
                </tr>
            `;
            table.appendChild(thead);

            // Cuerpo de la tabla
            const tbody = document.createElement("tbody");
                        tbody.classList.add("tbody", "table-group-divider");
const tipoMap = {
                radio: "Radio",
                numberInput: "Entrada numérica",
                checkbox: "Checkbox",
                formSelect: "Radio desplegable",
            };

            data.forEach(pregunta => {
                const row = document.createElement("tr");
                row.innerHTML = `
                    <!-- ID -->
                    <th scope="row" class="align-middle text-center fw-bold text-primary">${pregunta.id}</th>
                    
                    <!-- Título -->
                    <td class="align-middle">${pregunta.titulo}</td>
                    
                    <!-- Número de página -->
                    <td class="align-middle text-center">${pregunta.n_pag}</td>
                    
                    <!-- Tipo -->
                    <td class="align-middle text-center">
                        <span >${tipoMap[pregunta.tipo]}</span>
                    </td>
                    
                    <!-- Acciones -->
                    <td class="align-middle d-flex justify-content-center align-items-center gap-2">
                        <!-- Botón Editar -->
                        <button 
                            class="btn btn-sm btn-warning d-flex align-items-center" 
                            onclick="editarPregunta(${pregunta.id})"
                            title="Editar pregunta"
                        >
                            <i class="fas fa-edit"></i> Editar
                        </button>
                        
                        <!-- Botón Borrar -->
                                                    <button type="button" class="btn btn-danger btn-sm icon-change" onclick="confirmarBorrarPregunta(${pregunta.id})">


                        
                            <i class="fas fa-trash"></i> 
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            preguntasList.appendChild(table);
        })
        .catch(error => {
            console.error("Error al cargar las preguntas:", error);
            document.getElementById("preguntasList").innerHTML = `
                <p class='text-danger text-center'>
                    Error al cargar las preguntas. Inténtalo de nuevo más tarde.
                </p>`;
        });
}

// Función para confirmar el borrado de una pregunta
function confirmarBorrarPregunta(id) {
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    confirmDeleteButton.onclick = function () {
        
        borrarPregunta(id);
        const confirmDeleteModal = document.getElementById('confirmDeleteModal');
        const modalInstance = bootstrap.Modal.getInstance(confirmDeleteModal);
        modalInstance.hide();
    };
    const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
    confirmDeleteModal.show();
}

// Función para borrar una pregunta
function borrarPregunta(id) {
    fetch(`borrarPregunta.php?id=${id}`, { method: "DELETE" })
        .then(response => {
            if (!response.ok) {
                throw new Error("Error en la solicitud: " + response.status);
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                cargarPreguntas(); // Recargar la lista de preguntas
                alert("Pregunta eliminada correctamente.");
            } else {
                alert("Error al eliminar la pregunta.");
            }
        })
        .catch(error => {
            console.error("Error al borrar la pregunta:", error);
            alert("Ocurrió un error al intentar borrar la pregunta. Inténtalo de nuevo más tarde.");
        });
}

// Función para ajustar los parámetros del formulario según el tipo de pregunta
function ajustarParametros() {
    const tipo = document.getElementById("tipo").value;
    const numberInputFields = document.getElementById("numberInputFields");

    // Mostrar u ocultar campos adicionales según el tipo de pregunta
    if (tipo === "numberInput") {
        numberInputFields.style.display = "block";
    } else {
        numberInputFields.style.display = "none";
    }
}
// Función para buscar preguntas
function buscarPregunta() {
    const searchTerm = document.getElementById("searchQuestions").value.toLowerCase();
    const tableBody = document.querySelector("#preguntasList tbody");
    const rows = tableBody.querySelectorAll("tr");

    rows.forEach(row => {
        const cells = row.querySelectorAll("td");
        let matchFound = false;

        cells.forEach(cell => {
            if (cell.textContent.toLowerCase().includes(searchTerm)) {
                matchFound = true;
            }
        });

        // Mostrar u ocultar la fila según el resultado de la búsqueda
        row.style.display = matchFound ? "" : "none";
    });
}