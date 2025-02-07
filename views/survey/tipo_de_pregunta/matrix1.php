
<?php
if (isset($pregunta['opciones']) && is_array($pregunta['opciones'])) {
    $options = $pregunta['opciones'];

    foreach ($options as $clave => $opcion) {
        $partes = explode(' - ', $opcion);
        $parte1 = isset($partes[0]) ? $partes[0] : '';
        $parte2 = isset($partes[1]) ? $partes[1] : '';

        echo "
        <div class='row align-items-center justify-content-between mb-4'>
            <!-- Columna para la etiqueta de la pregunta -->
            <div class='col-12 col-md-2 '>
                $parte1
            </div>

            <!-- Columna para los botones numerados (1 al 7) -->
            <div class='col-12 col-md-5 text-center col-lg-auto'>
                <div class='text-center btn-group my-3 my-lg-0'>";

        for ($i = 1; $i <= 7; $i++) {
            $checked = '';
            if (isset($respuestas[$clave]) && $respuestas[$clave] == $i) {
                $checked = 'checked';
            }

            echo "
            <input type='radio' class='btn-check' required 
                name='{$clave}' 
                id='q{$clave}_{$i}' 
                value='{$i}' $checked>
            <label class='btn btn-outline-primary px-3' for='q{$clave}_{$i}'>{$i}</label>";
        }

        echo "
                </div>
            </div>
            <!-- Segunda parte del texto después de los botones -->
            <div class='col-12 col-md-2 text-end text-lg-start d-flex'>
                <div class='ms-auto'>$parte2</div>
            </div>

            <!-- Columna para los botones 'No sabe' y 'No contesta' -->
            <div class='col-12 col-md-5 offset-md-4 offset-lg-0 col-lg-3 mt-3 mt-md-0'>
                <div class='justify-content-lg-end justify-content-evenly justify-content-md-center d-flex gapx-0 gap-md-2 text-center'>";

        $checked88 = isset($respuestas[$clave]) && $respuestas[$clave] == 88 ? 'checked' : '';
        echo "
        <input type='radio' class='btn-check' required
            name='{$clave}' 
            value='88' 
            id='q{$clave}_88' $checked88>
        <label for='q{$clave}_88' class='btn btn-outline-secondary'>No sabe</label>";

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
