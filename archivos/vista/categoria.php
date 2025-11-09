<!-- Búsqueda de Categorías -->
<div class="p-3">
    <h5>Categorías</h5>
    <input 
        type="text" 
        id="buscarcategoria" 
        class="form-control mb-3" 
        placeholder="Buscar categoría..." 
        autocomplete="off">
</div> <!-- Cierre agregado -->

<!-- Tabla fija en HTML -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th>Categoría</th>
                <th>Descripción</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="resultadocategoria">
            <!-- Aquí se inyectan las filas -->
        </tbody>
    </table>
</div>

<!-- Paginación -->
<div id="paginacionContainer" class="d-flex justify-content-center mt-3">
    <!-- Botones de paginación -->
</div>