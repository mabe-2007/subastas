<?php
include '../herramientas/llave/llave.php';
include 'emailregistro.php';
require_once('config.php');
include_once 'conex.php'; // ✅ Incluir la nueva conexión segura

header('Content-Type: application/json; charset='.$charset);
header('Cache-Control: no-cache, must-revalidate');

session_name($session_name);
session_start();

$action = $_POST['action'] ?? '';

switch ($action) {
    case 'selectTipoD':			
        $jTableResult = array();
        $jTableResult['optionTipoDocumento'] = "";
        
        // ✅ CONSULTA PREPARADA SEGURA
        $sql = "SELECT idTipoDocumento, nombreTipoDocumento FROM tipodocumento";
        $result = ejecutarConsulta($sql);
        
        if ($result) {
            while($registro = mysqli_fetch_array($result)) {			
                // ✅ SANITIZAR SALIDA PARA PREVENIR XSS
                $jTableResult['optionTipoDocumento'] .= "<option value='".$registro['idTipoDocumento']."'>" 
                    . htmlspecialchars($registro['nombreTipoDocumento'], ENT_QUOTES, 'UTF-8') . "</option>";												
            }
        } else {
            $jTableResult['optionTipoDocumento'] = "<option value=''>Error cargando tipos</option>";
        }
        
        echo json_encode($jTableResult);
        break;
    
    case 'cargarSelectdepartamento':			
        $jTableResult = array();
        $jTableResult['rspst'] = 1;
        $jTableResult['optiondepto'] = "";
        
        // ✅ CONSULTA PREPARADA SEGURA
        $sql = "SELECT iddepartamento, departamento FROM departamentos";
        $result = ejecutarConsulta($sql);
        
        if ($result) {
            while($registro = mysqli_fetch_array($result)) {			
                // ✅ SANITIZAR SALIDA PARA PREVENIR XSS
                $jTableResult['optiondepto'] .= "<option value='".$registro['iddepartamento']."'>" 
                    . htmlspecialchars($registro['departamento'], ENT_QUOTES, 'UTF-8') . "</option>";												
            }
        } else {
            $jTableResult['optiondepto'] = "<option value=''>Error cargando departamentos</option>";
        }
        
        echo json_encode($jTableResult);
        break;

    case 'iniciar':
        $jTableResult = array();
        
        // ✅ SANITIZAR ENTRADAS
        $correo = filter_input(INPUT_POST, 'correo', FILTER_SANITIZE_EMAIL);
        $clave = $_POST['clave'] ?? '';
        
        // Validar que no estén vacíos
        if(empty($correo) || empty($clave)) {
            $jTableResult['rspst'] = "0";    
            $jTableResult['msj'] = "Por favor complete todos los campos";
            echo json_encode($jTableResult);
            break;
        }
        
        // Validar formato de email
        if(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $jTableResult['rspst'] = "0";    
            $jTableResult['msj'] = "Por favor ingrese un email válido";
            echo json_encode($jTableResult);
            break;
        }

        // ✅ CONSULTA PREPARADA SEGURA
        $sql = "SELECT idusuario, correo, nombre, apellidos, clave, idrol 
                FROM usuario WHERE correo = ?";
        $result = ejecutarConsulta($sql, "s", [$correo]);
        
        if($result && mysqli_num_rows($result) > 0) {
            $user = mysqli_fetch_assoc($result);
            
            // ⚠️ ADVERTENCIA: Esto debería cambiarse a password_verify() cuando implementes hash
            if($clave === $user['clave']) {
                $jTableResult['rspst'] = "1"; 
                $_SESSION['idusuario'] = $user['idusuario'];
                $_SESSION['correo'] = $user['correo'];
                $_SESSION['idrol'] = $user['idrol'];
                $_SESSION['usuarioLogueado'] = htmlspecialchars($user['nombre']." ".$user['apellidos']);
            } else {
                $jTableResult['rspst'] = "0";    
                $jTableResult['msj'] = "Credenciales incorrectas"; 
            }
        } else {
            $jTableResult['rspst'] = "0";    
            $jTableResult['msj'] = "Usuario no encontrado";
        }
        
        echo json_encode($jTableResult);
        break;	
    
    case 'registrarUsuario':
        $jTableResult = array();
        
        // ✅ SANITIZAR TODAS LAS ENTRADAS
        $correo = filter_input(INPUT_POST, 'correoregistro', FILTER_SANITIZE_EMAIL);
        $nombre = filter_input(INPUT_POST, 'nombre', FILTER_SANITIZE_STRING);
        $apellido = filter_input(INPUT_POST, 'apellido', FILTER_SANITIZE_STRING);
        $direccion = filter_input(INPUT_POST, 'direccion', FILTER_SANITIZE_STRING);
        $TipoDocumento = (int)($_POST['idTipoDocumento'] ?? 0);
        $identificacion = filter_input(INPUT_POST, 'identificacion', FILTER_SANITIZE_STRING);
        $telefono = filter_input(INPUT_POST, 'telefono', FILTER_SANITIZE_STRING);
        $iddepto = (int)($_POST['iddepto'] ?? 0);
        $claveregistro = $_POST['claveregistro'] ?? '';

        // ✅ VALIDACIONES COMPLETAS
        if(!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            $jTableResult['rspst'] = "0";
            $jTableResult['msj'] = "Correo inválido";
            echo json_encode($jTableResult);
            break;
        }

        if(empty($nombre) || empty($claveregistro)) {
            $jTableResult['rspst'] = "0";
            $jTableResult['msj'] = "Nombre y clave son obligatorios";
            echo json_encode($jTableResult);
            break;
        }

        if(strlen($claveregistro) < 6) {
            $jTableResult['rspst'] = "0";
            $jTableResult['msj'] = "La contraseña debe tener al menos 6 caracteres";
            echo json_encode($jTableResult);
            break;
        }

        // ✅ VERIFICAR SI EL CORREO EXISTE (CONSULTA PREPARADA)
        $sql_check = "SELECT idusuario FROM usuario WHERE correo = ?";
        $result_check = ejecutarConsulta($sql_check, "s", [$correo]);
        
        if($result_check && mysqli_num_rows($result_check) > 0) {
            $jTableResult['rspst'] = "0";    
            $jTableResult['msj'] = "El correo ya está registrado"; 
        } else {
            $idrol = 1;
            
            // ✅ CONSULTA PREPARADA SEGURA PARA INSERTAR
            $sql_insert = "INSERT INTO usuario (idrol, nombre, apellidos, direccion, 
                          idTipoDocumento, identificacion, telefono, correo, clave, iddepartamento) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
            $result_insert = ejecutarConsulta($sql_insert, "isssissssi", [
                $idrol, $nombre, $apellido, $direccion, $TipoDocumento,
                $identificacion, $telefono, $correo, $claveregistro, $iddepto
            ]);
            
            if($result_insert) {
                $result_email = enviarCorreo($email_remitente, $correo, $password, $claveregistro, $nombre, $apellido);
                if($result_email) {
                    $_SESSION['usuarioRegistrado'] = true;
                    $jTableResult['rspst'] = "1";    
                    $jTableResult['msj'] = "Registro exitoso"; 
                } else {
                    $jTableResult['rspst'] = "1";
                    $jTableResult['msj'] = "Registro completado pero no se pudo enviar el correo"; 
                }
            } else {
                $jTableResult['rspst'] = "0";
                $jTableResult['msj'] = "Error en el registro";
            }
        }
        
        echo json_encode($jTableResult);
        break;
    
    case 'salir':
        session_destroy();
        echo json_encode(['rspst' => '1']);
        break;

    default:
        echo json_encode(['rspst' => '0', 'msj' => 'Acción no válida']);
}
?>