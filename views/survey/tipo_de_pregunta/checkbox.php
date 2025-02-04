
<?php

/**
 * Verifica si la pregunta tiene opciones
 */
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    /**
     * Recorre las opciones y genera el HTML
     */
    foreach ($pregunta['opciones'] as $clave => $opcion) {
        /**
         * Verifica si hay una respuesta guardada en la sesión para esta pregunta
         */
        $checked = '';
        if (isset($_SESSION['respuestas'][$pregunta['id']])) {
            $respuestas = explode(', ', $_SESSION['respuestas'][$pregunta['id']]);
            if (in_array($clave, $respuestas)) {
                $checked = 'checked';
            }
        }

        /**
         * Genera el HTML
         */
        echo "
        <div class='form-check pb-1'>
            <input class='form-check-input' type='checkbox' id='$clave' name='{$pregunta['id']}[]' value='{$clave}' $checked required[]>
            <label class='form-check-label' for='$clave'>$opcion</label>
        </div>
        ";
    }
}
?>
<script>
    (function() {
    const form = document.querySelector('form');
    const checkboxes = form.querySelectorAll('input[type=checkbox]');
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
})();
</script>