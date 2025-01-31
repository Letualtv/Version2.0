<?php
function GeneralController(): array
{
    // Incluir el archivo que define las variables
    include $_SERVER['DOCUMENT_ROOT'] . '/version2.0/models/general.php';

    // Retornar las variables directamente
    return $variables;
}
?>
