// Función para cargar las jump_rules existentes
function cargarJumpRules(jumpRules) {
    var container = document.getElementById("jumpRulesContainer");
    container.innerHTML = ''; // Limpiar reglas anteriores
    for (const [rango, paginaDestino] of Object.entries(jumpRules)) {
        agregarJumpRule(rango, paginaDestino);
    }
}

// Función para agregar una nueva regla de salto dinámicamente
function agregarJumpRule(rango = '', paginaDestino = '') {
    var container = document.getElementById("jumpRulesContainer");
    var ruleDiv = document.createElement("div");
    ruleDiv.classList.add("input-group","input-group-sm", "mb-2", "align-items-center");
    
    ruleDiv.innerHTML = `
<button type="button" class="btn btn-outline-danger btn-sm" onclick="eliminarJumpRule(this)">
        <i class="fa-solid fa-trash"></i>
    </button>
    <input type="text" class="form-control shadow-sm" name="jump_rules[rango][]" value="${rango}" placeholder="Rango" required>
    <div class="mx-3" >
            <i class="fa-solid fa-arrow-right fa-lg"></i>
    </div>
    <input type="number" class="form-control w-50 shadow-sm" name="jump_rules[paginaDestino][]" value="${paginaDestino}" placeholder="Página destino" required>
    `;
    container.appendChild(ruleDiv);
}

// Función para eliminar una regla de salto dinámicamente
function eliminarJumpRule(button) {
    button.parentElement.remove();
}

// Función para mostrar u ocultar las jump_rules
function mostrarJumpRules() {
    var checkBox = document.getElementById("mostrar-jump-rules");
    var jumpRules = document.getElementById("jump-rules");
    jumpRules.style.display = checkBox.checked ? "block" : "none";
}
