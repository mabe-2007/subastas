<?php
include("../../include/conex.php");
include("../../herramientas/llave/llave.php");
$conex = Conectarse();
session_name($session_name);
session_start();
$idrol = $_SESSION['idrol'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Sidebar con Modal de Permisos</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet" />
  <link rel="stylesheet" href="estilo/styless.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <!-- Bootstrap Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    $(document).ready(function(){
        // 🔹 Botón salir
        $("#navBtnSalir").on("click", function() {                
            $.post("../../include/ctrlindex.php",{ 
                action:'salir'
            }, function(){
                location.href="../../index.php";
            }, 'json');
        });

        // 🔹 Cargar menú dinámico
        $.post("../controlador/permisosCtrl.php", {
            action:'menuPorRol',
            idrol: <?= json_encode($idrol) ?>
        }, function(data){
            $('#sidebarMenu').html(data.menuHtml);
        }, 'json');
    });
  </script> 
</head>
<body>
    <input type="hidden" id="idrolSesion" value="<?= htmlspecialchars($idrol) ?>">

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebarNav" class="col-md-3 col-lg-2 collapse d-md-block sidebar">
                <div class="position-sticky pt-3">
                    <div class="text-center mb-4">
                        <img src="../../estilo/img/dd.png" class="logo-img1" />
                        <div class="logo">SubastasOnline</div>
                    </div>

                    <!-- Inicio -->
                    <a class="nav-link d-flex align-items-center text-white p-2 rounded" href="#" id="inicio" style="background-color: #1e90ff;">
                        <i class="bi bi-house me-2"></i> Inicio
                    </a>

                    <!-- Menú dinámico cargado por AJAX -->
                    <div id="sidebarMenu"></div>
                    <div></div>

                    <!-- Botón Salir -->
                    <button type="button" id="navBtnSalir" class="nav-link btn btn-link text-start w-100">
                        <i class="bi bi-box-arrow-left me-2"></i> Salir
                    </button>
                </div>
            </nav>

            <!-- Contenido principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
                <h1 class="mt-4">Bienvenido </h1>
                <!-- Navbar con botón para abrir menú en móvil -->
                <nav class="navbar navbar-expand-lg navbar-light bg-light mb-3">
                    <div class="container-fluid">
                        <button class="btn btn-primary d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#sidebarNav">
                            <i class="bi bi-list"></i> Menú
                        </button>
                    </div>
                </nav>

                <!-- Capa oscura (se mostrará solo cuando el menú esté abierto) -->
                <div id="backdrop" class="d-none" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.3); z-index: 998;"></div>

              
            </main>
        </div>
    </div>

   
    <!-- MODAL 1: Seleccionar Rol -->
    <div class="modal fade" id="seleccionarRolModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Seleccionar Rol</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Seleccione el rol al que desea asignar permisos:</strong></p>
                    <input type="hidden" class="form-control" id="idrol" name="idrol" readonly>
                    <div id="listaRolesContainer"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                </div>
            </div>
        </div>
    </div>

    <!--  MODAL 2: Asignar Permisos  -->
    <div class="modal fade" id="modalAsignarPermisos" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Asignar Permisos al Rol: <span id="nombreRolSeleccionado"></span></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Campo oculto para guardar el ID del rol -->
                    <input type="hidden" id="idRolSeleccionado">

                    <!-- Acordeón de menús y submenús -->
                    <div class="accordion" id="acordionPermiso"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Editar -->
    <div class="modal fade" id="editarUsuarioModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header  text-black">
                    <h5 class="modal-title">Editar Usuario</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Formulario -->
                    <form id="formEditarUsuario">
                        <input type="hidden" id="editIdusuario" name="idusuario">

                        <div class="mb-3">
                            <label>Nombre</label>
                            <input type="text" class="form-control" id="editNombre" name="nombre" required>
                        </div>

                        <div class="mb-3">
                            <label>Apellidos</label>
                            <input type="text" class="form-control" id="editApellidos" name="apellidos" required>
                        </div>

                        <div class="mb-3">
                            <label>Dirección</label>
                            <input type="text" class="form-control" id="editDireccion" name="direccion">
                        </div>

                        <div class="mb-3">
                            <label>Identificación</label>
                            <input type="text" class="form-control" id="editIdentificacion" name="identificacion" readonly />
                        </div>

                        <div class="mb-3">
                            <label>Teléfono</label>
                            <input type="text" class="form-control" id="editTelefono" name="telefono">
                        </div>

                        <div class="mb-3">
                            <label>Correo</label>
                            <input type="email" class="form-control" id="editCorreo" name="correo" required>
                        </div>

                        <div class="mb-3">
                            <label>Rol</label>
                            <select class="form-control" id="editRol" name="idrol" required>
                                <!-- Se llenará con AJAX -->
                            </select>
                        </div>

                        <div class="mb-3">
                            <label>Departamento</label>
                            <select class="form-control" id="editDepartamento" name="iddepartamento" required>
                                <!-- Se llenará con AJAX -->
                            </select>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="btnGuardarEdicion">Guardar Cambios</button>
                </div>
            </div>
        </div>
    </div>

   


    <script>
        const idrolSesion = <?= json_encode($idrol) ?>;
    </script>
    <script src="../modelo/head.js"></script>
    
    
</body>
</html>