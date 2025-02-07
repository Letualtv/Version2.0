<?php
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    foreach ($pregunta['opciones'] as $clave => $opcion) {
        $checked = '';  // Inicializa la variable para verificar si la opción debe estar marcada
        $numberValue = ''; // Inicializa la variable para el valor del input number
        $inputDisabled = 'disabled'; // Inicializa la variable para deshabilitar el input number

        if (isset($respuestas[$pregunta['id']]) && $respuestas[$pregunta['id']] == $clave) {
            $checked = 'checked';
        }

        // Verifica si hay una respuesta guardada en la sesión para esta pregunta
        if (isset($respuestas[$pregunta['id']])) {
            $respuesta = $respuestas[$pregunta['id']];
            // Verifica si la respuesta es un valor numérico y no está en el rango de 1 a 100
            if (is_numeric($respuesta) && ($respuesta < 1 || $respuesta > 100)) {
                $numberValue = $respuesta;
                $inputDisabled = ''; // Habilita el input number si hay un valor guardado
                $checked = $clave == 1 ? 'checked' : ''; // Marca el radio 1 si el valor está fuera del rango
            } elseif ($respuesta == $clave) {
                $checked = 'checked';  // Marca la opción si coincide con la respuesta guardada
            }
        }

        echo "
        <div class='form-check d-flex align-items-center'>
            <input required
                class='form-check-input me-2 main-radio' 
                type='radio' 
                name='{$pregunta['id']}' 
                id='radio-{$pregunta['id']}-{$clave}' 
                value='$clave' 
                onchange='toggleNumberInput(this, {$pregunta['id']}, $clave)' $checked>
            <label class='form-check-label' for='radio-{$pregunta['id']}-{$clave}'>$opcion</label>";

        // Mostrar el input number para la opción que lo requiera
        if (in_array($clave, [1]) && isset($pregunta['valores'])) {
            $min = $pregunta['valores']['min'];
            $max = date("Y"); // Evaluar la fecha en el JSON
            $placeholder = $pregunta['valores']['placeholder'];

            echo "
            <input
                type='number'  required
                class='form-control form-control-sm ms-3 w-auto' 
                name='{$pregunta['id']}' 
                placeholder='$placeholder' 
                id='input-number-{$pregunta['id']}-$clave' 
                min='$min' 
                max='$max' 
                value='$numberValue' 
                $inputDisabled>";
        }

        echo "</div>";
    }
}
?>
<script>
function toggleNumberInput(selectedRadio, preguntaId, clave) {
    // Deshabilitar y limpiar todos los inputs de tipo number para la pregunta actual
    const allNumberInputs = document.querySelectorAll(`input[id^='input-number-${preguntaId}']`);
    allNumberInputs.forEach(input => {
        if (input.id !== `input-number-${preguntaId}-${clave}`) {
            input.disabled = true; // Deshabilitar el input
            input.removeAttribute('required'); // Quitar la obligatoriedad
            input.value = ''; // Limpiar su valor solo si el input no es el seleccionado
        }
    });

    // Habilitar el input asociado al radio seleccionado
    const inputNumber = document.getElementById(`input-number-${preguntaId}-${clave}`);
    if (selectedRadio.checked && inputNumber) {
        inputNumber.disabled = false; // Habilitar el input
        inputNumber.setAttribute('required', 'required'); // Hacer obligatorio
    }
}

document.addEventListener('DOMContentLoaded', () => {
    const radios = document.querySelectorAll('.main-radio');

    // Inicializa la visibilidad de los inputs number cuando la página carga
    radios.forEach(radio => {
        if (radio.checked) {
            const clave = radio.value;
            const preguntaId = radio.name;
            toggleNumberInput(radio, preguntaId, clave); // Llama a la función para mostrar el input number si ya está seleccionado
        }
    });

    // Evitar el submit del formulario al presionar Enter en un input number
    document.querySelectorAll('input[type="number"]').forEach(input => {
        input.addEventListener('keydown', event => {
            if (event.key === 'Enter') {
                event.preventDefault();
            }
        });
    });
});
</script>
