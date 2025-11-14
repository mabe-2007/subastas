<?php
// conex.php - Versión final y limpia

// ✅ INCLUIR CONFIG_ENV PRIMERO
$config_env_path = __DIR__ . '/config_env.php';
if (file_exists($config_env_path)) {
    require_once $config_env_path;
}

function Conectarse() {
    // ✅ CARGAR VARIABLES DE FORMA SEGURA
    if (function_exists('getEnvVar')) {
        $servername = getEnvVar('DB_HOST', 'localhost');
        $db = getEnvVar('DB_NAME', 'subastas');
        $username = getEnvVar('DB_USER', 'root');
        $password = getEnvVar('DB_PASS', '');
        $charset = getEnvVar('DB_CHARSET', 'utf8mb4');
    } else {
        // ✅ SIN CONTRASEÑAS EN CÓDIGO
        $servername = 'localhost';
        $db = 'subastas';
        $username = 'root';
        $password = '';
        $charset = 'utf8mb4';
    }
    
    // ✅ CARGAR CONTRASEÑA DESDE ARCHIVO EXTERNO
    if (empty($password)) {
        $config_file = __DIR__ . '/../config/db_config.php';
        if (file_exists($config_file)) {
            $config = include $config_file;
            $password = $config['db_password'];
        }
    }
    
    // ✅ CONEXIÓN SEGURA
    $conectar = mysqli_connect($servername, $username, $password, $db);
    
    if (!$conectar) {
        $error = mysqli_connect_error();
        error_log("Error de conexión MySQL: " . $error);
        
        // ✅ MENSAJES DE ERROR SEGUROS
        if (strpos($error, '1045') !== false) { // Access denied
            die("Error de autenticación MySQL: Verifica las credenciales en config/db_config.php");
        } elseif (strpos($error, '1049') !== false) { // Unknown database
            die("Error: La base de datos '$db' no existe. Ejecuta bs.sql primero.");
        } else {
            die("Error de conexión: Verifica que MySQL esté ejecutándose.");
        }
    } else {
        mysqli_set_charset($conectar, $charset);
        return $conectar;
    }
}

// Resto de funciones...
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