<?php
/**
 * Clase para gestión de Equipos
 */
class Equipo {
    private $db;
    
    public function __construct() {
        $this->db = Database::getLocalConnection();
    }
    
    /**
     * Crear nuevo equipo
     */
    public function crear($data) {
        try {
            // Validar datos
            $validacion = Validator::validarEquipo($data, false);
            if (!$validacion['valido']) {
                return ['success' => false, 'message' => implode('. ', $validacion['errores'])];
            }
            
            // Verificar que no exista un equipo con la misma serie
            $stmtCheck = $this->db->prepare("SELECT id_equipo FROM equipos WHERE serie = ? LIMIT 1");
            $stmtCheck->execute([$data['serie']]);
            if ($stmtCheck->fetch()) {
                return ['success' => false, 'message' => 'Ya existe un equipo con este número de serie'];
            }
            
            // Sanitizar datos
            $data['equipo'] = Validator::sanitizeString($data['equipo'], 100);
            $data['marca'] = Validator::sanitizeString($data['marca'], 50);
            $data['serie'] = Validator::sanitizeString($data['serie'], 50);
            $data['observacion'] = !empty($data['observacion']) ? Validator::sanitizeString($data['observacion'], 1000) : '';
            
            // Generar número de ingreso
            $numeroIngreso = $this->generarNumeroIngreso();
            
            $stmt = $this->db->prepare("
                INSERT INTO equipos (
                    numero_ingreso, fecha_ingreso, equipo, marca, serie,
                    tipo_servicio, costo_inicial, observacion, id_usuario_registro
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $numeroIngreso,
                $data['fecha_ingreso'],
                $data['equipo'],
                $data['marca'],
                $data['serie'],
                $data['tipo_servicio'],
                floatval($data['costo_inicial'] ?? 0),
                $data['observacion'],
                $_SESSION['usuario_id']
            ]);
            
            if ($result) {
                $idEquipo = $this->db->lastInsertId();
                
                // Intentar replicar a servidor remoto
                $this->replicarEquipo($idEquipo, 'INSERT');
                
                return ['success' => true, 'id' => $idEquipo, 'numero_ingreso' => $numeroIngreso];
            }
            
            return ['success' => false, 'message' => 'Error al crear el equipo'];
        } catch (PDOException $e) {
            error_log("Error al crear equipo: " . $e->getMessage());
            // Verificar si es error de duplicado
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                return ['success' => false, 'message' => 'Ya existe un equipo con este número de serie'];
            }
            return ['success' => false, 'message' => 'Error al procesar la solicitud'];
        }
    }
    
    /**
     * Obtener todos los equipos
     */
    public function obtenerTodos($filtros = []) {
        try {
            $sql = "SELECT * FROM vista_equipos_completa WHERE 1=1";
            $params = [];
            
            if (!empty($filtros['estado'])) {
                $sql .= " AND estado = ?";
                $params[] = $filtros['estado'];
            }
            
            if (!empty($filtros['busqueda'])) {
                $sql .= " AND (equipo LIKE ? OR marca LIKE ? OR serie LIKE ? OR numero_ingreso LIKE ?)";
                $busqueda = '%' . $filtros['busqueda'] . '%';
                $params = array_merge($params, [$busqueda, $busqueda, $busqueda, $busqueda]);
            }
            
            $sql .= " ORDER BY fecha_ingreso DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener equipos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener equipo por ID
     */
    public function obtenerPorId($id) {
        try {
            // Validar ID
            if (!Validator::validarId($id)) {
                return null;
            }
            
            $stmt = $this->db->prepare("SELECT * FROM equipos WHERE id_equipo = ?");
            $stmt->execute([intval($id)]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener equipo: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Actualizar equipo
     */
    public function actualizar($id, $data) {
        try {
            // Validar ID
            if (!Validator::validarId($id)) {
                return ['success' => false, 'message' => 'ID de equipo no válido'];
            }
            
            // Verificar que el equipo exista
            $equipoExistente = $this->obtenerPorId($id);
            if (!$equipoExistente) {
                return ['success' => false, 'message' => 'Equipo no encontrado'];
            }
            
            // Validar datos
            $validacion = Validator::validarEquipo($data, true);
            if (!$validacion['valido']) {
                return ['success' => false, 'message' => implode('. ', $validacion['errores'])];
            }
            
            // Verificar que no exista otro equipo con la misma serie (excepto el actual)
            $stmtCheck = $this->db->prepare("SELECT id_equipo FROM equipos WHERE serie = ? AND id_equipo != ? LIMIT 1");
            $stmtCheck->execute([$data['serie'], $id]);
            if ($stmtCheck->fetch()) {
                return ['success' => false, 'message' => 'Ya existe otro equipo con este número de serie'];
            }
            
            // Sanitizar datos
            $data['equipo'] = Validator::sanitizeString($data['equipo'], 100);
            $data['marca'] = Validator::sanitizeString($data['marca'], 50);
            $data['serie'] = Validator::sanitizeString($data['serie'], 50);
            $data['observacion'] = !empty($data['observacion']) ? Validator::sanitizeString($data['observacion'], 1000) : '';
            
            $stmt = $this->db->prepare("
                UPDATE equipos SET
                    fecha_ingreso = ?,
                    equipo = ?,
                    marca = ?,
                    serie = ?,
                    tipo_servicio = ?,
                    fecha_salida = ?,
                    costo_inicial = ?,
                    costo_final = ?,
                    observacion = ?,
                    estado = ?,
                    sincronizado = 0
                WHERE id_equipo = ?
            ");
            
            $result = $stmt->execute([
                $data['fecha_ingreso'],
                $data['equipo'],
                $data['marca'],
                $data['serie'],
                $data['tipo_servicio'],
                !empty($data['fecha_salida']) ? $data['fecha_salida'] : null,
                floatval($data['costo_inicial'] ?? 0),
                floatval($data['costo_final'] ?? 0),
                $data['observacion'],
                $data['estado'] ?? 'ingresado',
                intval($id)
            ]);
            
            if ($result && $stmt->rowCount() > 0) {
                // Intentar replicar
                $this->replicarEquipo($id, 'UPDATE');
                return ['success' => true];
            }
            
            return ['success' => false, 'message' => 'No se realizaron cambios en el equipo'];
        } catch (PDOException $e) {
            error_log("Error al actualizar equipo: " . $e->getMessage());
            if (strpos($e->getMessage(), 'Duplicate') !== false) {
                return ['success' => false, 'message' => 'Ya existe otro equipo con este número de serie'];
            }
            return ['success' => false, 'message' => 'Error al procesar la solicitud'];
        }
    }
    
    /**
     * Eliminar equipo
     */
    public function eliminar($id) {
        try {
            // Validar ID
            if (!Validator::validarId($id)) {
                return ['success' => false, 'message' => 'ID de equipo no válido'];
            }
            
            // Verificar que el equipo exista
            $equipo = $this->obtenerPorId($id);
            if (!$equipo) {
                return ['success' => false, 'message' => 'Equipo no encontrado'];
            }
            
            // Verificar si tiene mantenimientos activos
            $stmtCheck = $this->db->prepare("
                SELECT COUNT(*) as total 
                FROM mantenimientos 
                WHERE id_equipo = ? AND estado != 'terminada'
            ");
            $stmtCheck->execute([$id]);
            $result = $stmtCheck->fetch();
            
            if ($result && $result['total'] > 0) {
                return [
                    'success' => false, 
                    'message' => 'No se puede eliminar el equipo porque tiene mantenimientos activos. Termine primero los mantenimientos pendientes.'
                ];
            }
            
            $stmt = $this->db->prepare("DELETE FROM equipos WHERE id_equipo = ?");
            $result = $stmt->execute([intval($id)]);
            
            if ($result && $stmt->rowCount() > 0) {
                // Registrar eliminación para replicación
                $this->replicarEquipo($id, 'DELETE');
                return ['success' => true];
            }
            
            return ['success' => false, 'message' => 'No se pudo eliminar el equipo'];
        } catch (PDOException $e) {
            error_log("Error al eliminar equipo: " . $e->getMessage());
            // Verificar si es error de foreign key
            if (strpos($e->getMessage(), 'foreign key') !== false) {
                return ['success' => false, 'message' => 'No se puede eliminar el equipo porque tiene registros relacionados'];
            }
            return ['success' => false, 'message' => 'Error al procesar la solicitud'];
        }
    }
    
    /**
     * Generar número de ingreso automático
     */
    private function generarNumeroIngreso() {
        $anio = date('Y');
        $stmt = $this->db->prepare("
            SELECT COUNT(*) + 1 as siguiente 
            FROM equipos 
            WHERE YEAR(fecha_ingreso) = ?
        ");
        $stmt->execute([$anio]);
        $result = $stmt->fetch();
        $numero = str_pad($result['siguiente'], 5, '0', STR_PAD_LEFT);
        return "ING-{$anio}-{$numero}";
    }
    
    /**
     * Replicar equipo a MongoDB (Base de Datos Remota)
     */
    private function replicarEquipo($idEquipo, $operacion) {
        $equipo = $this->obtenerPorId($idEquipo);
        if (!$equipo) return false;
        
        $replicado = false;
        
        // Replicar a MongoDB (Base de datos remota)
        if (MONGO_ENABLED) {
            try {
                $replicado = MongoDBConnection::replicar('equipos', $equipo, $operacion);
                if ($replicado) {
                    // Marcar como sincronizado
                    try {
                        $updateStmt = $this->db->prepare("
                            UPDATE equipos SET sincronizado = 1, fecha_sincronizacion = NOW() 
                            WHERE id_equipo = ?
                        ");
                        $updateStmt->execute([$idEquipo]);
                    } catch (PDOException $e) {
                        error_log("Error al actualizar estado de sincronización: " . $e->getMessage());
                    }
                } else {
                    error_log("Error al replicar equipo a MongoDB");
                    // Guardar en log para intentar más tarde
                    $this->guardarLogSincronizacion('equipos', $idEquipo, $operacion);
                }
            } catch (Exception $e) {
                error_log("Error en replicación MongoDB: " . $e->getMessage());
                // Guardar en log para intentar más tarde
                $this->guardarLogSincronizacion('equipos', $idEquipo, $operacion);
            }
        }
        
        return $replicado;
    }
    
    /**
     * Guardar log de sincronización
     */
    private function guardarLogSincronizacion($tabla, $idRegistro, $operacion) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO log_sincronizacion (tabla_origen, id_registro, operacion, estado)
                VALUES (?, ?, ?, 'pendiente')
            ");
            $stmt->execute([$tabla, $idRegistro, $operacion]);
        } catch (PDOException $e) {
            error_log("Error al guardar log: " . $e->getMessage());
        }
    }
}

