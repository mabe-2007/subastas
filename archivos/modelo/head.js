$(document).ready(function () {
    // 🔹 Variables para búsqueda
    let terminoBusqueda = '';
    let paginaActual = 1;

    // 🔹 Cargar roles y departamentos (una sola vez al cargar)
    $.post("../controlador/permisosCtrl.php", { action: "cargarRoles" }, function (res) {
        if (res.roles) {
            res.roles.forEach(rol => {
                $("#editRol").append(`<option value="${rol.idrol}">${rol.rol}</option>`);
            });
        }
    }, "json");

    $.post("../controlador/permisosCtrl.php", { action: "cargarDepartamentos" }, function (res) {
        if (res.departamentos) {
            res.departamentos.forEach(dep => {
                $("#editDepartamento").append(`<option value="${dep.iddepartamento}">${dep.departamento}</option>`);
            });
        }
    }, "json");

    // 🔹 Cuando se abre el modal de seleccionar rol
    $(document).on('shown.bs.modal', '#seleccionarRolModal', function () {
        $('#listaRolesContainer').html('<div class="text-center">Cargando roles...</div>');

        $.post("../controlador/permisosCtrl.php", {
            action: 'presentarRoles'
        }, function (data) {
            if (data.listaRoles) {
                $('#listaRolesContainer').html(data.listaRoles);
            } else {
                $('#listaRolesContainer').html('<div class="alert alert-danger">No se pudieron cargar los roles.</div>');
            }
        }, 'json')
        .fail(function () {
            $('#listaRolesContainer').html('<div class="alert alert-danger">Error de conexión.</div>');
        });
    });

    // 🔹 Cuando se hace clic en un rol
    $(document).on("click", ".btn-rol", function () {
        let idrol = $(this).data("idrol");
        let nombrerol = $(this).data("nombrerol");

        $("#idRolSeleccionado").val(idrol);
        $("#nombreRolSeleccionado").text(nombrerol);

        $("#seleccionarRolModal").modal("hide");

        $.post("../controlador/permisosCtrl.php", {
            action: 'presentarmenuid',
            idrol: idrol
        }, function (data) {
            if (data.menus && data.menus.length > 0) {
                $("#acordionPermiso").html(data.menus.join(""));
            } else {
                $("#acordionPermiso").html("<div class='alert alert-warning'>No hay menús para este rol.</div>");
            }
            $("#modalAsignarPermisos").modal("show");
        }, 'json');
    });

    // 🔹 MANEJAR EL TOGGLE DE PERMISOS
    $(document).on("change", ".toggle-submenu", function () {
        const checkbox = $(this);
        const idrol = checkbox.data("idrol");
        const idmenu = checkbox.data("idmenu");
        const idsubmenu = checkbox.data("idsubmenu");
        const activar = checkbox.is(":checked");

        const action = activar ? 'adicionar' : 'suspender';

        checkbox.prop("disabled", true);

        $.post("../controlador/permisosCtrl.php", {
            action: action,
            idrolpermiso: idrol,
            idmenupermiso: idmenu,
            idsubmenupermiso: idsubmenu
        }, function (respuesta) {
            if (respuesta.estado === "OK" || respuesta.estado === "YA_EXISTE") {
                checkbox.prop("disabled", false);
            } else {
                checkbox.prop("disabled", false).prop("checked", !activar);
                alert("Error al actualizar permiso: " + (respuesta.estado || "Desconocido"));
            }
        }, "json")
        .fail(function () {
            checkbox.prop("disabled", false).prop("checked", !activar);
            alert("Error de conexión con el servidor.");
        });
    });

    // 🔹 Búsqueda de usuarios
    $(document).on("input", "#buscarUsuario", function () {
        let termino = $(this).val().trim();

        if (termino.length < 2) {
            $("#resultadosUsuarios").html("");
            $("#paginacionContainer").html("");
            return;
        }

        terminoBusqueda = termino;
        paginaActual = 1;
        cargarResultados();
    });

    // 🔹 Paginación
    $(document).on("click", ".page-link", function (e) {
        e.preventDefault();
        let pagina = $(this).data("pagina");
        if (pagina) {
            paginaActual = pagina;
            cargarResultados();
        }
    });

    // 🔹 Función para cargar resultados
    function cargarResultados() {
        $.post(
            "../controlador/permisosCtrl.php",
            {
                action: "buscarUsuarios",
                termino: terminoBusqueda,
                pagina: paginaActual
            },
            function (respuesta) {
                if (respuesta.filas) {
                    $("#resultadosUsuarios").html(respuesta.filas);
                    $("#paginacionContainer").html(respuesta.paginacion);
                } else {
                    $("#resultadosUsuarios").html(
                        "<tr><td colspan='9' class='text-center text-danger'>No se encontraron resultados.</td></tr>"
                    );
                    $("#paginacionContainer").html("");
                }
            },
            "json"
        ).fail(function () {
            $("#resultadosUsuarios").html(
                "<tr><td colspan='9' class='text-center text-danger'>Error de conexión.</td></tr>"
            );
            $("#paginacionContainer").html("");
        });
    }

    // 🔹 Botón para abrir modal de asignar rol
    $(document).on("click", "#menuAsignarRol", function(e) {
        e.preventDefault();
        $.post("../controlador/permisosCtrl.php", {
            action: 'presentarRoles'
        }, function(data) {
            $('#listaRolesContainer').html(data.listaRoles);
            $('#seleccionarRolModal').modal('show');
        }, 'json');
    });

    // 🔹 Botón EDITAR
    $(document).on("click", ".btn-editar", function () {
        const idusuario = $(this).data("id");

        if (!idusuario) {
            alert("ID no válido");
            return;
        }

        $("#formEditarUsuario")[0].reset();


        $.post("../controlador/permisosCtrl.php", {
            action: "obtenerUsuario",
            idusuario: idusuario
        }, function (res) {
            if (res.estado === "OK") {
                const u = res.usuario;
                $("#editIdusuario").val(u.idusuario);
                $("#editNombre").val(u.nombre);
                $("#editApellidos").val(u.apellidos);
                $("#editDireccion").val(u.direccion);
                $("#editIdentificacion").val(u.identificacion);
                $("#editTelefono").val(u.telefono);
                $("#editCorreo").val(u.correo);
                $("#editRol").val(u.idrol);
                $("#editDepartamento").val(u.iddepartamento);
                $("#editarUsuarioModal").modal("show");
            } else {
                alert("Usuario no encontrado: " + res.mensaje);
            }
        }, "json");
    });


    // Tabla usuario 
    $(document).on("click", ".listar-usuario", function (e) {
        e.preventDefault();
        $(".main-content").load("listarusuario.php");
         
    });

    // 🔹 Guardar edición
    $("#btnGuardarEdicion").on("click", function () {
        const datos = $("#formEditarUsuario").serialize() + "&action=actualizarUsuario";

        $.post("../controlador/permisosCtrl.php", datos, function (res) {
            if (res.estado === "OK") {
                alert("Usuario actualizado");
                $("#editarUsuarioModal").modal("hide");
                $("#buscarUsuario").trigger("input");
            } else {
                alert("Error: " + res.mensaje);
            }
        }, "json");
    });

    // 🔹 Botón BORRAR
    $(document).on("click", ".btn-borrar", function () {
        const idusuario = $(this).data("id");

        if (!idusuario || idusuario <= 0) {
            alert("Error: ID inválido.");
            return;
        }

        if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
            $.post("../controlador/permisosCtrl.php", {
                action: "borrarUsuario",
                idusuario: idusuario
            }, function (respuesta) {
                if (respuesta.estado === "OK") {
                    alert("Usuario eliminado");
                    $("#buscarUsuario").trigger("input");
                } else {
                    alert("Error: " + respuesta.mensaje);
                }
            }, "json")
            .fail(function () {
                alert("Error de conexión.");
            });
        }

    // Tabla categoria
    $(document).on("click", ".listar-categoria", function (e) {
        e.preventDefault();
        $(".main-content").load("categoria.php");
         
    });
    // Categorias
   
    
});
    
     //Función para cargar categorias
    // function cargarCategorias() {
    //     $.post("../controlador/permisosCtrl.php",{
    //             action: "buscarCategorias",
    //             termino: terminoBusqueda,
    //             pagina: paginaActual
    //         },
    //         function (respuesta) {
    //             if (respuesta.filas) {
    //                 $("#resultadocategoria").html(respuesta.filas);
    //             } else {
    //                 $("#resultadocategoria").html(
    //                     "<tr><td colspan='9' class='text-center text-danger'>No se encontraron resultados.</td></tr>"
    //                 );
    //             }
    //         },
    //         "json"
    //     ).fail(function () {
    //         $("#resultadocategoria").html(
    //             "<tr><td colspan='9' class='text-center text-danger'>Error de conexión.</td></tr>"
    //         );
    //     });
    // }
    // $(document).on("input", "buscarcategoria", function () {
    //     let termino = $(this).val().trim();

    //     if (termino.length < 2) {
    //         $("#listarCategorias").html("");
    //         return;
    //     } 
    //     terminoBusqueda = termino;
    //     paginaActual = 1;
    //     cargarCategorias();
    // });

    
});
