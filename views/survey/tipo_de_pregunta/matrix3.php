<?php

if (isset($pregunta['opciones']) && is_array($pregunta['opciones']) && isset($pregunta['subOpciones']) && is_array($pregunta['subOpciones'])) {
    $opciones = $pregunta['opciones'];
    $subOpciones = $pregunta['subOpciones'];
    $questionId = $pregunta['id'];
    $subOpcionIndex = 1;

    $opcionesKeys = array_keys($opciones);

    foreach ($opcionesKeys as $index => $clave) {
        $Label = $opciones[$clave];
        $subId = ceil(($index + 1) / 2);

?>

        <div class="row mb-3 align-items-center ">
            <div class="col-12 col-lg-4">
                <?php if ($subId == $subOpcionIndex): ?>
                    <?= $subOpciones[$subOpcionIndex] ?>
                    <?php $subOpcionIndex++; ?>
                <?php endif; ?>
            </div>

            <div class="col-12 col-lg-2">
                <label for="q<?= $clave ?>_<?= $subId ?>_1" class="form-label"><?= $Label ?></label>
            </div>

            <div class='col-12 col-md-6  text-center col-lg-auto'>
            <div class=' btn-group my-3 my-lg-0'>
            <?php for ($i = 1; $i <= 7; $i++):
                        $sessionValue = $_SESSION['respuestas'][$clave] ?? null;
                        $checked = ($sessionValue == $i) ? 'checked' : '';
                    ?>
                        <input type="radio" required class="btn-check" name="<?= $clave ?>" 
                            id="q<?= $clave ?>_<?= $subId ?>_<?= $i ?>" value="<?= $i ?>" <?= $checked ?> autocomplete="off"> 
                        <label class="btn btn-outline-primary px-3" for="q<?= $clave ?>_<?= $subId ?>_<?= $i ?>"><?= $i ?></label>
                    <?php endfor; ?>
                </div>
            </div>

            <div class='col-12 col-md-6 col-lg-3 mt-3 mt-md-0'>
            <div class='justify-content-md-end justify-content-evenly d-flex gap-0 gap-md-2 text-center'>
            <?php
                                                        $sessionValue = $_SESSION['respuestas'][$clave] ?? null;
                                                        $checked88 = ($sessionValue == 88) ? 'checked' : '';
                                                        $checked99 = ($sessionValue == 99) ? 'checked' : '';
                                                        ?>
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
