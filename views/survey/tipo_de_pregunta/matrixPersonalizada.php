<?php
session_start(); // Asegúrate de iniciar la sesión

if (isset($pregunta['opciones']) && is_array($pregunta['opciones']) && isset($pregunta['subOpciones']) && is_array($pregunta['subOpciones'])) {
    $opciones = $pregunta['opciones'];
    $subOpciones = $pregunta['subOpciones'];
    $questionId = $pregunta['id'];

    // Recuperar las respuestas de la sesión
    $respuestas = $_SESSION['respuestas'][$questionId] ?? [];

    foreach ($opciones as $opcionKey => $opcionValue) {
        echo "<div class='row align-items-center justify-content-between mb-4 mb-lg-0'>";
        echo "<div class='col-12 col-lg-5 mb-3 mb-lg-0 align-self-center'>";
        echo "<label for='q{$questionId}_{$opcionKey}'>{$opcionValue}</label>";
        echo "</div>";

        echo "<div class='d-flex flex-md-row flex-column gap-2 col-12 col-lg-7 text-center' id='group-{$questionId}_{$opcionKey}' data-group='grp_{$questionId}_{$opcionKey}'>";

        // Los dos primeros botones son checkbox
        $i = 0;
        foreach ($subOpciones as $subOpcionKey => $subOpcionValue) {
            // Verificar si la opción ya fue seleccionada
            $isChecked = in_array($subOpcionKey, $respuestas) ? 'checked' : '';

            if ($i < 2) {
                echo "<input type='checkbox' class='btn-check first-group' 
                        name='{$opcionKey}[]' 
                        id='chk_{$questionId}_{$opcionKey}_{$subOpcionKey}' 
                        value='{$subOpcionKey}' {$isChecked} data-group='grp_{$questionId}_{$opcionKey}'>";
                echo "<label class='btn btn-outline-primary text-center' 
                        style='text-wrap-style: balance;' 
                        for='chk_{$questionId}_{$opcionKey}_{$subOpcionKey}' aria-checked='{$isChecked}'>
                        {$subOpcionValue}
                      </label>";
                $i++;
            } else {
                // El tercer botón es radio (si es otro valor)
                // Usamos un sufijo único para el name de los radios
                echo "<input type='radio' class='btn-check third-radio' 
                        name='{$opcionKey}' 
                        id='rad_{$questionId}_{$opcionKey}_{$subOpcionKey}' 
                        value='{$subOpcionKey}' {$isChecked} data-group='grp_{$questionId}_{$opcionKey}'>";
                echo "<label class='btn btn-outline-primary text-wrap text-center' 
                        style='text-wrap-style: balance;' 
                        for='rad_{$questionId}_{$opcionKey}_{$subOpcionKey}' aria-checked='{$isChecked}'>
                        {$subOpcionValue}
                      </label>";
            }
        }

        // Botón "No sabe" es radio dentro del mismo grupo
        $isNoContestaChecked = in_array('99', $respuestas) ? 'checked' : '';
        echo "<input type='radio' name='{$opcionKey}' 
                value='99' class='btn-check no-sabe-radio' 
                id='noSabe_{$questionId}_{$opcionKey}_99' {$isNoContestaChecked} data-group='grp_{$questionId}_{$opcionKey}'>";
        echo "<label for='noSabe_{$questionId}_{$opcionKey}_99' 
                class='btn btn-outline-secondary' aria-checked='{$isNoContestaChecked}'>
                No contesta
              </label>";
        echo "</div>";
        echo "</div>";
        echo "<hr>";
    }
}
?>
<script>
const groups = document.querySelectorAll('[data-group]');

groups.forEach(group => {
    const groupId = group.getAttribute('data-group');
    const inputs = group.querySelectorAll(`[data-group="${groupId}"]`);
    const checkboxes = group.querySelectorAll('.first-group');
    const radios = group.querySelectorAll('.third-radio');
    const noContestaRadio = group.querySelector('.no-sabe-radio'); // Radio "No contesta"

    const manageCheckboxRadioState = (element, elementsToClear) => {
        if (element.checked) {
            elementsToClear.forEach(el => el.checked = false);
        }
    };

    if (noContestaRadio) {
        noContestaRadio.addEventListener('change', function () {
            manageCheckboxRadioState(this, [...checkboxes, ...radios]);
            checkValidity();
        });
    }

    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function () {
            manageCheckboxRadioState(this, [...radios, noContestaRadio]);
            checkValidity();
        });
    });

    radios.forEach(radio => {
        radio.addEventListener('change', function () {
            manageCheckboxRadioState(this, checkboxes);
            if (noContestaRadio) noContestaRadio.checked = false;
            checkValidity();
        });
    });

    const checkValidity = () => {
        const isValid = [...inputs].some(input => input.checked);
        inputs.forEach(input => {
            input.setCustomValidity(isValid ? '' : 'Debe seleccionar al menos una opción en cada fila.');
        });
    };

    checkValidity();
});
</script>
