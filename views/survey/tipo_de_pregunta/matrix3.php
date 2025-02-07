<?php
if (isset($pregunta['opciones']) && is_array($pregunta['opciones']) && isset($pregunta['subOpciones']) && is_array($pregunta['subOpciones'])) {
    $opciones = $pregunta['opciones'];
    $subOpciones = $pregunta['subOpciones'];
    $subOpcionIndex = 1;

    $opcionesKeys = array_keys($opciones);

    foreach ($opcionesKeys as $index => $clave) {
        $Label = $opciones[$clave];
        $subId = ceil(($index + 1) / 2);

        // Verifica si hay una respuesta guardada para 'No sabe' y 'No contesta'
        $checked88 = ''; 
        $checked99 = ''; 

        if (isset($respuestas[$clave])) {
            $respuesta = $respuestas[$clave];
            if ($respuesta == 88) {
                $checked88 = 'checked';
            } elseif ($respuesta == 99) {
                $checked99 = 'checked';
            }
        }
        ?>

        <div class="row mb-3 align-items-center">
            <div class="col-12 col-lg-4">
                <?php if ($subId == $subOpcionIndex): ?>
                    <?= $subOpciones[$subOpcionIndex] ?>
                    <?php $subOpcionIndex++; ?>
                <?php endif; ?>
            </div>

            <div class="col-12 col-lg-2">
                <label for="q<?= $clave ?>_<?= $subId ?>_1" class="form-label"><?= $Label ?></label>
            </div>

            <div class="col-12 col-md-6 text-center col-lg-auto">
                <div class="btn-group my-3 my-lg-0">
                <?php for ($i = 1; $i <= 7; $i++):
                    $checked = '';
                    if (isset($respuestas[$clave]) && $respuestas[$clave] == $i) {
                        $checked = 'checked';
                    }
                ?>
                    <input type="radio" required class="btn-check" name="<?= $clave ?>" 
                        id="q<?= $clave ?>_<?= $subId ?>_<?= $i ?>" value="<?= $i ?>" <?= $checked ?> autocomplete="off"> 
                    <label class="btn btn-outline-primary px-3" for="q<?= $clave ?>_<?= $subId ?>_<?= $i ?>"><?= $i ?></label>
                <?php endfor; ?>
                </div>
            </div>

            <div class="col-12 col-md-6 col-lg-3 mt-3 mt-md-0">
                <div class="justify-content-md-end justify-content-evenly d-flex gap-0 gap-md-2 text-center">
                    <input type="radio" required name="<?= $clave ?>" value="88" class="btn-check"
                        id="q<?= $clave ?>_<?= $subId ?>_88" <?= $checked88 ?> autocomplete="off">
                    <label class="btn btn-outline-secondary" for="q<?= $clave ?>_<?= $subId ?>_88">No sabe</label>

                    <input type="radio" required name="<?= $clave ?>" value="99" class="btn-check"
                        id="q<?= $clave ?>_<?= $subId ?>_99" <?= $checked99 ?> autocomplete="off">
                    <label class="btn btn-outline-secondary" for="q<?= $clave ?>_<?= $subId ?>_99">No contesta</label>
                </div>
            </div>
        </div>

        <?php if (($index + 1) % 2 == 0 && $index < count($opcionesKeys) - 1): ?>
            <hr>
        <?php endif; ?>
<?php
    }
}
?>

