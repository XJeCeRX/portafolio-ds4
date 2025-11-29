<?php
/**
 * Configuración de Base de Datos
 * Sistema de Mantenimiento NIBARRA
 */

// Prevenir acceso directo
if (!defined('NIBARRA_ACCESS')) {
    die('Acceso denegado');
}

// Configuración de base de datos local
define('DB_LOCAL_HOST', 'localhost');
define('DB_LOCAL_NAME', 'nibarra_local');
define('DB_LOCAL_USER', 'root');
define('DB_LOCAL_PASS', '');
define('DB_LOCAL_CHARSET', 'utf8mb4');

// Configuración de MongoDB (Base de Datos Remota - Replicación)
define('MONGO_ENABLED', true); // Cambiar a false para deshabilitar MongoDB
define('MONGO_HOST', 'localhost');
define('MONGO_PORT', 27017);
define('MONGO_DB', 'nibarra_remoto'); // Base de datos remota en MongoDB
define('MONGO_USER', ''); // Dejar vacío si no requiere autenticación
define('MONGO_PASS', ''); // Dejar vacío si no requiere autenticación
define('MONGO_URI', 'mongodb://localhost:27017'); // URI completa si es necesario

// Clase para conexión a base de datos
class Database {
    private static $localConnection = null;
    
    /**
     * Obtener conexión a base de datos local (MySQL)
     */
    public static function getLocalConnection() {
        if (self::$localConnection === null) {
            try {
                $dsn = "mysql:host=" . DB_LOCAL_HOST . ";dbname=" . DB_LOCAL_NAME . ";charset=" . DB_LOCAL_CHARSET;
                $options = [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_LOCAL_CHARSET
                ];
                
                self::$localConnection = new PDO($dsn, DB_LOCAL_USER, DB_LOCAL_PASS, $options);
            } catch (PDOException $e) {
                error_log("Error de conexión local: " . $e->getMessage());
                throw new Exception("Error al conectar con la base de datos local");
            }
        }
        return self::$localConnection;
    }
    
    /**
     * Cerrar conexiones
     */
    public static function closeConnections() {
        self::$localConnection = null;
    }
}

