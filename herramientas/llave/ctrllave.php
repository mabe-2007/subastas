<?php
include '../../herramientas/llave/llave.php';
include '../../include/enviaremail.php';
$link = Conectarse();

$email_destinatario = $_POST['correoinicio'];
$jTableResult = array();
$jTableResult['msjValidez'] = "";	

//VALIDACIÓN 
if (filter_var($email_destinatario, FILTER_VALIDATE_EMAIL)) {
    //CONSULTA PARA EVITAR SQL INJECTION
    $sql = "SELECT usuario.correo FROM usuario WHERE correo = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email_destinatario);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    
    if($result && mysqli_num_rows($result) > 0){
        $caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $longitud_caracteres = strlen($caracteres);
        $clave = '';
        for ($i = 0; $i < 8; $i++) {
            $clave .= $caracteres[rand(0, $longitud_caracteres - 1)];
        }

        $resul = enviarCorreo($email_remitente, $email_destinatario, $password, $clave);
        
        if($resul === true){ //comparación estricta
            $jTableResult['rspst'] = 1;
            $jTableResult['msjValidez'] = "Se envió un correo de recuperación";
            
            // ✅ ACTUALIZACIÓN SEGURA
            $sql = "UPDATE usuario SET clave = ? WHERE usuario.correo = ?";
            $stmt = mysqli_prepare($link, $sql);
            mysqli_stmt_bind_param($stmt, "ss", $clave, $email_destinatario);
            mysqli_stmt_execute($stmt);
            
        } else {
            $jTableResult['rspst'] = 3;
            $jTableResult['msjValidez'] = "No se pudo enviar el correo de recuperación";
        }

    } else {
        $jTableResult['rspst'] = 2;
        $jTableResult['msjValidez'] = "El correo no está registrado";
    } 
    mysqli_stmt_close($stmt);
}else{
    $jTableResult['rspst'] = 0;
    $jTableResult['msjValidez'] = "Correo inválido";
}

header('Content-Type: application/json');
echo json_encode($jTableResult);
mysqli_close($link);
exit;
?>