  <!--  Búsqueda de Usuarios -->
<div class="p-3">
    <h5>Buscar Usuario</h5>
    <input 
        type="text" 
        id="buscarUsuario" 
        class="form-control mb-3" 
        placeholder="Buscar por nombre, apellido, identificación, correo o teléfono...">
<!-- Tabla fija en HTML -->
<div class="table-responsive">
    <table class="table table-striped table-hover">
        <thead class="table-primary">
            <tr>
                <th>Nombre</th>
                <th>Apellidos</th>
                <th>Dirección</th>
                <th>Identificación</th>
                <th>Teléfono</th>
                <th>Correo</th>
                <th>Rol</th>
                <th>Departamento</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody id="resultadosUsuarios">
            <!-- Aquí se inyectan las filas devueltas por PHP -->
        </tbody>
    </table>
</div>
<!-- Paginación -->
<div id="paginacionContainer" class="d-flex justify-content-center mt-3">
    <!-- Botones de paginación se inyectarán aquí -->
</div>
</div>