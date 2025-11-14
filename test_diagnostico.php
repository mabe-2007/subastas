<?php
// test_diagnostico.php - Diagnosticar conexión
echo "<h3>Diagnóstico de Conexión MySQL</h3>";

// Incluir config_env para cargar variables
require_once 'include/config_env.php';

// Verificar variables de entorno
echo "DB_HOST: " . (getEnvVar('DB_HOST') ?: 'NO CONFIGURADO') . "<br>";
echo "DB_USER: " . (getEnvVar('DB_USER') ?: 'NO CONFIGURADO') . "<br>";
echo "DB_PASS: " . (getEnvVar('DB_PASS') ? '***CONFIGURADO***' : 'NO CONFIGURADO') . "<br>";
echo "DB_NAME: " . (getEnvVar('DB_NAME') ?: 'NO CONFIGURADO') . "<br>";

// Probar conexión directa
$host = getEnvVar('DB_HOST', 'localhost');
$user = getEnvVar('DB_USER', 'root');
$pass = getEnvVar('DB_PASS', 'root'); // ← Usar 'root' como fallback
$db   = getEnvVar('DB_NAME', 'subastas');

echo "<br><strong>Intentando conectar con:</strong><br>";
echo "Host: $host<br>";
echo "Usuario: $user<br>";
echo "Base de datos: $db<br>";

$conn = mysqli_connect($host, $user, $pass, $db);
if ($conn) {
    echo "<span style='color: green;'>✅ CONEXIÓN EXITOSA</span>";
    mysqli_close($conn);
} else {
    echo "<span style='color: red;'>❌ ERROR: " . mysqli_connect_error() . "</span>";
}
?>