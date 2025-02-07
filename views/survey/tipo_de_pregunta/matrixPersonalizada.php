<?php
session_start(); // Asegúrate de iniciar la sesión

if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    $opciones = $pregunta['opciones'];
    $questionId = $pregunta['id'];

    // Subopciones definidas manualmente
    $subOpciones = [
        "1" => "Sí, he trabajado.",
        "2" => "He realizado estancias de investigación o estudios.",
        "3" => "No he trabajado, ni realizado estancias."
    ];

    // Recuperar las respuestas de la sesión

    foreach ($opciones as $opcionKey => $opcionValue) {
        $checked1 = ''; // Inicializa la variable para verificar si la opción debe estar marcada para los primeros checkboxes
        $checked2 = ''; // Inicializa la variable para verificar si la opción debe estar marcada para los primeros checkboxes
        $checked3 = ''; // Inicializa la variable para verificar si la opción debe estar marcada para el radio
        $checked99 = ''; // Inicializa la variable para verificar si la opción debe estar marcada para 'No contesta'
        
        if (isset($respuestas[$opcionKey])) {
            $respuesta = $respuestas[$opcionKey];
            $respuestaArray = explode(', ', $respuesta); // Convertir la respuesta en un array
            if (in_array('1', $respuestaArray)) {
                $checked1 = 'checked';
            }
            if (in_array('2', $respuestaArray)) {
                $checked2 = 'checked';
            }
            if ($respuesta == '3') {
                $checked3 = 'checked';
            } elseif ($respuesta == '99') {
                $checked99 = 'checked';
            }
        }

        echo "<div class='row align-items-center justify-content-between mb-4 mb-lg-0'>";
        echo "<div class='col-12 col-lg-5 mb-3 mb-lg-0 align-self-center'>";
        echo "<label for='q{$questionId}_{$opcionKey}'>{$opcionValue}</label>";
        echo "</div>";
        echo "<div class='d-flex flex-md-row flex-column gap-2 col-12 col-lg-7 text-center' id='group-{$opcionKey}' data-group='grp_{$opcionKey}'>";

        // Primer checkbox
        echo "<input type='checkbox' class='btn-check first-group' 
                name='{$opcionKey}[]' 
                id='chk_{$opcionKey}_1' 
                value='1' $checked1 
                data-group='grp_{$opcionKey}' onclick='deselectRadios(\"{$opcionKey}\")'>";
        echo "<label class='btn btn-outline-primary text-center' 
                for='chk_{$opcionKey}_1' 
                {$checked1}>
                Sí, he trabajado.
              </label>";

        // Segundo checkbox
        echo "<input type='checkbox' class='btn-check first-group' 
                name='{$opcionKey}[]' 
                id='chk_{$opcionKey}_2' 
                value='2' $checked2
                data-group='grp_{$opcionKey}' onclick='deselectRadios(\"{$opcionKey}\")'>";
        echo "<label class='btn btn-outline-primary text-center' 
                for='chk_{$opcionKey}_2' 
                $checked2>
                He realizado estancias de investigación o estudios.
              </label>";

        // Radio button (tercera opción)
        echo "<input type='radio' class='btn-check third-radio' 
                name='{$opcionKey}' 
                id='rad_{$opcionKey}_3' 
                value='3' $checked3 
                data-group='grp_{$opcionKey}' onclick='deselectCheckboxes(\"{$opcionKey}\")'>";
        echo "<label class='btn btn-outline-primary text-center' 
                for='rad_{$opcionKey}_3' 
                $checked3>
                No he trabajado, ni realizado estancias.
              </label>";

        // Botón "No contesta" como radio
        echo "<input type='radio' class='btn-check no-sabe-radio' 
                name='{$opcionKey}' 
                id='noSabe_{$opcionKey}_99' 
                value='99' $checked99 
                data-group='grp_{$opcionKey}' onclick='deselectCheckboxes(\"{$opcionKey}\")'>";
        echo "<label for='noSabe_{$opcionKey}_99' 
                class='btn btn-outline-secondary' 
                $checked99>
                No contesta
              </label>";

        echo "</div>";
        echo "</div>";
        echo "<hr>";
    }
}
?>

<script>
function deselectRadios(opcionKey) {
    var radios = document.getElementsByName(opcionKey);
    for (var i = 0; i < radios.length; i++) {
        radios[i].checked = false;
    }
}

function deselectCheckboxes(opcionKey) {
    var checkboxes = document.getElementsByName(opcionKey + '[]');
    for (var i = 0; i < checkboxes.length; i++) {
        checkboxes[i].checked = false;
    }
}


document.addEventListener('DOMContentLoaded', function() {
    var groups = document.querySelectorAll('[data-group]');

    groups.forEach(function(group) {
        var groupId = group.getAttribute('data-group');
        var inputs = group.querySelectorAll(`[data-group="${groupId}"]`);
        var checkboxes = group.querySelectorAll('.first-group');
        var radios = group.querySelectorAll('.third-radio');
        var noContestaRadio = group.querySelector('.no-sabe-radio'); // Radio "No contesta"

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
});
</script>
