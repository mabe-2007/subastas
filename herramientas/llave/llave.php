<?php
// llave.php - Versión corregida y funcional

// ✅ CORREGIDO: Usar require_once para incluir archivos
if (file_exists(__DIR__ . '/../../include/config_env.php')) {
    require_once __DIR__ . '/../../include/config_env.php';
} else {
    // Si no existe config_env, usar valores por defecto
    $session_name = "Sessionsubastas";
    $email_remitente = "astridmabesoy@gmail.com";
    $password = 'fach imiv bgez hutb'; // Tu contraseña de aplicación
    return; // Salir temprano
}

// ✅ CORREGIDO: Verificar si la función existe antes de usarla
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

// ✅ CORREGIDO: Si no hay variable de entorno, usar valor directo
if (empty($password)) {
    $password = 'fach imiv bgez hutb'; // Tu contraseña de aplicación de Gmail
}

// ✅ CORREGIDO: Opcional - intentar archivo de configuración externo
if (empty($password)) {
    $config_file = __DIR__ . '/../../config/email_config.php';
    if (file_exists($config_file)) {
        $config = include $config_file; // ✅ CORREGIDO: usar include, no use
        $password = isset($config['email_password']) ? $config['email_password'] : '';
    }
}
?>