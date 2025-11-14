<?php
// test_conexion_segura.php - Diagnóstico seguro
echo "<h3>Diagnóstico de Conexión Segura</h3>";

require_once 'include/config_env.php';

// Verificar configuración SIN mostrar contraseñas
echo "DB_HOST: " . (getEnvVar('DB_HOST') ?: 'NO CONFIGURADO') . "<br>";
echo "DB_USER: " . (getEnvVar('DB_USER') ?: 'NO CONFIGURADO') . "<br>";
echo "DB_PASS: " . (getEnvVar('DB_PASS') ? '***CONFIGURADO***' : 'NO CONFIGURADO') . "<br>";
echo "DB_NAME: " . (getEnvVar('DB_NAME') ?: 'NO CONFIGURADO') . "<br>";

// Verificar archivo externo
$config_file = __DIR__ . '/../../config/db_config.php';
echo "Archivo config externo: " . (file_exists($config_file) ? '✅ EXISTE' : '❌ NO EXISTE') . "<br>";

// Probar conexión
require_once 'include/conex.php';
$conn = Conectarse();

if ($conn) {
    echo "<span style='color: green;'>✅ CONEXIÓN SEGURA EXITOSA</span>";
    mysqli_close($conn);
} else {
    echo "<span style='color: red;'>❌ ERROR EN CONEXIÓN</span>";
}
?>