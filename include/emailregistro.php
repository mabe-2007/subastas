<?php
//https://getbootstrap.com/docs/5.0/components/modal/
include_once('conex.php');
include_once('config.php');
// include ('../../herramientas/llave/llave.php'); 
header('Content-Type: text/html; charset='.$charset);
header('Cache-Control: no-cache, must-revalidate');
//session_name($session_name);

require '../herramientas/PHPMailermas/src/Exception.php';
require '../herramientas/PHPMailermas/src/PHPMailer.php';
require '../herramientas/PHPMailermas/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

function enviarCorreo($email_remitente, $email_destinatario, $password, $claveregistro, $nombre, $apellido ) {
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
      $mail->setFrom($email_remitente, "{$nombre} {$apellido}");
        $mail->addAddress($email_destinatario); // Dirección de correo del destinatario

        // Contenido del correo
        $mail->isHTML(true);
        $mail->Subject = 'Bienvenido te has registraoo correctamente ';

       $mail->addEmbeddedImage(__DIR__ . '/../estilo/img/logo.jpeg', 'logoimg');
          // Asegúrate de que la ruta sea correcta EL dir es el directorio del script que llama a esta función
       
        $mail->Body = "
            <html>
                <head>
                    <title>Bienvenido</title>
                </head>
                <tr>
                    <td style='text-align: center;'>
                        <img src='cid:logoimg' alt='Logo' style='width: 100px; height: auto;'>
                    </td>
                </tr>
                <body style='font-family: Arial, sans-serif; text-align: center;'>
                    <h2 style='color: #2c3e50;'>¡Bienvenido a la página de subastas!</h2>
                    <p style='font-size: 16px;'>
                        Nos alegra que te hayas registrado. Ahora puedes disfrutar de todas las funciones 
                        y participar en nuestras subastas.
                    </p>
                    <p style='color: #888; font-size: 14px;'>
                        ¡Mucha suerte en tus pujas!
                    </p>
                </body>
            </html>";


        // Enviar el correo
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Error al enviar correo: {$mail->ErrorInfo}");
        return false;
    }
}

