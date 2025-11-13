<?php
// llave.php - Versión segura

$session_name = "Sessionsubastas";

// Configuración de email (sin contraseña)
$email_remitente = 'astridmabesoy@gmail.com';

// La contraseña debe venir de variables de entorno
$password = getenv('EMAIL_PASSWORD') ?: $_ENV['EMAIL_PASSWORD'] ?? null;

// Si no hay variable de entorno, usa un archivo de configuración externo
if (!$password) {
    // Intenta cargar desde archivo de configuración externo
    $config_file = __DIR__ . '/../../config/email_config.php';
    if (file_exists($config_file)) {
        $config = include $config_file;
        $password = $config['email_password'] ?? null;
    }
    
    if (!$password) {
        // Log del error en lugar de die() para producción
        error_log('Error: Configuración de email no encontrada');
        // Manejo más elegante según tu aplicación
        throw new Exception('Configuración de email no disponible');
    }
}
?>