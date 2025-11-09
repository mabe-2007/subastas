<?php
require_once('config.php');
header('Content-Type: text/html; charset='.$charset);
header('Cache-Control: no-cache, must-revalidate');
// include('herramientas/llave/ctrllave.php');
include('conex.php');
session_name($session_name);
session_start();
$conEctar=Conectarse();
// ECHO "Clave : ".$_POST['clave']."<br>";
// ECHO "Usuario: ".$_POST['correo']; exit();

switch($_POST['action']) {
  case 'guardarSubasta':
    $jTableResult = array();
    $jTableResult['msj'] = "";
    $jTableResult['rspst'] = "";
    
    // Verificar si el usuario existe (opcional)
    //  $query = "SELECT idusuario FROM usuario WHERE idusuario = '".$_SESSION['idusuario']."'";
    $usuario = $_SESSION ['idusuraio'];
    exit;

    $registros = mysqli_query($conEctar, $query);
    $numero = mysqli_num_rows($registros);
    
    if($numero == 0) {
        $jTableResult['rspst'] = "0";
        $jTableResult['msj'] = "USUARIO NO EXISTE";
    } else {
        // Recoger datos del formulario
        $nombre = $_POST['nombre'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['preciosalida'];
        $fechalimite = $_POST['fechalimite'];
        $idcategoria = $_POST['idcategoria'];
        $idestado = $_POST['idestado'];
        $idusuario = $_SESSION['idusuario'];
        
        // Insertar en tabla articulo
        $query = "INSERT INTO articulo 
                 (idusuario, nombre, descripcion, idestado, preciosalida, fechalimite) 
                 VALUES 
                 ('$idusuario', '$nombre', '$descripcion', '$idestado', '$precio', '$fechalimite')";
        
        if(mysqli_query($conEctar, $query)) {
            $idarticulo = mysqli_insert_id($conEctar);
            
            // Insertar en tabla articulocategoria
            $query2 = "INSERT INTO articulocategoria 
                      (idarticulo, idcategoria) 
                      VALUES 
                      ('$idarticulo', '$idcategoria')";
            
            if(mysqli_query($conEctar, $query2)) {
                $jTableResult['rspst'] = "1";
                $jTableResult['msj'] = "SUBASTA CREADA CON ÉXITO";
            } else {
                $jTableResult['rspst'] = "0";
                $jTableResult['msj'] = "ERROR AL ASIGNAR CATEGORÍA: " . mysqli_error($conEctar);
            }
        } else {
            $jTableResult['rspst'] = "0";
            $jTableResult['msj'] = "ERROR AL CREAR SUBASTA: " . mysqli_error($conEctar);
        }
    }
    
    print json_encode($jTableResult);
    break;
}
?>