


<?php $encabezado1 = $pregunta['encabezado1']; ?>

<div class="row align-items-center ">
    <div class="col-12  col-lg-5 text-center text-lg-start fw-bold">
        <?= $encabezado1['label'] ?>
    </div>

    <div class="col-12 col-md-6 col-lg-4">
        <div class="d-flex justify-content-evenly ">
            <?php
            // Función para evitar la repetición de código
            function mostrarElemento1($valor, $numero, $alineacion) {
                if (isset($valor) && !empty($valor)) {
                    echo '<div class="text-body-secondary py-3  ' . $alineacion . '">'; // Usa la clase pasada como parámetro
                    echo "<span>[$numero]</span>";
                    echo "<p>$valor</p>";
                    echo '</div>';
                }
            }

            mostrarElemento1($encabezado1['uno'][1] ?? null, 1, 'text-start'); // Alineación a la izquierda para el 1
            if (isset($encabezado1['uno'][1]) && isset($encabezado1['dos'][7]) && !empty($encabezado1['uno'][1]) && !empty($encabezado1['dos'][7])) {
                echo '<div class="mx-3"></div>';
            }
            mostrarElemento1($encabezado1['dos'][7] ?? null, 7, 'text-end'); // Alineación a la derecha para el 7
            ?>
        </div>
    </div>

    <div class="col-12 text-muted col-lg-1 text-center">
  <?php  
         if (isset($encabezado1['tres']) && !empty($encabezado1['tres'])): ?>
            <?= $encabezado1['tres'] ?>
        <?php endif; ?>
    </div>
</div>







<?php $encabezado2 = $pregunta['encabezado2']; ?>

<div class="row align-items-center ">
    <div class="col-12  col-lg-5 text-center text-lg-start fw-bold">
        <?= $encabezado2['label'] ?>
    </div>

    <div class="col-12 col-md-6 col-lg-5">
        <div class="d-flex justify-content-lg-evenly  justify-content-md-center justify-content-between">
            <?php
            // Función para evitar la repetición de código
            function mostrarElemento2($valor, $numero, $alineacion) {
                if (isset($valor) && !empty($valor)) {
                    echo '<div class="text-body-secondary pb-2 pt-lg-0 ' . $alineacion . '">'; // Usa la clase pasada como parámetro
                    echo "<span>[$numero]</span>";
                    echo "<p class='g-5'>$valor</p>";
                    echo '</div>';
                }
            }

            mostrarElemento2($encabezado2['uno'][1] ?? null, 1, 'text-wrap col-lg-5  ps-lg-3'); // Alineación a la izquierda para el 1
            if (isset($encabezado['uno'][1]) && isset($encabezado2['dos'][7]) && !empty($encabezado2['uno'][1]) && !empty($encabezado2['dos'][7])) {
                echo '<div></div>';
            }
            mostrarElemento2($encabezado2['dos'][7] ?? null, 7, 'text-end text-wrap col-lg-5  pe-lg-3'); // Alineación a la derecha para el 7
            ?>
        </div>
    </div>

    <div class="col-12 text-muted col-lg-1 text-center">
        <?php if (isset($encabezado2['tres']) && !empty($encabezado2['tres'])): ?>
            <?= $encabezado2['tres']                
 ?>
        <?php endif; ?>               

    </div>
    
</div>