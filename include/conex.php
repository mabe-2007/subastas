<?php
// conex.php - Versión definitiva SIN getEnvVar

// Incluir configurador de variables de entorno
require_once 'config_env.php';

function Conectarse() {
    // ✅ USAR FUNCIÓN DE config_env.php
    $servername = getEnvVar('DB_HOST', 'localhost');
    $db = getEnvVar('DB_NAME', 'subastas');
    $username = getEnvVar('DB_USER', 'root');
    $password = getEnvVar('DB_PASS', 'root');
    
    // ✅ CONEXIÓN
    $conectar = mysqli_connect($servername, $username, $password, $db);
    
    if (!$conectar) {
        $error = mysqli_connect_error();
        error_log("Error de conexión MySQL: " . $error);
        
        if (strpos($error, 'Unknown database') !== false) {
            die("Error: La base de datos '$db' no existe. Crea la base de datos 'subastas' primero.");
        } elseif (strpos($error, 'Access denied') !== false) {
            die("Error: Acceso denegado. Verifica usuario/contraseña de MySQL.");
        } else {
            die("Error de conexión MySQL: " . $error);
        }
    } else {
        $charset = getEnvVar('DB_CHARSET', 'utf8mb4');
        mysqli_set_charset($conectar, $charset);
        return $conectar;
    }
}

// Resto de funciones (sin getEnvVar)
function ejecutarConsultaSegura($sql, $tipos = "", $parametros = []) {
    $conn = Conectarse();
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        error_log("Error preparando consulta: " . mysqli_error($conn));
        mysqli_close($conn);
        return false;
    }
    
    if (!empty($tipos) && !empty($parametros)) {
        mysqli_stmt_bind_param($stmt, $tipos, ...$parametros);
    }
    
    if (mysqli_stmt_execute($stmt)) {
        $result = mysqli_stmt_get_result($stmt);
        mysqli_stmt_close($stmt);
        mysqli_close($conn);
        return $result;
    } else {
        error_log("Error ejecutando consulta: " . mysqli_stmt_error($stmt));
        mysqli_stmt_close($stmt);
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