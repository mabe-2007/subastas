<?php
//https://getbootstrap.com/docs/5.0/components/modal/
include_once('conex.php');
include_once('config.php');
// include ('../../herramientas/llave/llave.php'); 
header('Content-Type: text/html; charset='.$charset);
header('Cache-Control: no-cache, must-revalidate');
//session_name($session_name);
session_start();
require '../../herramientas/PHPMailermas/src/Exception.php';
require '../../herramientas/PHPMailermas/src/PHPMailer.php';
require '../../herramientas/PHPMailermas/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

//$conn=Conectarse();
function generarClave($longitud = 8) {
	$caracteres = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$longitud_caracteres = strlen($caracteres);
	$clave = '';
	for ($i = 0; $i < $longitud; $i++) {
		$clave .= $caracteres[rand(0, $longitud_caracteres - 1)];
	}
	return $clave;
}

function enviarCorreo($email_remitente, $email_destinatario, $password, $nueva_clave) {
    $mail = new PHPMailer(true);

    try {
        // Configuración del servidor SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Servidor SMTP de Gmail
        $mail->SMTPAuth = true;
        $mail->Username = $email_remitente; // Tu dirección de correo de Gmail
        $mail->Password = $password; // Tu contraseña de Gmail
        $mail->SMTPSecure = 'tls'; // Activa la encriptación TLS
        $mail->Port = 587; // Puerto TCP para TLS

        // Remitente y destinatario 
        $mail->setFrom($email_remitente, 'Bili');
        $mail->addAddress($email_destinatario); // Dirección de correo del destinatario

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Restablecimiento de Clave';

        $mail->addEmbeddedImage(__DIR__. '/../estilo/img/logo.jpeg', 'logoimg'); // Asegúrate de que la ruta sea correcta EL dir es el directorio del script que llama a esta función
       
        $mail->Body = "
        <html>
        <head>
            <title>Recuperacion de clave programa SENA</title>
        </head>
        <body style='font-family: Arial, sans-serif;'>
       
        <table style='width: 100%; border-collapse: collapse;'>
            <tr>
                <td style='text-align: center;'>
                    <img src='cid:logoimg' alt='Logo' style='width: 100px; height: auto;'>
                    
                </td>
            </tr>
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'>Hemos recibido una solicitud para restablecer tu clave, por favor utilize 
                la clave enviada:</td>
            </tr>
             <tr>
                <td style='padding: 10px; border: 1px solid #ddd;'> Tu correo es:
                <strong>$email_destinatario</strong></td> 
            </tr>
             
            <tr>
                <td style='padding: 10px; border: 1px solid #ddd; background-color:#99E6D8;'>La nueva clave es:
                <strong>$nueva_clave</strong></td> 
            </tr>
            
        </table>
        <tr>
            <td style='padding: 10px; text-align: justify;'>
                <p> Si no has solicitado este cambio, puedes ignorar este mensaje.
                    Si has solicitado el cambio de clave, por favor ingresa a la plataforma con la clave dada.
                    Por favor, inicia sesión y cambia ingresa con la clave lo antes posible.
                </p>
            </td>
        </tr>
        <tr>
            <td style='padding: 10px; text-align: right; font-style: italic;'>
                <p>Horarios de atencion: Lunes-Viernes:7am a 7pm  Sabado: 8am a 1pm</p>

            </td>
        </tr>
        
        </body>
        </html>
        ";


        // Enviar el correo
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}

