<?php
/**
 * Clase para gestión de Trabajos Realizados
 */
class TrabajoRealizado {
    private $db;
    
    public function __construct() {
        $this->db = Database::getLocalConnection();
    }
    
    /**
     * Crear trabajo realizado
     */
    public function crear($data) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO trabajos_realizados (
                    id_equipo, id_mantenimiento, descripcion_trabajo,
                    fecha_trabajo, horas_trabajadas, costo_total,
                    id_tecnico, estado_final, calificacion
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
            ");
            
            $result = $stmt->execute([
                $data['id_equipo'],
                $data['id_mantenimiento'] ?? null,
                $data['descripcion_trabajo'],
                $data['fecha_trabajo'],
                $data['horas_trabajadas'] ?? 0,
                $data['costo_total'] ?? 0,
                $data['id_tecnico'] ?? $_SESSION['usuario_id'],
                $data['estado_final'] ?? 'completado',
                $data['calificacion'] ?? null
            ]);
            
            if ($result) {
                return ['success' => true, 'id' => $this->db->lastInsertId()];
            }
            
            return ['success' => false, 'message' => 'Error al crear el trabajo'];
        } catch (PDOException $e) {
            error_log("Error al crear trabajo: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar la solicitud'];
        }
    }
    
    /**
     * Obtener todos los trabajos realizados
     */
    public function obtenerTodos($filtros = []) {
        try {
            $sql = "
                SELECT tr.*, e.equipo, e.marca, e.serie, u.nombre_completo as tecnico_nombre
                FROM trabajos_realizados tr
                INNER JOIN equipos e ON tr.id_equipo = e.id_equipo
                LEFT JOIN usuarios u ON tr.id_tecnico = u.id_usuario
                WHERE 1=1
            ";
            $params = [];
            
            if (!empty($filtros['fecha_desde'])) {
                $sql .= " AND tr.fecha_trabajo >= ?";
                $params[] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $sql .= " AND tr.fecha_trabajo <= ?";
                $params[] = $filtros['fecha_hasta'];
            }
            
            $sql .= " ORDER BY tr.fecha_trabajo DESC, tr.fecha_registro DESC";
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll();
        } catch (PDOException $e) {
            error_log("Error al obtener trabajos: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de trabajos
     */
    public function obtenerEstadisticas() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    COUNT(*) as total_trabajos,
                    SUM(horas_trabajadas) as total_horas,
                    SUM(costo_total) as total_costo,
                    AVG(calificacion) as promedio_calificacion
                FROM trabajos_realizados
                WHERE estado_final = 'completado'
            ");
            $stmt->execute();
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return null;
        }
    }
}



