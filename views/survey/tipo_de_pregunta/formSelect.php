<?php
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    foreach ($pregunta['opciones'] as $clave => $opcion) {
        $checked = '';  // Inicializa la variable para verificar si la opción debe estar marcada
        $selectVisible = 'display: none;';  // Inicializa la visibilidad del select asociado

        // Verifica si hay una respuesta guardada en la sesión para esta pregunta
        if (isset($_SESSION['respuestas'][$pregunta['id']]) && $_SESSION['respuestas'][$pregunta['id']] == $clave) {
            $checked = 'checked';  // Marca la opción si coincide con la respuesta guardada
            $selectVisible = 'display: block;';  // Muestra el select asociado si el radio está marcado
        }

        echo "
        <div class='form-check pb-1'>
            <input class='form-check-input main-radio' 
                   type='radio' required
                   name='" . $pregunta['id'] . "'  
                   id='radio-" . $pregunta['id'] . "-" . $clave . "' 
                   value='" . $clave . "' 
                   onchange='toggleSelects(this)' $checked>
            <label class='form-check-label' for='radio-" . $pregunta['id'] . "-" . $clave . "'>" . $opcion['label'] . "</label>
        </div>
        ";

        // Verificar si hay un select asociado a la opción
        if (isset($opcion['subLabel']) && count($opcion['subLabel']) > 0) {
            echo "
            <!-- Select asociado al radio -->
            <div class='select-container ms-4 mt-2' type='select'
                 id='select-container-" . $pregunta['id'] . "-" . $clave . "'  
                 style='$selectVisible'>
                <select class='form-select mb-2 w-auto' 
                        id='select-" . $pregunta['id'] . "-" . $clave . "' 
                        name='" . $pregunta['id'] . "'>";  // Mantén el `name` del select sin cambios

            // Asegurarse de que la opción vacía no sea seleccionada por defecto
            echo "<option class='text-wrap mx-auto'  value='' disabled selected>Seleccione una opción</option>";

            // Iterar sobre las subopciones (esto hace que se muestren las opciones del select)
            foreach ($opcion['subLabel'] as $subValue => $subLabel) {
                // Verificar si la subopción está seleccionada
                $selected = (isset($_SESSION['respuestas'][$pregunta['id']]) && $_SESSION['respuestas'][$pregunta['id']] == $subValue) ? 'selected' : '';
                echo "<option class='text-wrap mx-auto' value='" . $subValue . "' $selected>" . $subLabel . "</option>";
            }

            echo "
                </select>
            </div>
            ";
        }
    }
}
?>

<script>
    function toggleSelects(selectedRadio) {
    // Ocultar todos los selects
    const selects = document.querySelectorAll('.select-container');
    
    // Ocultar todos los selects inicialmente
    selects.forEach(select => {
        select.style.display = 'none';
    });

    // Mostrar el select correspondiente solo si el radio está seleccionado
    const selectId = `select-container-${selectedRadio.name}-${selectedRadio.value}`;
    const selectContainer = document.getElementById(selectId);
    
    if (selectedRadio.checked && selectContainer) {
        selectContainer.style.display = 'block';  // Muestra el select
    }

    // Guardar la selección del radio en la sesión
    saveResponse(selectedRadio.name, selectedRadio.value);
}

document.addEventListener('DOMContentLoaded', function() {
    const radios = document.querySelectorAll('.main-radio');

    // Inicializa la visibilidad de los selects cuando la página carga
    radios.forEach(radio => {
        if (radio.checked) {
            toggleSelects(radio);  // Llama a la función para mostrar el select si ya está seleccionado
        }
    });

    // Añadir evento para cambiar la visibilidad cuando se selecciona un radio
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            toggleSelects(this);  // Cambia la visibilidad cuando se selecciona un radio
        });
    });

    // Marcar el radio correspondiente si el select tiene valor
    document.querySelectorAll('.form-select').forEach(select => {
        select.addEventListener('change', function() {
            const selectIdParts = this.id.split('-');
            const preguntaId = selectIdParts[1];
            const clave = selectIdParts[2];
            const radio = document.getElementById(`radio-${preguntaId}-${clave}`);
            if (this.value !== '' && radio) {
                radio.checked = true;
                toggleSelects(radio);
            }

            // Guardar la selección del select en la sesión
            saveResponse(this.name, this.value);
        });

        // Mantener el radio marcado si el select tiene valor al cargar la página
        if (select.value !== '') {
            const selectIdParts = select.id.split('-');
            const preguntaId = selectIdParts[1];
            const clave = selectIdParts[2];
            const radio = document.getElementById(`radio-${preguntaId}-${clave}`);
            if (radio) {
                radio.checked = true;
                toggleSelects(radio);
            }
        }
    });
});

/* (function() {
    const form = document.querySelector('form');
    const checkboxes = form.querySelectorAll('input[type=select]');
    const checkboxLength = checkboxes.length;
    const firstCheckbox = checkboxLength > 0 ? checkboxes[0] : null;

    function init() {
        if (firstCheckbox) {
            for (let i = 0; i < checkboxLength; i++) {
                checkboxes[i].addEventListener('change', checkValidity);
            }

            checkValidity();
        }
    }

    function isChecked() {
        for (let i = 0; i < checkboxLength; i++) {
            if (checkboxes[i].checked) return true;
        }

        return false;
    }

    function checkValidity() {
        const errorMessage = !isChecked() ? 'Debe seleccionar al menos una opción.' : '';
        firstCheckbox.setCustomValidity(errorMessage);
    }

    init();
})(); */

</script>