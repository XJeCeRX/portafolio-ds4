<?php
/**
 * Clase para gestión de Mantenimientos
 */
class Mantenimiento {
    private $db;
    
    public function __construct() {
        $this->db = Database::getLocalConnection();
    }
    
    /**
     * Crear nuevo mantenimiento
     */
    public function crear($data) {
        try {
            // Validar datos
            $validacion = Validator::validarMantenimiento($data, false);
            if (!$validacion['valido']) {
                return ['success' => false, 'message' => implode('. ', $validacion['errores'])];
            }
            
            // Calcular porcentaje de avance según estado
            $porcentaje = $this->calcularPorcentajePorEstado($data['estado'] ?? 'por_hacer');
            
            // Sanitizar datos
            $data['descripcion'] = !empty($data['descripcion']) ? Validator::sanitizeString($data['descripcion'], 500) : '';
            $data['observaciones'] = !empty($data['observaciones']) ? Validator::sanitizeString($data['observaciones'], 1000) : '';
            $data['material_requerido'] = !empty($data['material_requerido']) ? Validator::sanitizeString($data['material_requerido'], 500) : '';
            
            $stmt = $this->db->prepare("
                INSERT INTO mantenimientos (
                    id_equipo, tipo_mantenimiento, estado, porcentaje_avance,
                    fecha_inicio, fecha_fin_prevista, descripcion, observaciones,
                    material_requerido, id_tecnico_asignado, costo_mantenimiento
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                intval($data['id_equipo']),
                $data['tipo_mantenimiento'],
                $data['estado'] ?? 'por_hacer',
                $porcentaje,
                $data['fecha_inicio'] ?? date('Y-m-d'),
                !empty($data['fecha_fin_prevista']) ? $data['fecha_fin_prevista'] : null,
                $data['descripcion'],
                $data['observaciones'],
                $data['material_requerido'],
                intval($data['id_tecnico_asignado'] ?? $_SESSION['usuario_id']),
                floatval($data['costo_mantenimiento'] ?? 0)
            ]);
            
            if ($result) {
                $idMantenimiento = $this->db->lastInsertId();
                
                // Replicar
                $this->replicarMantenimiento($idMantenimiento, 'INSERT');
                
                return ['success' => true, 'id' => $idMantenimiento];
            }
            
            return ['success' => false, 'message' => 'Error al crear el mantenimiento'];
        } catch (PDOException $e) {
            error_log("Error al crear mantenimiento: " . $e->getMessage());
            if (strpos($e->getMessage(), 'foreign key') !== false) {
                return ['success' => false, 'message' => 'El equipo seleccionado no existe'];
            }
            return ['success' => false, 'message' => 'Error al procesar la solicitud'];
        }
    }
    
    /**
     * Obtener todos los mantenimientos
     */
    public function obtenerTodos($filtros = []) {
        try {
            $sql = "SELECT * FROM vista_mantenimientos_completa WHERE 1=1";
            $params = [];
            
            if (!empty($filtros['estado'])) {
                $sql .= " AND estado = ?";
                $params[] = $filtros['estado'];
            }
            
            if (!empty($filtros['tipo_mantenimiento'])) {
                $sql .= " AND tipo_mantenimiento = ?";
                $params[] = $filtros['tipo_mantenimiento'];
            }
            
            if (!empty($filtros['id_equipo'])) {
                $sql .= " AND id_equipo = ?";
                $params[] = $filtros['id_equipo'];
            }
            
            $sql .= " ORDER BY fecha_inicio DESC, fecha_registro DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener mantenimientos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener mantenimientos por estado
     */
    public function obtenerPorEstado($estado) {
        return $this->obtenerTodos(['estado' => $estado]);
    }
    
    /**
     * Obtener mantenimientos para calendario
     */
    public function obtenerParaCalendario($mes, $anio) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM vista_mantenimientos_completa
                WHERE MONTH(fecha_inicio) = ? AND YEAR(fecha_inicio) = ?
                OR MONTH(fecha_fin_prevista) = ? AND YEAR(fecha_fin_prevista) = ?
                ORDER BY fecha_inicio ASC
            ");
            $stmt->execute([$mes, $anio, $mes, $anio]);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener mantenimientos para calendario: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Actualizar mantenimiento
     */
    public function actualizar($id, $data) {
        try {
            // Calcular porcentaje si cambia el estado
            if (isset($data['estado'])) {
                $data['porcentaje_avance'] = $this->calcularPorcentajePorEstado($data['estado']);
            }
            
            // Si se marca como terminada, establecer fecha_fin_real
            if (isset($data['estado']) && $data['estado'] === 'terminada' && empty($data['fecha_fin_real'])) {
                $data['fecha_fin_real'] = date('Y-m-d');
            }
            
            $campos = [];
            $valores = [];
            
            $camposPermitidos = [
                'tipo_mantenimiento', 'estado', 'porcentaje_avance',
                'fecha_inicio', 'fecha_fin_prevista', 'fecha_fin_real',
                'descripcion', 'observaciones', 'material_requerido',
                'id_tecnico_asignado', 'costo_mantenimiento'
            ];
            
            foreach ($camposPermitidos as $campo) {
                if (isset($data[$campo])) {
                    $campos[] = "$campo = ?";
                    $valores[] = $data[$campo];
                }
            }
            
            $campos[] = "sincronizado = 0";
            $valores[] = $id;
            
            $sql = "UPDATE mantenimientos SET " . implode(', ', $campos) . " WHERE id_mantenimiento = ?";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute($valores);
            
            if ($result) {
                $this->replicarMantenimiento($id, 'UPDATE');
                return ['success' => true];
            }
            
            return ['success' => false, 'message' => 'Error al actualizar el mantenimiento'];
        } catch (PDOException $e) {
            error_log("Error al actualizar mantenimiento: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar la solicitud'];
        }
    }
    
    /**
     * Calcular porcentaje de avance según estado
     */
    private function calcularPorcentajePorEstado($estado) {
        $porcentajes = [
            'por_hacer' => 0,
            'en_espera_material' => 25,
            'en_revision' => 75,
            'terminada' => 100
        ];
        return $porcentajes[$estado] ?? 0;
    }
    
    /**
     * Replicar mantenimiento a MongoDB (Base de Datos Remota)
     */
    private function replicarMantenimiento($idMantenimiento, $operacion) {
        $stmt = $this->db->prepare("SELECT * FROM mantenimientos WHERE id_mantenimiento = ?");
        $stmt->execute([$idMantenimiento]);
        $mantenimiento = $stmt->fetch();
        
        if (!$mantenimiento) return false;
        
        $replicado = false;
        
        // Replicar a MongoDB (Base de datos remota)
        if (MONGO_ENABLED) {
            try {
                $replicado = MongoDBConnection::replicar('mantenimientos', $mantenimiento, $operacion);
                if ($replicado) {
                    // Marcar como sincronizado
                    try {
                        $updateStmt = $this->db->prepare("
                            UPDATE mantenimientos SET sincronizado = 1, fecha_sincronizacion = NOW() 
                            WHERE id_mantenimiento = ?
                        ");
                        $updateStmt->execute([$idMantenimiento]);
                    } catch (PDOException $e) {
                        error_log("Error al actualizar estado de sincronización: " . $e->getMessage());
                    }
                } else {
                    error_log("Error al replicar mantenimiento a MongoDB");
                }
            } catch (Exception $e) {
                error_log("Error en replicación MongoDB: " . $e->getMessage());
            }
        }
        
        return $replicado;
    }
}

