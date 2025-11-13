<?php
include '../herramientas/llave/llave.php';
include 'emailregistro.php';

require_once('config.php');
header('Content-Type: text/html; charset='.$charset);
header('Cache-Control: no-cache, must-revalidate');

session_name($session_name);
session_start();

// ✅ FUNCIÓN PARA VALIDAR Y LIMPIAR DATOS (COMPATIBLE CON PHP 5.x)
function limpiarDatos($dato) {
    $dato = trim($dato);
    $dato = stripslashes($dato);
    $dato = htmlspecialchars($dato);
    return $dato;
}
$conEctar = Conectarse();

switch ($_REQUEST['action']) {
    case 'selectTipoD':			
        $jTableResult = array();
        $jTableResult['msj'] = "";	
        $jTableResult['rspst'] = "";
        $jTableResult['optionTipoDocumento'] = "";
        
        // ✅ CONSULTA SEGURA
        $query = "SELECT idTipoDocumento, nombreTipoDocumento FROM tipodocumento";					
        $reg = mysqli_query($conEctar, $query);					
        while($registro = mysqli_fetch_array($reg)) {			
            $jTableResult['optionTipoDocumento'] .= "<option value='".$registro['idTipoDocumento']."'>".$registro['nombreTipoDocumento']."</option>";												
        }
        print json_encode($jTableResult);
        break;
    
    case 'cargarSelectdepartamento':			
        $jTableResult = array();
        $jTableResult['msj'] = "";	
        $jTableResult['rspst'] = 1;
        $jTableResult['optiondepto'] = "";
        
        // ✅ CONSULTA SEGURA
        $query = "SELECT iddepartamento, departamento FROM departamentos";					
        $reg = mysqli_query($conEctar, $query);		
        
        while($registro = mysqli_fetch_array($reg)) {			
            $jTableResult['optiondepto'] .= "<option value='".$registro['iddepartamento']."'>".$registro['departamento']."</option>";												
        }
        print json_encode($jTableResult);
        exit;
        break;

    case 'iniciar':
        $jTableResult = array();
        $jTableResult['msj'] = "";
        $jTableResult['rspst'] = "";
        
        // ✅ VALIDACIÓN Y LIMPIEZA DE DATOS (COMPATIBLE)
        $correo = isset($_POST['correo']) ? limpiarDatos($_POST['correo']) : '';
        $clave = isset($_POST['clave']) ? $_POST['clave'] : '';
        
        $correoinicio = strlen($correo);
        $claveinicio = strlen($clave);
        
        if($correoinicio > 0 and $claveinicio > 0) {
            // ✅ CONSULTA PREPARADA - SEGURA CONTRA INYECCIÓN SQL
            $query = "SELECT usuario.idusuario, usuario.correo, usuario.nombre,
                      usuario.apellidos, usuario.clave, usuario.idrol
                    FROM usuario
                    WHERE usuario.correo = ?";
                      
            $stmt = mysqli_prepare($conEctar, $query);
            mysqli_stmt_bind_param($stmt, "s", $correo);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $numero = mysqli_num_rows($result);
            
            if($numero > 0) { 
                $usuario = mysqli_fetch_assoc($result);
                
                // ✅ VERIFICAR CONTRASEÑA (compatible con texto plano temporalmente)
                if ($usuario['clave'] === $clave) {
                // Cuando implementes hash, usar:
                // if (password_verify($clave, $usuario['clave'])) {
                    
                    $jTableResult['rspst'] = "1"; 
                    $_SESSION['idusuario'] = $usuario['idusuario'];
                    $_SESSION['correo'] = $usuario['correo'];
                    $_SESSION['idrol'] = $usuario['idrol'];
                    $_SESSION['usuarioLogueado'] = $usuario['nombre']." ".$usuario['apellidos'];
                    
                    // ✅ REGENERAR ID DE SESIÓN POR SEGURIDAD
                    if (function_exists('session_regenerate_id')) {
                        session_regenerate_id(true);
                    }
                } else {
                    $jTableResult['rspst'] = "0";    
                    $jTableResult['msj'] = "CREDENCIALES INCORRECTAS"; 
                }
            } else {  
                $jTableResult['rspst'] = "0";    
                $jTableResult['msj'] = "CREDENCIALES INCORRECTAS"; 
            }
            mysqli_stmt_close($stmt);
        } else {
            $jTableResult['rspst'] = "0";    
            $jTableResult['msj'] = "NO EXISTEN DATOS";
        }					
        print json_encode($jTableResult);
        break;	
    
    //registro
    case 'registrarUsuario':
        $jTableResult = array();
        $jTableResult['msj'] = "";
        $jTableResult['rspst'] = "";
        
        // ✅ VALIDAR Y LIMPIAR TODOS LOS DATOS (COMPATIBLE)
        $correo = isset($_POST['correoregistro']) ? limpiarDatos($_POST['correoregistro']) : '';
        $nombre = isset($_POST['nombre']) ? limpiarDatos($_POST['nombre']) : '';
        $apellido = isset($_POST['apellido']) ? limpiarDatos($_POST['apellido']) : '';
        $direccion = isset($_POST['direccion']) ? limpiarDatos($_POST['direccion']) : '';
        $TipoDocumento = isset($_POST['idTipoDocumento']) ? intval($_POST['idTipoDocumento']) : 0;
        $identificacion = isset($_POST['identificacion']) ? limpiarDatos($_POST['identificacion']) : '';
        $telefono = isset($_POST['telefono']) ? limpiarDatos($_POST['telefono']) : '';
        $iddepto = isset($_POST['iddepto']) ? intval($_POST['iddepto']) : 0;
        $claveregistro = isset($_POST['claveregistro']) ? $_POST['claveregistro'] : '';
        
        // ✅ VALIDACIONES BÁSICAS
        if (empty($correo) || empty($nombre) || empty($claveregistro)) {
            $jTableResult['rspst'] = "0";
            $jTableResult['msj'] = "DATOS INCOMPLETOS";
            print json_encode($jTableResult);
            break;
        }
        
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $jTableResult['rspst'] = "0";
            $jTableResult['msj'] = "CORREO INVÁLIDO";
            print json_encode($jTableResult);
            break;
        }
        
        // ✅ VERIFICAR SI EL CORREO YA EXISTE (CONSULTA PREPARADA)
        $query = "SELECT usuario.idusuario FROM usuario WHERE usuario.correo = ?";	
        $stmt = mysqli_prepare($conEctar, $query);
        mysqli_stmt_bind_param($stmt, "s", $correo);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $numero = mysqli_num_rows($result);
        mysqli_stmt_close($stmt);
        
        if($numero > 0) {
            $jTableResult['rspst'] = "0";    
            $jTableResult['msj'] = "EL CORREO YA ESTÁ REGISTRADO"; 
        } else {	
            // ✅ HASH DE CONTRASEÑA SEGURO (si tu PHP lo soporta)
            if (function_exists('password_hash')) {
                $clave_hash = password_hash($claveregistro, PASSWORD_DEFAULT);
            } else {
                // Si no soporta password_hash, usar texto plano temporalmente
                $clave_hash = $claveregistro;
            }
            
            // Asignamos automáticamente idrol = 1 (usuario normal)
            $idrol = 1;
            
            // ✅ INSERT SEGURO CON CONSULTA PREPARADA
            $query = "INSERT INTO usuario (idusuario, idrol, nombre, apellidos, 
                      direccion, idTipoDocumento, identificacion, telefono, correo, clave, iddepartamento) 
                      VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $stmt = mysqli_prepare($conEctar, $query);
            mysqli_stmt_bind_param($stmt, "isssiisssi", 
                $idrol, $nombre, $apellido, $direccion, $TipoDocumento,
                $identificacion, $telefono, $correo, $clave_hash, $iddepto
            );
            
            if(mysqli_stmt_execute($stmt)) {
                // Intentar enviar correo (manejar errores)
                try {
                    $result = enviarCorreo($email_remitente, $correo, $password, $claveregistro, $nombre, $apellido);
                    if($result) {
                        $_SESSION['usuarioRegistrado'] = true;
                        $jTableResult['rspst'] = "1";    
                        $jTableResult['msj'] = "REGISTRO EXITOSO"; 
                    } else {
                        $jTableResult['rspst'] = "1"; // Registro exitoso pero correo falló
                        $jTableResult['msj'] = "REGISTRO EXITOSO, PERO NO SE PUDO ENVIAR EL CORREO DE CONFIRMACIÓN"; 
                    }
                } catch (Exception $e) {
                    $jTableResult['rspst'] = "1";
                    $jTableResult['msj'] = "REGISTRO EXITOSO (ERROR EN ENVÍO DE CORREO)"; 
                }
            } else {
                $jTableResult['rspst'] = "0";
                $jTableResult['msj'] = "ERROR EN EL REGISTRO: ".mysqli_error($conEctar);
            }
            mysqli_stmt_close($stmt);
        }					
        print json_encode($jTableResult);
        break;
    
    case 'salir':
        $jTableResult = array();
        $jTableResult['rspst'] = "";
        
        // ✅ DESTRUIR SESIÓN DE FORMA SEGURA
        $_SESSION = array();
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();	
        $jTableResult['rspst'] = "1";	
        print json_encode($jTableResult);
        break;
        
    default:
        $jTableResult = array();
        $jTableResult['rspst'] = "0";
        $jTableResult['msj'] = "ACCIÓN NO VÁLIDA";
        print json_encode($jTableResult);
        break;
}		

mysqli_close($conEctar);
?>