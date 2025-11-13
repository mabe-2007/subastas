<?php
// Incluir configurador de variables de entorno
require_once 'config_env.php';

function Conectarse() {
    // ✅ USAR VARIABLES DE ENTORNO
    $servername = $_ENV['DB_HOST'] ?? 'localhost';
    $db = $_ENV['DB_NAME'] ?? 'subastas';
    $username = $_ENV['DB_USER'] ?? '';
    $password = $_ENV['DB_PASS'] ?? '';
    
    // Validar que tengamos credenciales
    if (empty($username) || empty($password)) {
        error_log("Error: Credenciales de BD no configuradas en .env");
        die("Error de configuración. Contacte al administrador.");
    }
    
    // ✅ CONEXIÓN SEGURA
    $conectar = mysqli_connect($servername, $username, $password, $db);
    
    if (!$conectar) {
        error_log("Error de conexión a la base de datos: " . mysqli_connect_error());
        die("Error de conexión a la base de datos. Por favor, intente más tarde.");
    } else {
        $charset = $_ENV['DB_CHARSET'] ?? 'utf8mb4';
        mysqli_set_charset($conectar, $charset);
        return $conectar;
    }
}

// Resto de tus funciones...
function ejecutarConsulta($sql, $tipos = "", $parametros = []) {
    $conn = Conectarse();
    $stmt = mysqli_prepare($conn, $sql);
    
    if ($stmt && !empty($tipos) && !empty($parametros)) {
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
    }
    
    if ($stmt && mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_close($conn);
        return $result;
    } else {
        error_log("Error en consulta: " . mysqli_error($conn));
        mysqli_close($conn);
        return false;
    }
}

function cerrarConexion($conexion) {
    if ($conexion) {
        mysqli_close($conexion);
    }
}
?>