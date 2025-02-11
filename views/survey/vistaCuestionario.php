<?php foreach ($preguntasEnPagina as $indice => $pregunta): ?>
    <?php if (isset($pregunta['titulo']) || isset($pregunta['subtitulo'])): ?>
        <div class="card-header">
            <div class="pt-1 pt-md-3 ps-1 ps-md-3">
                <h6><?= $pregunta['titulo'] ?></h6>
                <p class="text-muted"><?= $pregunta['subTitulo'] ?? '' ?></p>
            </div>
        </div>
    <?php endif; ?>

    <div class="card-body p-4">
        <?php if (isset($pregunta['texto1']) || isset($pregunta['lista']) || isset($pregunta['texto2'])): ?>
            <div class="pb-2">
                <?php if (isset($pregunta['texto1'])): ?>
                    <p><?= $pregunta['texto1'] ?></p>
                <?php endif; ?>
                <?php if (isset($pregunta['lista']) && is_array($pregunta['lista'])): ?>
                    <ul>
                        <?php foreach ($pregunta['lista'] as $item): ?>
                            <li><?= $item ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
                <?php if (isset($pregunta['texto2'])): ?>
                    <p><?= $pregunta['texto2'] ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>
        <?php include_once __DIR__ . '/tipo_de_pregunta/encabezado.php'; ?>
        <?php
        switch ($pregunta['tipo']) {
            case 'radio':
                include_once __DIR__ . '/tipo_de_pregunta/radio.php';
                break;
            case 'checkbox':
                include_once  __DIR__ . '/tipo_de_pregunta/checkbox.php';
                break;
            case 'numberInput':
                include_once  __DIR__ . '/tipo_de_pregunta/numberInput.php';
                break;
            case 'formSelect':
                include_once  __DIR__ . '/tipo_de_pregunta/formSelect.php';
                break;
            case 'matrix1':
                include_once  __DIR__ . '/tipo_de_pregunta/matrix1.php';
                break;
            case 'matrix2':
                include_once  __DIR__ . '/tipo_de_pregunta/matrix2.php';
                break;
            case 'matrix3':
                include_once  __DIR__ . '/tipo_de_pregunta/matrix3.php';
                break;
            case 'matrixPersonalizada':
                include_once  __DIR__ . '/tipo_de_pregunta/matrixPersonalizada.php';
                break;
            case 'matrixPregunta':
                include_once  __DIR__ . '/tipo_de_pregunta/matrixPregunta.php';
                break;
            default:
                echo "<div class='alert alert-danger p-3' role='alert'><p><b>Error:</b> Tipo de pregunta no reconocido.</p><p>Posibles causas:</p><li>El archivo no existe.</li><li>El nombre no coincide con el de case.</li><li>La ubicación del archivo es correcta.</li></div>";
        }
        ?>
    </div>
<?php endforeach; ?>
<?php if (isset($pregunta['final'])): ?>
    <p class="text-center fst-italic py-3"><?= $pregunta['final'] ?></p>
<?php endif; ?>
<div class="card-footer text-center">
    <div class="row p-1">
        <div class="col ">
            <?php if ($prevPag): ?>
                <a href="?n_pag=<?= $prevPag ?>" class="btn btn-primary">Anterior</a>
            <?php endif; ?>           
        </div>
        <div class="col d-flex justify-content-center">
            <button class="btn btn-warning" type="reset">Resetear</button>
        </div>
        <div class="col ">
            <?php if ($nextPag): ?>
                <button type="submit" class="btn btn-primary">Siguiente</button>
            <?php else: ?>
                <button type="submit" class="btn btn-success">Finalizar</button>
            <?php endif; ?>
        </div>
    </div>
</div>