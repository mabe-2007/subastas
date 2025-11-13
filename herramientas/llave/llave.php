<?php
// liave.php - Version segura

$session_name = "Sessionsubastas";

// Configuración de email (sin contraseña)
$email_remitente = "astridmabesoy@gmail.com";

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
}
?>