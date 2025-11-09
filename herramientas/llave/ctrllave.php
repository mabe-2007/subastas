<?php
include '../../herramientas/llave/llave.php';
include '../../include/enviaremail.php';
$link = Conectarse();

$email_destinatario = $_POST['correoinicio'];
$jTableResult = array();
$jTableResult['msjValidez'] = "";	


    if (filter_var($email_destinatario, FILTER_VALIDATE_EMAIL)) {
        $sql = "SELECT usuario.correo FROM usuario WHERE correo = '$email_destinatario'";
        $result = mysqli_query($link, $sql);
        if($result && mysqli_num_rows($result) > 0){
            $row= mysqli_fetch_assoc($result);

                $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
                $longitud_caracteres = strlen($caracteres);
                $clave = '';
                for ($i = 0; $i < 8; $i++) {
                    $clave .= $caracteres[rand(0, $longitud_caracteres - 1)];
                }
        // echo"Nueva Clave".$clave;
        // echo"clave".$email_destinatario; exit();

      $resul=enviarCorreo($email_remitente, $email_destinatario, $password, $clave);
            if($resul = true){
                $jTableResult['rspst'] = 1;
                $jTableResult['msjValidez'] = "Se envió un correo de recuperación";
                $sql="UPDATE usuario SET clave = '$clave' WHERE usuario.correo = '$email_destinatario'";
                $result=mysqli_query($link, $sql);
                
            } else {
                $jTableResult['rspst'] = 3;
                $jTableResult['msjValidez'] = "No se pudo enviar el correo de recuperación";
            }

        } else {
            $jTableResult['rspst'] = 2;
            $jTableResult['msjValidez'] = "El correo no está registrado";
        } 
    }else{
        $jTableResult['rspst'] = 0;
        $jTableResult['msjValidez'] = "Correo inválido";
    }
    header('Content-Type: application/json');
    echo json_encode($jTableResult);
    exit;


?>

