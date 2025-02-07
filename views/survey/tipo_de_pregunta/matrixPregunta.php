<?php
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    $options = $pregunta['opciones'];
    foreach ($options as $clave => $opcion) {
        $partes = explode(' - ', $opcion);
        $parte1 = isset($partes[0]) ? $partes[0] : '';
        $parte2 = isset($partes[1]) ? $partes[1] : '';

        // Inicializar variables para verificar si la opción debe estar marcada
        $checked = '';
        $numberValue = '';
        $inputDisabled = 'disabled';

        // Verificar si hay una respuesta guardada para esta pregunta
        if (isset($respuestas[$clave])) {
            $respuesta = $respuestas[$clave];
            if (!in_array($respuesta, range(1, 100))) {
                $numberValue = $respuesta;
                $inputDisabled = '';
                $checked = 'checked';  // Marcar el radio padre
            } elseif ($respuesta == $clave) {
                $checked = 'checked';
            }
        }

        echo "
        <div class='row align-items-center justify-content-between mb-4'>
            <!-- Columna para la etiqueta de la pregunta -->
            <div class='col-12 col-md-2 '>
                $parte1
            </div>

            <!-- Columna para los botones numerados (1 al 7) -->
            <div class='col-12 col-md-5 text-center col-lg-auto'>
                <div class='text-center btn-group my-3 my-lg-0'>";

        // Generar los botones numerados (1 al 7)
        for ($i = 1; $i <= 7; $i++) {
            $checkedBtn = '';
            if (isset($respuestas[$clave]) && $respuestas[$clave] == $i) {
                $checkedBtn = 'checked';
            }

            echo "
            <input type='radio' class='btn-check' required 
                name='{$pregunta['id']}' 
                id='q{$clave}_{$i}' 
                value='{$i}' $checkedBtn>
            <label class='btn btn-outline-primary px-3' for='q{$clave}_{$i}'>{$i}</label>";
        }

        echo "
                </div>
            </div>
            <!-- Segunda parte del texto después de los botones -->
            <div class='col-12 col-md-2 text-end text-lg-start d-flex'>
                <div class='ms-auto'>$parte2</div>
            </div>

            <!-- Columna para el input number -->
            <div class='col-12 col-md-2 text-end text-lg-start d-flex'>
                <div class='ms-auto'>
                    <input
                        type='number' required 
                        class='form-control form-control-sm ms-3 w-auto' 
                        name='{$pregunta['id']}' 
                        placeholder='Número'
                        id='input-number-{$pregunta['id']}-$clave' 
                        min='1' max='100' 
                        value='$numberValue' 
                        $inputDisabled>
                </div>
            </div>

            <!-- Columna para los botones 'No sabe' y 'No contesta' -->
            <div class='col-12 col-md-5 offset-md-4 offset-lg-0 col-lg-3 mt-3 mt-md-0'>
                <div class='justify-content-lg-end justify-content-evenly justify-content-md-center d-flex gap-0 gap-md-2 text-center'>";

        // Botón 'No sabe'
        $checked88 = isset($respuestas[$clave]) && $respuestas[$clave] == 88 ? 'checked' : '';
        echo "
        <input type='radio' class='btn-check' required
            name='{$pregunta['id']}' 
            value='88' 
            id='q{$clave}_88' $checked88>
        <label for='q{$clave}_88' class='btn btn-outline-secondary'>No sabe</label>";

        // Botón 'No contesta'
        $checked99 = isset($respuestas[$clave]) && $respuestas[$clave] == 99 ? 'checked' : '';
        echo "
        <input type='radio' class='btn-check' required
            name='{$clave}' 
            value='99' 
            id='q{$clave}_99' $checked99>
        <label for='q{$clave}_99' class='btn btn-outline-secondary'>No contesta</label>";

        echo "
                </div>
            </div>
        </div>
        <hr>";
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
