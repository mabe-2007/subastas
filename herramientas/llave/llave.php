<?php
// llave.php - Usar función centralizada

// Incluir config_env para tener getEnvVar
require_once __DIR__ . '/../../include/config_env.php';

$session_name = getEnvVar('SESSION_NAME', "Sessionsubastas");

// Configuración de email
$email_remitente = getEnvVar('EMAIL_REMITENTE', "astridmabesoy@gmail.com");
$password = getEnvVar('EMAIL_PASSWORD', '');

// Si no hay variable de entorno, usa un archivo de configuración externo
if (empty($password)) {
    $config_file = __DIR__ . '/../../config/email_config.php';
    if (file_exists($config_file)) {
        $config = include_once $config_file;
        $password = isset($config['email_password']) ? $config['email_password'] : '';
    }
}
?>