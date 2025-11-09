<?php
include '../../herramientas/llave/llave.php';
include '../../include/emailregistro.php';
$link = Conectarse();

$email_destinatario = $_POST['correoregistro'];
$jTableResult = array();
$jTableResult['msjValidez'] = "";	


    if (filter_var($email_destinatario, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT usuario.correo FROM usuario WHERE correo = '$email_destinatario'";
        $result = mysqli_query($link, $sql);
        if($result && mysqli_num_rows($result) > 0){
            $row= mysqli_fetch_assoc($result);
        // echo"Nueva Clave".$clave;
        // echo"clave".$email_destinatario; exit();

      $resul=enviarCorreo($email_remitente, $email_destinatario, $password, $clave);
            if($resul = true){
                $jTableResult['rspst'] = 1;
                $jTableResult['msjValidez'] = "Se envió un correo de registro";
            } else {
                $jTableResult['rspst'] = 3;
                $jTableResult['msjValidez'] = "No se pudo enviar el correo de registro";
            }
        } 
    }else{
        $jTableResult['rspst'] = 0;
        $jTableResult['msjValidez'] = "Correo inválido";
    }
    header('Content-Type: application/json');
    echo json_encode($jTableResult);
    exit;


?>

