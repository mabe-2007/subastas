<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Subastas</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css?family=Exo:400,500,600,700,800&display=swap" rel="stylesheet">

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">

  <!-- Estilos personalizados -->
  <script type="module" src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.esm.js"></script>
  <script nomodule src="https://unpkg.com/ionicons@7.1.0/dist/ionicons/ionicons.js"></script>
  <link rel="stylesheet" href="estilo/styles.css">

  <!-- jQuery -->
  <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
  <script type="text/javascript">
    $(document).ready(function(){        
        $('#nombre').val("");
        $('#apellidos').val("");
        $('#correo').val("");
        $('#clave').val("");

        // ✅ INICIO DE SESIÓN SEGURO
        $("#btningresar").on("click", function() {
            var correo = $("#correoinicio").val().trim();
            var clave = $("#claveinicio").val();
            
            if(correo === '' || clave === '') {
                $("#msj").html("Por favor complete todos los campos");
                return;
            }
            
            $.post("include/ctrlindex.php", { 
                action: 'iniciar', 
                clave: clave,
                correo: correo
            }, function(data){
                if(data.rspst == "1"){   
                    location.href = "archivos/vista/inicio.php";
                } else {  
                    $("#msj").html(data.msj); 
                }
            }, 'json').fail(function() {
                $("#msj").html("Error de conexión");
            });
        });

         // Verificar si el usuario ya se registró
        $.get('include/verificarRegistro.php', function (data) {
            if (data.registrado) {
                // Oculta el botón de registrar
                $('#btnregistrar').hide();

                // Previene la apertura del modal si aún queda el botón
                $('#btnregistrar').attr('data-bs-toggle', '');
                $('#btnregistrar').on('click', function (e) {
                    e.preventDefault();
                });
            }
        }, 'json');
    

      $("#btnguardar").on("click", function () {
        
        const nombre = $('#nombre').val().trim();
        $("#btnguardar").prop("disabled", true);

            // Si el nombre es Aenas, bloquea el botón después del registro
            if (nombre.toLowerCase() === "aenas") {
                $("#btnguardar").prop("disabled", true); // Desactiva el botón
            }

            $.post("include/ctrlindex.php", {
                action: 'registrarUsuario',
                nombre: nombre,
                apellido: $('#apellidos').val(),
                identificacion: $('#identificacion').val(),
                direccion: $('#direccion').val(),
                idTipoDocumento: $('#selectTipoDoc').val(),
                telefono: $('#telefono').val(),
                iddepto: $('#selecdepto').val(),
                correoregistro: $('#correoregistro').val(),
                claveregistro: $('#claveregistro').val()
            }, function (data) {
                if (data.rspst == "1") {
                  $(".alert").removeClass("alert-danger").addClass("alert-success");
                  $(".alert").html(data.msj);

                  // Cierra el modal
                  $("#btnCancelar").trigger("click");

                  // Oculta el botón de registrar para evitar más aperturas
                  $('#btnregistrar').hide();
                  $('#btnregistrar').attr('data-bs-toggle', '');
                  $('#btnregistrar').off('click'); // quita cualquier handler anterior

                  // Si quieres cambiar el texto del botón (opcional)
                  if (nombre.toLowerCase() === "aenas") {
                      $("#btnguardar").text("Registrado");
                  }
              

                } else {
                    $(".alert").removeClass("alert-success").addClass("alert alert-danger");
                    $(".alert").html(data.msj);

                    // Rehabilita el botón si el registro falló
                    if (nombre.toLowerCase() === "aenas") {
                        $("#btnguardar").prop("disabled", false);
                    }
                }
            }, 'json');
        });


        //recuperar clave
        $(".claverecuperar").on("click", function() {                
          let correo=$("#correoinicio").val().trim();
          if (correo) {
              $.post("herramientas/llave/ctrllave.php", {
                  correoinicio: correo
              }, function(data) {
                  $("#mensaje").html(data.msjValidez);
              }, 'json');
          } else {
              //alert("Digita correo para recuperar clave ");
              $("#mensaje").html("<span style='color:red;'>Digite correo para recuperar clave</span>");
              $("#correoinicio").focus();
              
          }
        }); 

        function cargarSelectTD(){      
          $.post("include/ctrlindex.php",{ 
              action:'selectTipoD', 
              documento:$('#documento').val() // la ultima linea no lleva coma.
            }, function(data){
              $("#selectTipoDoc").html(data.optionTipoDocumento);
          }, 'json');           
        }

        
          // alert('Holaaaaa');

         
          function cargarSelectdepartamento(){         
          $.post("include/ctrlindex.php",{ 
              action:'cargarSelectdepartamento', 
              documento:$('#documento').val() // la ultima linea no lleva coma.
            }, function(data){
              
             
               $("#selecdepto").html(data.optiondepto);
          }, 'json');            
        }
        
        cargarSelectTD();
        cargarSelectdepartamento(); 
            
    });  
  </script>
</head>
<body >
  
	<div class="login">
	    <h1>Inicio Sesión</h1>   
    	<input type="text" id="correoinicio" name="correoinicio" placeholder="Correo" required="required" />
      <input type="password" id="claveinicio" name="claveinicio" placeholder="Clave" required="required" />

      <button type="button" id="btningresar" name="btningresar" class="btn btn-primary btn-block btn-large"> <i class="fas fa-sign-in-alt"></i> Ingresar</button>
      <button class="btn btn-secondary btn-block btn-large" id="btnregistrar" name="btnregistrar" data-bs-toggle="modal" data-bs-target="#registroModal">
          <i class="fas fa-user-plus"></i> Registrarse
      </button>
      <button type="button" class="claverecuperar" name="claverecuperar" id="claverecuperar">Olvidé mi clave</button>
      <div id="mensaje" style="color:red"></div>
    </div>

        <!-- Modal de Registro -->
          <div class="modal fade" id="registroModal" tabindex="-1" aria-labelledby="registroModalLabel" aria-hidden="true">
            <div class="modal-dialog">
              <div class="modal-content">
                  <div class="modal-header">
                    <h5 class="modal-title">Registro de Usuario</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                  </div>
                  <div class="modal-body">
                    <div class="mb-3">
                       <div class="input-group">
                          <span class="input-group-text"><ion-icon name="person-sharp" style="color: blue"></ion-icon></span>
                          <input type="text" class="form-control" id="nombre" name="nombre"  placeholder="Nombre" required>
                      </div>
                     </div>
                     
                    <div class="mb-3">
                      <div class="input-group ">
                          <span class="input-group-text"><ion-icon name="person-sharp" style="color:blue"></ion-icon></span>
                          <input type="text" class="form-control" id="apellidos" name="apellidos"  placeholder="Apellido" required>
                      </div>
                    </div>
                    <div class="col-12">
                        <div class="input-group">
                          <span class="input-group-text"><i class="fas fa-address-card" title="Tipo de documento" style="color:blue;"></i></span>
                          <select class="form-select" id="selectTipoDoc" name="selectTipoDoc" title="Tipo de documento" >
                          </select>
                        </div>
                    </div>
                    <div class="col-12">
                      <div class="input-group">
                        <span class="input-group-text"><ion-icon name="card-outline" style="color:blue;"></ion-icon></span>
                        <input type="number" class="form-control" id="identificacion" name="identificacion"  placeholder="Identificación" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <div class="input-group">
                        <span class="input-group-text"><ion-icon name="paper-plane-outline" style="color:blue;"></ion-icon></span>
                        <input type="text" class="form-control" id="direccion" name="direccion"  placeholder="Direccion" required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <div class="input-group">
                        <span class="input-group-text"><ion-icon name="call-outline" style="color:blue;"></ion-icon></span>
                        <input type="tel" class="form-control" id="telefono" name="telefono"   placeholder="Telefono" required>
                      </div>
                    </div>

                    <div class="col-12">
                      <div class="input-group">
                        <span class="input-group-text"><ion-icon name="earth-outline" title="Departamento" style="color:blue;"></ion-icon></span>
                        <select class="form-select" id="selecdepto" name="selecdepto"  title="Departamento" >
                          <option value="" selected disabled>Seleccione un departamento</option>
                        </select>
                      </div>
                    </div>

                    <div class="mb-3">
                      <div class="input-group">
                        <span class="input-group-text"><ion-icon name="mail-open-outline" style="color:blue;"></ion-icon></span>
                        <input type="email" class="form-control" id="correoregistro" name="correoregistro"   placeholder="Correo"required>
                      </div>
                    </div>
                    <div class="mb-3">
                      <div class="input-group">
                        <span class="input-group-text"><ion-icon name="help-outline" style="color:blue;"></ion-icon></span>
                        <input type="password" class="form-control" id="claveregistro" name="claveregistro"  placeholder="Clave" required>
                      </div>
                    </div>
                    <div class="alert" role="alert" > 

                    </div>
                  </div>
                  <div class="modal-footer">
                    <button type="button" id="btnguardar" class="btn btn-primary" >Registrar</button>
                    <button type="button" id="btnCancelar" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                  </div>              
              </div>
            </div>
          </div>
  
</body>
</html>