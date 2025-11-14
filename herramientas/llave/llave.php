<?php
// llave.php - Versión segura

//  EFINIR CONSTANTE PARA EL EMAIL
define('EMAIL_DEFAULT', 'astridmabesoy@gmail.com');

// Incluir config_env.php de forma segura
if (file_exists(__DIR__ . '/../../include/config_env.php')) {
    require_once __DIR__ . '/../../include/config_env.php';
    
    // Usar getEnvVar de forma segura
    if (function_exists('getEnvVar')) {
        $session_name = getEnvVar('SESSION_NAME', "Sessionsubastas");
        $email_remitente = getEnvVar('EMAIL_REMITENTE', EMAIL_DEFAULT);
        $password = getEnvVar('EMAIL_PASSWORD', '');
    } else {
        // Fallback seguro sin contraseñas en código
        $session_name = "Sessionsubastas";
        $email_remitente = EMAIL_DEFAULT;
        $password = '';
    }
} else {
    // Sin contraseñas en código - usar solo variables de entorno
    $session_name = "Sessionsubastas";
    $email_remitente = EMAIL_DEFAULT;
    $password = '';
}

// Cargar contraseña desde archivo externo seguro
if (empty($password)) {
    $config_file = __DIR__ . '/../../../config/email_config.php'; // Fuera del directorio web
    if (file_exists($config_file)) {
        $config = include_once $config_file;
        $password = $config['email_password'];
    }
}

//Si no hay contraseña, mostrar error claro
if (empty($password)) {
    error_log("Error: Contraseña de email no configurada");
    // No exponer información sensible
}
?>