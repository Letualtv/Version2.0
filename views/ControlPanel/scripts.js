function agregarOpcion(clave = "", opcion = "") {
  const opcionesDiv = document.getElementById("opciones");

  // Crear la nueva opción
  const nuevaOpcion = document.createElement("div");
  nuevaOpcion.classList.add("input-group", "mb-2");
  nuevaOpcion.innerHTML = `
    <button type="button" class="btn btn-outline-danger btn-sm icon-change" onclick="eliminarOpcion(this)"><i class="fa-solid fa-trash"></i><i class="fa-solid fa-trash-arrow-up"></i></button>
    <input type="text" class="form-control shadow-sm" name="claves[]" placeholder="Clave" value="${clave}" required>
    <input type="text" class="form-control w-75 shadow-sm" name="opciones[]" placeholder="Opción" value="${opcion}" required>
  `;
  opcionesDiv.appendChild(nuevaOpcion);

  // Añadir el botón "Agregar Opción" si no existe
  let addButtonContainer = document.querySelector(".add-option-container");
  if (!addButtonContainer) {
    addButtonContainer = document.createElement("div");
    addButtonContainer.classList.add("add-option-container", "my-2");
    addButtonContainer.innerHTML = `
      <a type="button" class="hover-zoom" onclick="agregarOpcion()"><i class="fa-xl fa-solid fa-circle-plus"></i></a>
    `;
    opcionesDiv.parentElement.appendChild(addButtonContainer);
  }
}

function mostrarFormularioNuevaPregunta() {
  document.getElementById("preguntaForm").scrollIntoView({ behavior: 'smooth' });
  document.getElementById("preguntaForm").reset();
  document.getElementById("preguntaId").value = "";
  document.getElementById("opciones").innerHTML = "";

  // Eliminar el botón "Agregar Opción" si ya existe
  const existingAddButton = document.querySelector(".add-option-container");
  if (existingAddButton) {
    existingAddButton.remove();
  }

  // Añadir el botón "Agregar Opción"
  const opcionesDiv = document.getElementById("opciones");
  const addButtonContainer = document.createElement("div");
  addButtonContainer.classList.add("add-option-container", "my-2");
  addButtonContainer.innerHTML = `
    <a type="button" class="hover-zoom" onclick="agregarOpcion()"><i class="fa-xl fa-solid fa-circle-plus"></i></a>
  `;
  opcionesDiv.parentElement.appendChild(addButtonContainer);
  
  ajustarParametros();
}


document
  .getElementById("preguntaForm")
  .addEventListener("submit", function (event) {
    event.preventDefault();
    const preguntaId = document.getElementById("preguntaId").value;
    const titulo = document.getElementById("titulo").value;
    const n_pag = document.getElementById("n_pag").value;
    const tipo = document.getElementById("tipo").value;
    const subTitulo = document.getElementById("subTitulo").value;
    const opciones = Array.from(
      document.querySelectorAll('[name="opciones[]"]')
    ).map((input) => input.value);
    const claves = Array.from(
      document.querySelectorAll('[name="claves[]"]')
    ).map((input) => input.value);
    const next_pag = Array.from(
      document.querySelectorAll('[name="next_pag[]"]')
    ).map((input) => input.value);
    const valores =
      tipo === "numberInput"
        ? {
            min: document.getElementById("min").value,
            max: document.getElementById("max").value,
            placeholder: document.getElementById("placeholder").value,
          }
        : {};

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
        valores,
      }),
    })
      .then((response) => response.json())
      .then((data) => {
        if (data.success) {
          alert("Pregunta guardada con éxito");
          document.getElementById("preguntaForm").reset();
          document.getElementById("preguntaId").value = "";
          document.getElementById("opciones").innerHTML = "";
          cargarPreguntas();
        } else {
          alert("Error al guardar la pregunta");
        }
      });
  });

function cargarPreguntas() {
  fetch("obtenerPreguntas.php")
    .then((response) => response.json())
    .then((data) => {
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
      data.forEach((pregunta) => {
        const row = document.createElement("tr");
        row.innerHTML = `
          <td class="align-middle text-center fw-bold">${pregunta.id}</td>
          <td class="align-middle">${pregunta.titulo}</td>
          <td class="align-middle text-center">${pregunta.n_pag}</td>
          <td class="align-middle text-center">${tipoMap[pregunta.tipo]}</td>
          <td class="align-middle d-flex justify-content-center align-items-center w-100">
            <div class="d-flex w-100 ">
                <button class="btn btn-sm btn-warning me-2 flex-fill d-flex justify-content-center align-items-center" onclick="editarPregunta(${pregunta.id})">
                    <i class="fas fa-edit"></i> <span class="ms-2">Editar</span>
                </button>
                <button type="button" class="btn btn-danger btn-sm  icon-change" onclick="borrarPregunta(${pregunta.id})"><i class="fa-solid fa-trash"></i><i class="fa-solid fa-trash-arrow-up"></i></button>
            </div>
          </td>
        `;
        tbody.appendChild(row);
      });

      // Crear la fila para el botón "Agregar nueva pregunta"
      const addQuestionRow = document.createElement("tr");
      addQuestionRow.innerHTML = `
        <td colspan="5" class="text-center">
          <button type="button" class="btn btn-outline-success btn-sm btn-animado" onclick="mostrarFormularioNuevaPregunta()">
            <i class="fa-regular fa-circle-plus"></i> Nueva pregunta
          </button>
        </td>
      `;
      tbody.appendChild(addQuestionRow);

      table.appendChild(tbody);
      preguntasTable.appendChild(table);
    });
}

function mostrarFormularioNuevaPregunta() {
  document.getElementById("preguntaForm").scrollIntoView({ behavior: 'smooth' });
  document.getElementById("preguntaForm").reset();
  document.getElementById("preguntaId").value = "";
  document.getElementById("opciones").innerHTML = "";
  ajustarParametros();
}

function editarPregunta(id) {
  fetch(`obtenerPregunta.php?id=${id}`)
    .then((response) => response.json())
    .then((pregunta) => {
      document.getElementById("preguntaId").value = pregunta.id;
      document.getElementById("titulo").value = pregunta.titulo;
      document.getElementById("n_pag").value = pregunta.n_pag;
      document.getElementById("tipo").value = pregunta.tipo;
      document.getElementById("subTitulo").value = pregunta.subTitulo;
      const opcionesDiv = document.getElementById("opciones");
      opcionesDiv.innerHTML = "";
      Object.keys(pregunta.opciones).forEach((key) => {
        agregarOpcion(key, pregunta.opciones[key]);
      });
      if (pregunta.tipo === "numberInput") {
        document.getElementById("min").value = pregunta.valores.min;
        document.getElementById("max").value = pregunta.valores.max;
        document.getElementById("placeholder").value =
          pregunta.valores.placeholder;
      }
      ajustarParametros();
    });
}

function borrarPregunta(id) {
  fetch(`borrarPregunta.php?id=${id}`, { method: "DELETE" })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        alert("Pregunta borrada con éxito");
        cargarPreguntas();
      } else {
        alert("Error al borrar la pregunta");
      }
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

function exportarWeb() {
  fetch("exportarWeb.php")
    .then((response) => response.blob())
    .then((blob) => {
      const url = window.URL.createObjectURL(blob);
      const a = document.createElement("a");
      a.style.display = "none";
      a.href = url;
      a.download = "web_export.zip";
      document.body.appendChild(a);
      a.click();
      window.URL.revokeObjectURL(url);
    });
}

document.addEventListener("DOMContentLoaded", cargarPreguntas);
