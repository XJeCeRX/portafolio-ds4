<?php
/**
 * Configuración General del Sistema
 * Sistema de Mantenimiento NIBARRA
 */

// Prevenir acceso directo
define('NIBARRA_ACCESS', true);

// Configuración de sesión
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Cambiar a 1 en producción con HTTPS
session_start();

// Zona horaria
date_default_timezone_set('America/Panama');

// Configuración de errores (cambiar en producción)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/error.log');

// Rutas del sistema
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('API_PATH', ROOT_PATH . '/api');
define('UPLOADS_PATH', ROOT_PATH . '/uploads');

// URL base (ajustar según configuración del servidor)
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$script = dirname($_SERVER['SCRIPT_NAME']);
define('BASE_URL', $protocol . '://' . $host . $script);

// Incluir configuración de base de datos
require_once CONFIG_PATH . '/database.php';

// Función de autoload para clases
spl_autoload_register(function ($class) {
    $file = INCLUDES_PATH . '/classes/' . $class . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

// Cargar clase MongoDBConnection si está disponible
$mongoClass = INCLUDES_PATH . '/classes/MongoDBConnection.php';
if (file_exists($mongoClass)) {
    require_once $mongoClass;
}

// Función helper para verificar autenticación
function isAuthenticated() {
    return isset($_SESSION['usuario_id']) && isset($_SESSION['usuario_nombre']);
}

// Función helper para requerir autenticación
function requireAuth() {
    if (!isAuthenticated()) {
        header('Location: ' . BASE_URL . '/login.php');
        exit;
    }
}

// Función helper para sanitizar entrada
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// Función helper para formatear fecha
function formatDate($date, $format = 'd/m/Y') {
    if (empty($date)) return '';
    $dateObj = new DateTime($date);
    return $dateObj->format($format);
}

// Función helper para formatear moneda
function formatCurrency($amount) {
    return '$' . number_format($amount, 2, '.', ',');
}

// Crear directorios necesarios si no existen
$directories = [INCLUDES_PATH . '/classes', API_PATH, UPLOADS_PATH, ROOT_PATH . '/logs'];
foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0755, true);
    }
}

