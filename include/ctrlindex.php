<?php
include '../herramientas/llave/llave.php';
include 'emailregistro.php';

require_once('config.php');
header('Content-Type: text/html; charset='.$charset);
header('Cache-Control: no-cache, must-revalidate');

session_name($session_name);
session_start();
$conEctar=Conectarse();

switch ($_REQUEST['action']) 
{
    case 'selectTipoD':			
        $jTableResult = array();
        $jTableResult['msj'] = "";	
        $jTableResult['rspst'] = "";
        $jTableResult['optionTipoDocumento']="";
        $query = "SELECT idTipoDocumento, nombreTipoDocumento FROM tipodocumento";					
        $reg = mysqli_query($conEctar, $query);					
        while($registro = mysqli_fetch_array($reg)) {			
            $jTableResult['optionTipoDocumento'].="<option value='".$registro['idTipoDocumento']."'>".$registro['nombreTipoDocumento']."</option>";												
        }
        print json_encode($jTableResult);
    break;
    
    case 'cargarSelectdepartamento':			
        $jTableResult = array();
        $jTableResult['msj'] = "";	
        $jTableResult['rspst'] = 1;
        $jTableResult['optiondepto'] = "";
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
        $jTableResult['msj']="";
        $jTableResult['rspst']="";
        $correoinicio = strlen($_POST['correo']);
        $claveinicio = strlen($_POST['clave']);
        
        if($correoinicio>0 and $claveinicio>0) {
            $query = "SELECT usuario.idusuario, usuario.correo, usuario.nombre,
                      usuario.apellidos, usuario.clave, usuario.idrol
                    FROM usuario
                    WHERE
                      usuario.correo = '".$_POST['correo']."'
                      AND usuario.clave= '".$_POST['clave']."'";
                      
            $registros = mysqli_query($conEctar, $query);
            $numero=mysqli_num_rows($registros);
            
            if($numero>0) { 
                while($regis = mysqli_fetch_array($registros)) {			
                    $jTableResult['rspst']= "1"; 
                    $_SESSION['idusuario']=$regis['idusuario'];
                    $_SESSION['correo']=$regis['correo'];
					$_SESSION['idrol']=$regis['idrol'];
                    $_SESSION['usuarioLogueado']=$regis['nombre']." ".$regis['apellidos'];				
                }
            }
            else {  
                $jTableResult['rspst']= "0";    
                $jTableResult['msj']="CREDENCIALES INCORRECTAS"; 
            }
        } else {
            $jTableResult['rspst']= "0";    
            $jTableResult['msj']="NO EXISTEN DATOS";
        }					
        print json_encode($jTableResult);
    break;	
    
    //registro
    case 'registrarUsuario':
    $jTableResult = array();
    $jTableResult['msj'] = "";
    $jTableResult['rspst'] = "";
    
    $query = "SELECT usuario.idusuario FROM usuario WHERE usuario.correo = '".$_POST['correoregistro']."'";	

    $registros = mysqli_query($conEctar, $query);
    $numero = mysqli_num_rows($registros);
    
    if($numero > 0) {
        $jTableResult['rspst'] = "0";    
        $jTableResult['msj'] = "EL CORREO YA ESTÁ REGISTRADO"; 
    }
    else {	
        $nombre = $_POST['nombre'];
        $apellido = $_POST['apellido'];
        $direccion = $_POST['direccion'];
        $TipoDocumento = $_POST['idTipoDocumento'];
        $identificacion = $_POST['identificacion'];
        $telefono = $_POST['telefono'];
        $iddepto = $_POST['iddepto'];
        $correoregistro = $_POST['correoregistro'];
        $claveregistro = $_POST['claveregistro'];
    
        // Asignamos automáticamente idrol = 1 (usuario normal)
        $idrol = 1;
        
        $query = "INSERT INTO usuario (idusuario, idrol, nombre, apellidos, 
                  direccion, idTipoDocumento, identificacion, telefono, correo, clave, iddepartamento) 
                  VALUES (NULL, $idrol, '$nombre', '$apellido', '$direccion', '$TipoDocumento',
                  '$identificacion', '$telefono', '$correoregistro', '$claveregistro', '$iddepto')";

        if(mysqli_query($conEctar, $query)) {
            $result = enviarCorreo($email_remitente, $correoregistro, $password, $claveregistro, $nombre, $apellido);
            if($result) {
                $_SESSION['usuarioRegistrado'] = true;
                $jTableResult['rspst'] = "1";    
                $jTableResult['msj'] = "REGISTRO EXITOSO"; 
            } else {
                $jTableResult['rspst'] = "0";    
                $jTableResult['msj'] = "REGISTRO COMPLETADO PERO NO SE PUDO ENVIAR EL CORREO"; 
            }
        } else {
            $jTableResult['rspst'] = "0";
            $jTableResult['msj'] = "ERROR EN EL REGISTRO: ".mysqli_error($conEctar);
        }
    }					
    print json_encode($jTableResult);
break;
    
    case 'salir':
        $jTableResult = array();
        $jTableResult['rspst']="";
        unset($_SESSION['idusuario']);
        unset($_SESSION['correo']);
        unset($_SESSION['usuarioLogueado']);
        session_destroy();	
        $jTableResult['rspst']="1";	
        print json_encode($jTableResult);
    break;
}		
mysqli_close($conEctar);
?>