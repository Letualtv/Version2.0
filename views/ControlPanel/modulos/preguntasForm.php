<form id="preguntaForm">
    <input type="hidden" id="preguntaId">
    <div class="form-group">
        <label for="titulo">Título de la Pregunta</label>
        <input type="text" class="form-control" id="titulo" required>
    </div>
    <div class="form-group">
        <label for="subTitulo">Subtítulo de la Pregunta</label>
        <input type="text" class="form-control" id="subTitulo">
    </div>
    <div class="form-group">
        <label for="n_pag">Número de Página</label>
        <input type="number" class="form-control" id="n_pag" required>
    </div>
    <div class="form-group">
        <label for="tipo">Tipo de Pregunta</label>
        <select class="form-control" id="tipo" required onchange="ajustarParametros()">
            <option value="radio">Tipo radio</option>
            <option value="numberInput">Entrada numérica</option>
            <option value="checkbox">Tipo checkbox</option>
            <option value="formSelect">Seleccion desplegable</option>
        </select>
    </div>
    <div id="opciones">
        <div class="form-group">
            <label>Opciones</label>
            <div class="input-group mb-2">
                <input type="text" class="form-control" name="opciones[]" placeholder="Opción" required>
            </div>
        </div>
    </div>
    <div id="valores" style="display: none;">
        <div class="form-group" id="numberInputFields">
            <label for="min">Valor Mínimo</label>
            <input type="number" class="form-control" id="min" name="valores[min]">
            <label for="max">Valor Máximo</label>
            <input type="number" class="form-control" id="max" name="valores[max]">
            <label for="placeholder">Placeholder</label>
            <input type="text" class="form-control" id="placeholder" name="valores[placeholder]">
        </div>
    </div>
    <button type="button" class="btn btn-secondary" onclick="agregarOpcion()">Añadir Opción</button>
    <button type="submit" class="btn btn-primary">Guardar Pregunta</button>
</form>
