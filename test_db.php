<?php
/**
 * Script para probar conexión a la base de datos
 * Lee las credenciales desde el archivo .env
 */

// Función para cargar variables desde .env
function loadEnv($path) {
    if (!file_exists($path)) {
        return false;
    }
    
    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    
    foreach ($lines as $line) {
        // Ignorar comentarios
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        
        // Separar clave y valor
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Establecer variable de entorno
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
    
    return true;
}

// Cargar archivo .env
$envPath = __DIR__ . '/.env';
if (!loadEnv($envPath)) {
    die("ERROR: No se encontró el archivo .env en: $envPath\n");
}

// Obtener credenciales
$dbHost = getenv('DB_HOST');
$dbName = getenv('DB_NAME');
$dbUser = getenv('DB_USER');
$dbPass = getenv('DB_PASS');

// Validar que existan las credenciales
if (empty($dbHost) || empty($dbName) || empty($dbUser)) {
    die("ERROR: Credenciales incompletas en el archivo .env\n");
}

echo "=== Probando conexión a la base de datos ===\n";
echo "Host: $dbHost\n";
echo "Base de datos: $dbName\n";
echo "Usuario: $dbUser\n";
echo "Contraseña: " . (empty($dbPass) ? "(vacía)" : "***") . "\n\n";

// Intentar conexión con MySQLi
echo "Método 1: MySQLi\n";
echo str_repeat("-", 40) . "\n";

$mysqli = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($mysqli->connect_error) {
    echo "❌ ERROR de conexión:\n";
    echo "   Código: " . $mysqli->connect_errno . "\n";
    echo "   Mensaje: " . $mysqli->connect_error . "\n";
} else {
    echo "✅ ¡Conexión exitosa con MySQLi!\n";
    
    // Obtener información del servidor
    echo "   Versión del servidor: " . $mysqli->server_info . "\n";
    echo "   Host info: " . $mysqli->host_info . "\n";
    
    // Probar consulta simple
    $result = $mysqli->query("SELECT 1 as test");
    if ($result) {
        echo "   Consulta de prueba: OK\n";
    }
    
    $mysqli->close();
}

echo "\n";

// Intentar conexión con PDO
echo "Método 2: PDO\n";
echo str_repeat("-", 40) . "\n";

try {
    $dsn = "mysql:host=$dbHost;dbname=$dbName;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];
    
    $pdo = new PDO($dsn, $dbUser, $dbPass, $options);
    echo "✅ ¡Conexión exitosa con PDO!\n";
    
    // Obtener información
    $version = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
    echo "   Versión del servidor: $version\n";
    
    // Probar consulta
    $stmt = $pdo->query("SELECT 1 as test");
    if ($stmt) {
        echo "   Consulta de prueba: OK\n";
    }
    
    $pdo = null;
    
} catch (PDOException $e) {
    echo "❌ ERROR de conexión:\n";
    echo "   Mensaje: " . $e->getMessage() . "\n";
}

echo "\n=== Prueba finalizada ===\n";
?>