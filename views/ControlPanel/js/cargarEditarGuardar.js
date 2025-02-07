// Variable global para rastrear si se está editando una pregunta
let isEditing = false;


// Función para agregar una nueva opción dinámicamente
function agregarOpcion(clave = "", opcion = "") {
    const opcionesDiv = document.getElementById("opciones");

    const nuevaOpcion = document.createElement("div");
    nuevaOpcion.classList.add("input-group", "mb-2");
    nuevaOpcion.innerHTML = `
        <button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarOpcion(this)">
            <i class="fa-solid fa-trash"></i>
        </button>
        <input type="text" class="form-control shadow-sm" name="claves[]" placeholder="Clave" value="${clave}" required>
        <input type="text" class="form-control w-75 shadow-sm" name="opciones[]" placeholder="Opción" value="${opcion}" required>
    `;
    opcionesDiv.appendChild(nuevaOpcion);

    // Asegurarse de que el botón "Agregar Opción" exista solo una vez
    let addButtonContainer = document.querySelector(".add-option-container");
    if (!addButtonContainer) {
        addButtonContainer = document.createElement("div");
        addButtonContainer.classList.add("add-option-container", "my-2");
        addButtonContainer.innerHTML = `
            <a type="button" class="hover-zoom" onclick="agregarOpcion()">
                <i class="fa-xl fa-solid fa-circle-plus"></i>
            </a>
        `;
        opcionesDiv.parentElement.appendChild(addButtonContainer);
    }
}

// Función para eliminar una opción dinámicamente
function eliminarOpcion(button) {
    button.parentElement.remove();
}

// Cargar preguntas al cargar la página
document.addEventListener("DOMContentLoaded", () => {
    cargarPreguntas();
});

// Función para editar una pregunta existente
function editarPregunta(id) {
    fetch(`obtenerPregunta.php?id=${id}`)
        .then(response => response.json())
        .then(pregunta => {
            // Rellenar el formulario con los datos de la pregunta
            document.getElementById("preguntaId").value = pregunta.id;
            document.getElementById("titulo").value = pregunta.titulo;
            document.getElementById("n_pag").value = pregunta.n_pag;
            document.getElementById("tipo").value = pregunta.tipo;
            document.getElementById("subTitulo").value = pregunta.subTitulo;

            const opcionesDiv = document.getElementById("opciones");
            opcionesDiv.innerHTML = ""; // Limpiar opciones previas

            // Agregar las opciones existentes
            Object.keys(pregunta.opciones).forEach(key => {
                agregarOpcion(key, pregunta.opciones[key]);
            });

            if (pregunta.tipo === "numberInput") {
                document.getElementById("min").value = pregunta.valores.min;
                document.getElementById("max").value = pregunta.valores.max;
                document.getElementById("placeholder").value = pregunta.valores.placeholder;
            }

            // Ajustar parámetros del formulario
            ajustarParametros();

            // Indicar que se está editando una pregunta
            isEditing = true;

        });
}

// Función para guardar una pregunta
document.getElementById("preguntaForm").addEventListener("submit", function(event) {
    event.preventDefault();

    // Cambiar texto del botón a "Guardando..."
    const submitButton = document.querySelector('button[type="submit"]');
    submitButton.innerHTML = 'Guardando...';
    submitButton.disabled = true;

    const preguntaId = document.getElementById("preguntaId").value;
    const titulo = document.getElementById("titulo").value;
    const n_pag = document.getElementById("n_pag").value;
    const tipo = document.getElementById("tipo").value;
    const subTitulo = document.getElementById("subTitulo").value;
    const opciones = Array.from(document.querySelectorAll('[name="opciones[]"]')).map(input => input.value);
    const claves = Array.from(document.querySelectorAll('[name="claves[]"]')).map(input => input.value);
    const next_pag = Array.from(document.querySelectorAll('[name="next_pag[]"]')).map(input => input.value);
    const valores = tipo === "numberInput" ? {
        min: document.getElementById("min").value,
        max: document.getElementById("max").value,
        placeholder: document.getElementById("placeholder").value
    } : {};

    fetch("guardarPregunta.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
            id: preguntaId,
            titulo,
            n_pag,
            tipo,
            subTitulo,
            claves,
            opciones,
            next_pag,
            valores
        })
    })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Resetear el formulario después de un delay de 1 segundo
                setTimeout(() => {
                    submitButton.innerHTML = 'Guardar pregunta';
                    submitButton.disabled = false;

                    document.getElementById("preguntaForm").reset();
                    document.getElementById("preguntaId").value = "";
                    document.getElementById("opciones").innerHTML = "";
                    cargarPreguntas();

                    // Restablecer el estado de edición
                    isEditing = false;

                    // Mostrar el botón "Nueva pregunta"
                    document.getElementById("nuevaPreguntaButton").style.display = "inline-block";
                }, 500);
            } else {
                alert("Error al guardar la pregunta");
                submitButton.innerHTML = 'Guardar pregunta';
                submitButton.disabled = false;
            }
        });
});




// Cargar preguntas al cargar la página
document.addEventListener("DOMContentLoaded", cargarPreguntas);