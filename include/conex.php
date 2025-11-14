<?php
// conex.php - Versión corregida y funcional

// ✅ INCLUIR CONFIG_ENV PRIMERO
$config_env_path = __DIR__ . '/config_env.php';
if (file_exists($config_env_path)) {
    require_once $config_env_path;
} else {
    error_log("Config env no encontrado");
}

function Conectarse() {
    // ✅ CARGAR VARIABLES CON VALORES POR DEFECTO SEGUROS
    if (function_exists('getEnvVar')) {
        $servername = getEnvVar('DB_HOST', 'localhost');
        $db = getEnvVar('DB_NAME', 'subastas');
        $username = getEnvVar('DB_USER', 'root');
        $password = getEnvVar('DB_PASS', 'root'); // ✅ PASSWORD POR DEFECTO
        $charset = getEnvVar('DB_CHARSET', 'utf8mb4');
    } else {
        // ✅ VALORES TEMPORALES PARA PRUEBAS
        $servername = 'localhost';
        $db = 'subastas';
        $username = 'root';
        $password = 'root'; // ← Tu contraseña de MySQL
        $charset = 'utf8mb4';
    }
    
    // ✅ CONEXIÓN DIRECTA
    $conectar = mysqli_connect($servername, $username, $password, $db);
    
    if (!$conectar) {
        $error = mysqli_connect_error();
        error_log("Error MySQL: " . $error);
        
        // Mensajes de error específicos
        if (strpos($error, '1045') !== false) { // Access denied
            // ✅ SOLUCIÓN TEMPORAL: Intentar sin contraseña (común en XAMPP/WAMP)
            $conectar = mysqli_connect($servername, $username, '', $db);
            if ($conectar) {
                return $conectar;
            }
            die("Error de acceso: Verifica usuario/contraseña de MySQL");
        } elseif (strpos($error, '1049') !== false) { // Unknown database
            die("Error: La base de datos '$db' no existe.");
        } else {
            die("Error de conexión: " . $error);
        }
    } else {
        mysqli_set_charset($conectar, $charset);
        return $conectar;
    }
}

// Resto de funciones permanecen igual...
function ejecutarConsultaSegura($sql, $tipos = "", $parametros = []) {
    $conn = Conectarse();
    $stmt = mysqli_prepare($conn, $sql);
    
    if (!$stmt) {
        error_log("Error en consulta: " . mysqli_error($conn));
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
        error_log("Error ejecutando: " . mysqli_stmt_error($stmt));
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