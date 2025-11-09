<?php
require_once('config.php');
header('Content-Type: text/html; charset='.$charset);
header('Cache-Control: no-cache, must-revalidate');
include('conex.php');
session_name($session_name);
session_start();
$conEctar=Conectarse();
// ECHO "Clave : ".$_POST['clave']."<br>";
// ECHO "Usuario: ".$_POST['correo']; exit();
$action = isset($_REQUEST['action']) ? $_REQUEST['action'] : '';
switch ($action) {
    case 'selectCategorias':
        $jTableResult = array();
        $jTableResult['msj'] = "";	
        $jTableResult['rspst'] = "";
        $jTableResult['optionCategorias'] = "";

        $query = "SELECT idcategoria, nombre FROM categoria";					
        $reg = mysqli_query($conEctar, $query);

        while($subastar = mysqli_fetch_array($reg)) {			
            $jTableResult['optionCategorias'] .= "<option value='".$subastar['idcategoria']."'>".$subastar['nombre']."</option>";												
        }
        print json_encode($jTableResult);
    break;
}
mysqli_close($conEctar);

?>