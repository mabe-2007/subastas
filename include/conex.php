<?php
// llave.php - Usar función centralizada (VERSIÓN CORREGIDA)

// ✅ CORREGIDO: Usar require_once para incluir archivos, no 'use'
if (file_exists(__DIR__ . '/../../include/config_env.php')) {
    require_once __DIR__ . '/../../include/config_env.php';
    
    // Configuración usando getEnvVar si está disponible
    if (function_exists('getEnvVar')) {
        $session_name = getEnvVar('SESSION_NAME', "Sessionsubastas");
        $email_remitente = getEnvVar('EMAIL_REMITENTE', "astridmabesoy@gmail.com");
        $password = getEnvVar('EMAIL_PASSWORD', '');
    } else {
        // Fallback si la función no existe
        $session_name = "Sessionsubastas";
        $email_remitente = "astridmabesoy@gmail.com";
        $password = 'fach imiv bgez hutb';
    }
} else {
    // Si config_env.php no existe, usar valores directos
    $session_name = "Sessionsubastas";
    $email_remitente = "astridmabesoy@gmail.com";
    $password = 'fach imiv bgez hutb';
}

// ✅ CORREGIDO: Si no hay variable de entorno, usa un archivo de configuración externo
if (empty($password)) {
    $config_file = __DIR__ . '/../../config/email_config.php';
    if (file_exists($config_file)) {
        // ✅ CORREGIDO: Usar include, no 'use'
        $config = include $config_file;
        $password = isset($config['email_password']) ? $config['email_password'] : '';
    }
}

// ✅ Asegurar que siempre tengamos valores
if (empty($password)) {
    $password = 'fach imiv bgez hutb'; // Tu contraseña de aplicación
}
?>