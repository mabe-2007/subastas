<?php
// include/config_env.php - Cargar variables de entorno

function loadEnvironmentVariables($filePath = "../env") {
    if (!file_exists($filePath)) {
        error_log("Archivo env no encontrado: " . $filePath);
        return false;
    }
    
    $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        // Saltar comentarios
        if (strpos(ltrim($line), '#') === 0) {
            continue;
        }
        
        // Separar clave-valor
        if (strpos($line, '=') !== false) {
            list($key, $value) = explode('=', $line, 2);
            $key = trim($key);
            $value = trim($value);
            
            // Establecer en $_ENV y putenv
            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
    return true;
}

// ✅ USAR CONDICIONAL PARA EVITAR REDECLARACIÓN
if (!function_exists('getEnvVar')) {
    function getEnvVar($key, $default = '') {
        if (isset($_ENV[$key])) {
            return $_ENV[$key];
        }
        $value = getenv($key);
        return $value !== false ? $value : $default;
    }
}

// Cargar variables al incluir este archivo
loadEnvironmentVariables();
?>