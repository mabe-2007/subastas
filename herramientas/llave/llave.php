<?php
// llave.php - Versión segura

$session_name = "Sessionsubastas";

// ✅ NUNCA pongas contraseñas directamente en el código
// ✅ Usa variables de entorno o archivos de configuración separados

// Configuración de email (sin contraseña)
$email_remitente = 'astridmabesoy@gmail.com';

// La contraseña debe venir de variables de entorno
$password = getenv('EMAIL_PASSWORD');

// Si no hay variable de entorno, usa un archivo de configuración externo
if (!$password) {
    // Intenta cargar desde archivo de configuración externo
    $config_file = __DIR__ . '/../../config/email_config.php';
    if (file_exists($config_file)) {
        include $config_file;
    } else {
        // Fallback - pero esto debería manejarse mejor
        die('Error: Configuración de email no encontrada');
    }
}
?>