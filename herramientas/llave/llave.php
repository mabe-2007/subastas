<?php
// llave.php - Versión corregida y funcional

// ✅ DEFINIR CONSTANTE PARA EL EMAIL (como sugiere el analizador)
define('EMAIL_DEFAULT', 'astridmabesoy@gmail.com');

// ✅ CORREGIDO: Incluir config_env.php de forma segura
if (file_exists(__DIR__ . '/../../include/config_env.php')) {
    require_once __DIR__ . '/../../include/config_env.php';
    
    // ✅ CORREGIDO: Usar getEnvVar (no getEnWar)
    if (function_exists('getEnvVar')) {
        $session_name = getEnvVar('SESSION_NAME', "Sessionsubastas");
        $email_remitente = getEnvVar('EMAIL_REMITENTE', EMAIL_DEFAULT);
        $password = getEnvVar('EMAIL_PASSWORD', '');
    } else {
        // Fallback si la función no existe
        $session_name = "Sessionsubastas";
        $email_remitente = EMAIL_DEFAULT;
        $password = 'fach imiv bgez hutb';
    }
} else {
    // Si config_env.php no existe, usar valores directos
    $session_name = "Sessionsubastas";
    $email_remitente = EMAIL_DEFAULT;
    $password = 'fach imiv bgez hutb';
}

// ✅ CORREGIDO: Si no hay contraseña, usar archivo de configuración externo
if (empty($password)) {
    $config_file = __DIR__ . '/../../config/email_config.php';
    if (file_exists($config_file)) {
        $config = include $config_file;
        $password = isset($config['email_password']) ? $config['email_password'] : '';
    }
}

// ✅ Asegurar que siempre tengamos valores
if (empty($password)) {
    $password = 'fach imiv bgez hutb'; // Tu contraseña de aplicación
}
?>