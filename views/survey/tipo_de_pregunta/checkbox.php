
<?php
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    foreach ($pregunta['opciones'] as $clave => $opcion) {
        $checked = '';  // Inicializa la variable para verificar si la opción debe estar marcada

        if (isset($respuestas[$pregunta['id']])) {
            $respuestaArray = explode(', ', $respuestas[$pregunta['id']]);
            
            // Verifica si la respuesta es un valor numérico y no es uno de los valores esperados
            foreach ($respuestaArray as $respuesta) {
                if (!in_array($respuesta, range(1, 10)) && is_numeric($respuesta)) {
                    $inputDisabled = ''; // Habilita el input number si hay un valor guardado
                } elseif (in_array($clave, $respuestaArray)) {
                    $checked = 'checked';  // Marca la opción si coincide con la respuesta guardada
                }
            }
        }

        echo "
        <div class='form-check d-flex align-items-center'>
            <input 
                class='form-check-input me-2 main-checkbox' 
                type='checkbox' 
                name='{$pregunta['id']}[]' 
                id='checkbox-{$pregunta['id']}-{$clave}' 
                value='$clave' 
                $checked>
            <label class='form-check-label' for='checkbox-{$pregunta['id']}-{$clave}'>$opcion</label>";

        echo "</div>";
    }
}
?>

<script>
   

    document.addEventListener('DOMContentLoaded', () => {
        const checkboxes = document.querySelectorAll('.main-checkbox');

        // Inicializa la visibilidad de los inputs number cuando la página carga
        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                const clave = checkbox.value;
                const preguntaId = checkbox.name.replace('[]', ''); // Eliminar los corchetes del nombre si es un array
                toggleNumberInput(checkbox, preguntaId, clave); // Llama a la función para mostrar el input number si ya está seleccionado
            }
        });

    
    });
</script>
