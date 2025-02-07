<?php
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    foreach ($pregunta['opciones'] as $clave => $opcion) {
        $checked = '';  // Inicializa la variable para verificar si la opción debe estar marcada
        $selectVisible = 'display: none;';  // Inicializa la visibilidad del select asociado
        $selectedValue = '';  // Inicializa la variable para verificar si el select debe tener un valor seleccionado

        // Verifica si hay una respuesta guardada en la sesión para esta pregunta
        if (isset($respuestas[$pregunta['id']])) {
            $respuesta = $respuestas[$pregunta['id']];
            // Verifica si la respuesta es un valor numérico y no es uno de los valores esperados
            if (!in_array($respuesta, range(1, 10)) && is_numeric($respuesta)) {
                $selectedValue = $respuesta;
                $selectVisible = 'display: block;';  // Muestra el select asociado si el radio está marcado
                if ($clave == 1) {
                    $checked = 'checked';  // Marca la opción correspondiente
                }
            } elseif ($respuesta == $clave) {
                $checked = 'checked';  // Marca la opción si coincide con la respuesta guardada
            }
        }

        echo "
        <div class='form-check '>
            <input required
                class='form-check-input me-2 main-radio' 
                type='radio' 
                name='{$pregunta['id']}' 
                id='radio-{$pregunta['id']}-{$clave}' 
                value='$clave' 
                onchange='toggleSelects(this, {$pregunta['id']}, $clave)' $checked>
            <label class='form-check-label' for='radio-{$pregunta['id']}-{$clave}'>$opcion[label]</label>";

        // Mostrar el select para la opción correspondiente
        if (isset($opcion['subLabel']) && count($opcion['subLabel']) > 0) {
            echo "
            <div class='select-container ms-4 mt-2' 
                 id='select-container-{$pregunta['id']}-$clave'  
                 style='$selectVisible'>
                <select class='form-select mb-2 w-auto' 
                        id='select-{$pregunta['id']}-$clave' 
                        name='{$pregunta['id']}'>";  // Mantén el `name` del select sin cambios

            // Asegurarse de que la opción vacía no sea seleccionada por defecto
            echo "<option class='text-wrap mx-auto' value='' disabled selected>Seleccione una opción</option>";

            // Iterar sobre las subopciones
            foreach ($opcion['subLabel'] as $subValue => $subLabel) {
                $selected = ($selectedValue == $subValue) ? 'selected' : '';
                echo "<option class='text-wrap mx-auto' value='$subValue' $selected>$subLabel</option>";
            }

            echo "
                </select>
            </div>
            ";
        }

        echo "</div>";
    }
}
?>

<script>
function toggleSelects(selectedRadio, preguntaId, clave) {
    // Ocultar todos los selects
    const selects = document.querySelectorAll('.select-container');
    
    // Ocultar todos los selects inicialmente
    selects.forEach(select => {
        select.style.display = 'none';
    });

    // Mostrar el select correspondiente solo si el radio está seleccionado
    const selectContainer = document.getElementById(`select-container-${preguntaId}-${clave}`);
    if (selectedRadio.checked && selectContainer) {
        selectContainer.style.display = 'block';  // Muestra el select
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const radios = document.querySelectorAll('.main-radio');

    // Inicializa la visibilidad de los selects cuando la página carga
    radios.forEach(radio => {
        if (radio.checked) {
            const clave = radio.value;
            const preguntaId = radio.name;
            toggleSelects(radio, preguntaId, clave); // Llama a la función para mostrar el select si ya está seleccionado
        }
    });

    // Añadir evento para cambiar la visibilidad cuando se selecciona un radio
    radios.forEach(radio => {
        radio.addEventListener('change', function() {
            toggleSelects(this, this.name, this.value);  // Cambia la visibilidad cuando se selecciona un radio
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
                toggleSelects(radio, preguntaId, clave);
            }
        });

        // Mantener el radio marcado si el select tiene valor al cargar la página
        if (select.value !== '') {
            const selectIdParts = select.id.split('-');
            const preguntaId = selectIdParts[1];
            const clave = selectIdParts[2];
            const radio = document.getElementById(`radio-${preguntaId}-${clave}`);
            if (radio) {
                radio.checked = true;
                toggleSelects(radio, preguntaId, clave);
            }
        }
    });
});
</script>
