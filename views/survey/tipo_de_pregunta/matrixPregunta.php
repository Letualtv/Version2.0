
<?php

/**
 * Verifica si la pregunta tiene opciones
 */
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    /**
     * Decodifica el JSON
     */
    $options = $pregunta['opciones'];

    /**
     * Recorre las opciones y genera el HTML
     */
    foreach ($options as $clave => $opcion) {
        /**
         * Separa el texto por el guion
         */
        $partes = explode(' - ', $opcion);

        /**
         * Verifica que la separación generó dos partes
         */
        $parte1 = isset($partes[0]) ? $partes[0] : '';
        $parte2 = isset($partes[1]) ? $partes[1] : '';

        /**
         * Genera el HTML
         */
        echo "
        <div class='row align-items-center justify-content-between mb-4'>
            <!-- Columna para la etiqueta de la pregunta -->
            <div class='col-12 col-md-2 '>
                $parte1
            </div>

            <!-- Columna para los botones numerados (1 al 7) -->
            <div class='col-12 col-md-5  text-center col-lg-auto'>
                <div class='text-center btn-group my-3 my-lg-0'>";

        /**
         * Genera los botones numerados (1 al 7)
         */
        for ($i = 1; $i <= 7; $i++) {
            $checked = ''; // Inicializa la variable para verificar si la opción debe estar marcada

            // Verifica si hay una respuesta guardada en la sesión para esta pregunta
            if (isset($_SESSION['respuestas'][$clave]) && $_SESSION['respuestas'][$clave] == $i) {
                $checked = 'checked'; // Marca la opción si coincide con la respuesta guardada
            }

            echo "
            <input type='radio' class='btn-check' required 
                name='{$pregunta['id']}' 
                id='q{$clave}_{$i}' 
                value='{$i}' $checked>
            <label class='btn btn-outline-primary px-3' for='q{$clave}_{$i}'>{$i}</label>";
        }

        /**
         * Cierra la columna para los botones numerados
         */
        echo "
                </div>
            </div>
            <!-- Segunda parte del texto después de los botones -->
            <div class='col-12 col-md-2 text-end text-lg-start d-flex'>
                <div class='ms-auto'>$parte2</div>
            </div>

            <!-- Columna para los botones 'No sabe' y 'No contesta' -->
            <div class='col-12 col-md-5 offset-md-4 offset-lg-0 col-lg-3 mt-3 mt-md-0'>
                <div class='justify-content-lg-end justify-content-evenly justify-content-md-center d-flex gap-0 gap-md-2 text-center'>";

        // Botón 'No sabe'
        $checked88 = isset($_SESSION['respuestas'][$clave]) && $_SESSION['respuestas'][$clave] == 88 ? 'checked' : '';
        echo "
        <input type='radio' class='btn-check' required
            name='{$pregunta['id']}' 
            value='88' 
            id='q{$clave}_88' $checked88>
        <label for='q{$clave}_88' class='btn btn-outline-secondary'>No sabe</label>";

        // Botón 'No contesta'
        $checked99 = isset($_SESSION['respuestas'][$clave]) && $_SESSION['respuestas'][$clave] == 99 ? 'checked' : '';
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
