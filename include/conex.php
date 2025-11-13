<?php
// conex.php - Versión segura y corregida

function Conectarse() {
    $servername = "localhost";
    $db = "subastas";
    $username = "sena123";
    $password = "sena123";
    
    // ✅ CONEXIÓN CORREGIDA
    $conEctar = mysqli_connect($servername, $username, $password, $db);
    
    if (!$conEctar) {
        // ✅ MANEJO SEGURO DE ERRORES
        error_log("Error de conexión a la base de datos: " . mysqli_connect_error());
        die("Error de conexión a la base de datos. Por favor, intente más tarde.");
    } else {
        // ✅ CONFIGURACIÓN DE SEGURIDAD
        mysqli_set_charset($conEctar, "utf8mb4");
        return $conEctar;
    }
}

// ✅ FUNCIÓN ADICIONAL PARA CONSULTAS PREPARADAS
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
?>