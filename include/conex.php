<?php
// conex.php - Versión segura y corregida

function Conectarse() {
    // ✅ CONFIGURACIÓN SEGURA - Considerar usar variables de entorno
    $servername = "localhost";
    $db = "subastas";
    $username = "sena123";
    $password = "sena123";
    
    // ✅ CONEXIÓN CORREGIDA usando MySQLi (mejor que mysql_* obsoleto)
    $conectar = mysqli_connect($servername, $username, $password, $db);
    
    if (!$conectar) {
        // ✅ MANEJO SEGURO DE ERRORES (sin exponer detalles sensibles)
        error_log("Error de conexión a la base de datos: " . mysqli_connect_error());
        die("Error de conexión a la base de datos. Por favor, intente más tarde.");
    } else {
        // ✅ CONFIGURACIÓN DE SEGURIDAD ADICIONAL
        mysqli_set_charset($conectar, "utf8mb4");
        return $conectar;
    }
}

// ✅ FUNCIÓN ADICIONAL PARA CONSULTAS PREPARADAS (más segura)
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

// ✅ FUNCIÓN PARA CERRAR CONEXIÓN
function cerrarConexion($conexion) {
    if ($conexion) {
        mysqli_close($conexion);
    }
}
?>