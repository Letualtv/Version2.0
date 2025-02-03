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
          }, 500);
        } else {
          alert("Error al guardar la pregunta");
          submitButton.innerHTML = 'Guardar pregunta';
          submitButton.disabled = false;
        }
      });
  });
  
  function cargarPreguntas() {
    fetch("obtenerPreguntas.php")
      .then(response => response.json())
      .then(data => {
        const preguntasTable = document.getElementById("preguntasList");
        preguntasTable.innerHTML = "";
  
        const table = document.createElement("table");
        table.classList.add("table", "table-bordered", "table-hover", "table-sm");
  
        const thead = document.createElement("thead");
        thead.innerHTML = `
          <tr class="table-primary text-center align-middle">
            <th class="col">ID</th>
            <th class="col col-md-8">Título</th>
            <th class="col">Nº página</th>
            <th class="col">Tipo</th>
            <th class="col">Acciones</th>
          </tr>
        `;
        table.appendChild(thead);
  
        const tbody = document.createElement("tbody");
        const tipoMap = {
          radio: "Radio",
          numberInput: "Entrada numérica",
          checkbox: "Checkbox",
          formSelect: "Radio desplegable",
        };
        data.forEach(pregunta => {
          const row = document.createElement("tr");
          row.innerHTML = `
            <td class="align-middle text-center fw-bold">${pregunta.id}</td>
            <td class="align-middle">${pregunta.titulo}</td>
            <td class="align-middle text-center">${pregunta.n_pag}</td>
            <td class="align-middle text-center">${tipoMap[pregunta.tipo]}</td>
            <td class="align-middle d-flex justify-content-center align-items-center w-100">
              <div class="d-flex w-100">
                <button class="btn btn-sm btn-warning me-2 flex-fill d-flex justify-content-center align-items-center" onclick="editarPregunta(${pregunta.id})">
                  <i class="fas fa-edit"></i> <span class="ms-2">Editar</span>
                </button>
                <button type="button" class="btn btn-danger btn-sm icon-change" onclick="confirmarBorrarPregunta(${pregunta.id})">
                  <i class="fa-solid fa-trash"></i><i class="fa-solid fa-trash-arrow-up"></i>
                </button>
              </div>
            </td>
          `;
          tbody.appendChild(row);
        });
  
        table.appendChild(tbody);
        preguntasTable.appendChild(table);
      });
  }
  
  
  function borrarPregunta(id) {
    fetch(`borrarPregunta.php?id=${id}`, { method: "DELETE" })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
            cargarPreguntas();
        } else {
            alert("Error al borrar la pregunta");
        }
    });
}
function confirmarBorrarPregunta(id) {
  const confirmDeleteButton = document.getElementById('confirmDeleteButton');
  confirmDeleteButton.onclick = function() {
    borrarPregunta(id);
    const confirmDeleteModal = document.getElementById('confirmDeleteModal');
    const modalInstance = bootstrap.Modal.getInstance(confirmDeleteModal);
    modalInstance.hide();
  };
  const confirmDeleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
  confirmDeleteModal.show();
}
  
  
  
  
  function editarPregunta(id) {
    fetch(`obtenerPregunta.php?id=${id}`)
      .then(response => response.json())
      .then(pregunta => {
        document.getElementById("preguntaId").value = pregunta.id;
        document.getElementById("titulo").value = pregunta.titulo;
        document.getElementById("n_pag").value = pregunta.n_pag;
        document.getElementById("tipo").value = pregunta.tipo;
        document.getElementById("subTitulo").value = pregunta.subTitulo;
        const opcionesDiv = document.getElementById("opciones");
        opcionesDiv.innerHTML = "";
        // Limpiar opciones antes de agregar
        document.querySelector(".add-option-container").remove();
        Object.keys(pregunta.opciones).forEach(key => {
          agregarOpcion(key, pregunta.opciones[key]);
        });
        if (pregunta.tipo === "numberInput") {
          document.getElementById("min").value = pregunta.valores.min;
          document.getElementById("max").value = pregunta.valores.max;
          document.getElementById("placeholder").value = pregunta.valores.placeholder;
        }
        ajustarParametros();
      });
  }
  

  
  function ajustarParametros() {
    const tipo = document.getElementById("tipo").value;
    const numberInputFields = document.getElementById("numberInputFields");
    if (tipo === "numberInput") {
      numberInputFields.style.display = "block";
    } else {
      numberInputFields.style.display = "none";
    }
  }
  
  document.addEventListener("DOMContentLoaded", cargarPreguntas);
  