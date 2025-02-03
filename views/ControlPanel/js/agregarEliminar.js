function agregarOpcion(clave = "", opcion = "") {
    const opcionesDiv = document.getElementById("opciones");
  
    // Crear la nueva opción
    const nuevaOpcion = document.createElement("div");
    nuevaOpcion.classList.add("input-group", "mb-2");
    nuevaOpcion.innerHTML = `
      <button type="button" class="btn btn-outline-danger btn-sm icon-change" onclick="eliminarOpcion(this)">
        <i class="fa-solid fa-trash"></i><i class="fa-solid fa-trash-arrow-up"></i>
      </button>
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
  
  function eliminarOpcion(button) {
    button.parentElement.remove();
  }
  