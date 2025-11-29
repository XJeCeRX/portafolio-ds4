<?php
/**
 * Clase para conexión a MongoDB
 * Sistema de Mantenimiento NIBARRA
 */

class MongoDBConnection {
    private static $connection = null;
    private static $database = null;
    
    /**
     * Obtener conexión a MongoDB
     */
    public static function getConnection() {
        if (!MONGO_ENABLED) {
            return null;
        }
        
        if (self::$connection === null) {
            try {
                // Verificar si la extensión de MongoDB está instalada
                if (!class_exists('MongoDB\Client')) {
                    error_log("MongoDB PHP Driver no está instalado. Instalar con: composer require mongodb/mongodb");
                    return null;
                }
                
                // Construir URI de conexión
                $uri = MONGO_URI;
                if (!empty(MONGO_USER) && !empty(MONGO_PASS)) {
                    $uri = "mongodb://" . MONGO_USER . ":" . MONGO_PASS . "@" . MONGO_HOST . ":" . MONGO_PORT;
                } else {
                    $uri = "mongodb://" . MONGO_HOST . ":" . MONGO_PORT;
                }
                
                // Crear cliente MongoDB
                $client = new MongoDB\Client($uri);
                
                // Seleccionar base de datos
                self::$database = $client->selectDatabase(MONGO_DB);
                self::$connection = $client;
                
                // Verificar conexión
                $admin = $client->selectDatabase('admin');
                $admin->command(['ping' => 1]);
                
                return self::$connection;
            } catch (Exception $e) {
                error_log("Error de conexión MongoDB: " . $e->getMessage());
                return null;
            }
        }
        
        return self::$connection;
    }
    
    /**
     * Obtener base de datos
     */
    public static function getDatabase() {
        if (self::$database === null) {
            self::getConnection();
        }
        return self::$database;
    }
    
    /**
     * Replicar documento a MongoDB
     */
    public static function replicar($coleccion, $documento, $operacion = 'INSERT') {
        if (!MONGO_ENABLED) {
            return false;
        }
        
        try {
            $db = self::getDatabase();
            if (!$db) {
                return false;
            }
            
            $collection = $db->selectCollection($coleccion);
            
            switch ($operacion) {
                case 'INSERT':
                case 'UPDATE':
                    // Convertir tipos de datos para MongoDB
                    $doc = self::convertirParaMongoDB($documento);
                    
                    // Usar upsert para insertar o actualizar
                    $filter = ['_id' => $doc['_id']];
                    $options = ['upsert' => true];
                    $result = $collection->replaceOne($filter, $doc, $options);
                    return $result->getUpsertedCount() > 0 || $result->getModifiedCount() > 0;
                    
                case 'DELETE':
                    $filter = ['_id' => intval($documento['id'] ?? $documento['_id'] ?? 0)];
                    $result = $collection->deleteOne($filter);
                    return $result->getDeletedCount() > 0;
                    
                default:
                    return false;
            }
        } catch (Exception $e) {
            error_log("Error al replicar a MongoDB: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Convertir datos de MySQL a formato MongoDB
     */
    private static function convertirParaMongoDB($data) {
        $doc = [];
        
        // Mapear campos de MySQL a MongoDB
        foreach ($data as $key => $value) {
            // Convertir nombres de campos (snake_case a camelCase opcional)
            $mongoKey = $key;
            
            // Convertir tipos de datos
            if (is_numeric($value) && strpos($key, 'id') !== false) {
                $doc[$mongoKey] = intval($value);
            } elseif (is_numeric($value) && (strpos($key, 'costo') !== false || strpos($key, 'precio') !== false)) {
                $doc[$mongoKey] = floatval($value);
            } elseif ($value === null) {
                $doc[$mongoKey] = null;
            } else {
                $doc[$mongoKey] = $value;
            }
        }
        
        // Agregar _id basado en id_equipo o id_mantenimiento
        if (isset($data['id_equipo'])) {
            $doc['_id'] = intval($data['id_equipo']);
        } elseif (isset($data['id_mantenimiento'])) {
            $doc['_id'] = intval($data['id_mantenimiento']);
        } elseif (isset($data['id_trabajo'])) {
            $doc['_id'] = intval($data['id_trabajo']);
        }
        
        // Agregar timestamp de sincronización
        $doc['fecha_sincronizacion_mongo'] = new MongoDB\BSON\UTCDateTime();
        
        return $doc;
    }
    
    /**
     * Obtener todos los documentos de una colección
     */
    public static function obtenerTodos($coleccion, $filtros = []) {
        if (!MONGO_ENABLED) {
            return [];
        }
        
        try {
            $db = self::getDatabase();
            if (!$db) {
                return [];
            }
            
            $collection = $db->selectCollection($coleccion);
            $cursor = $collection->find($filtros);
            
            $resultados = [];
            foreach ($cursor as $document) {
                $resultados[] = $document;
            }
            
            return $resultados;
        } catch (Exception $e) {
            error_log("Error al obtener documentos de MongoDB: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Cerrar conexión
     */
    public static function closeConnection() {
        self::$connection = null;
        self::$database = null;
    }
}



