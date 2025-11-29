<?php
require_once '../config/config.php';
requireAuth();

header('Content-Type: application/json');

$mantenimientoClass = new Mantenimiento();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            $calendario = $_GET['calendario'] ?? false;
            
            if ($id) {
                // Validar ID
                if (!Validator::validarId($id)) {
                    echo json_encode(['success' => false, 'message' => 'ID de mantenimiento no válido']);
                    break;
                }
                
                $mantenimientos = $mantenimientoClass->obtenerTodos(['id_mantenimiento' => intval($id)]);
                if (!empty($mantenimientos)) {
                    echo json_encode(['success' => true, 'mantenimiento' => $mantenimientos[0]]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Mantenimiento no encontrado']);
                }
            } elseif ($calendario) {
                $mes = !empty($_GET['mes']) ? intval($_GET['mes']) : date('m');
                $anio = !empty($_GET['anio']) ? intval($_GET['anio']) : date('Y');
                
                // Validar mes y año
                if ($mes < 1 || $mes > 12) {
                    echo json_encode(['success' => false, 'message' => 'Mes inválido']);
                    break;
                }
                if ($anio < 2020 || $anio > 2100) {
                    echo json_encode(['success' => false, 'message' => 'Año inválido']);
                    break;
                }
                
                $mantenimientos = $mantenimientoClass->obtenerParaCalendario($mes, $anio);
                echo json_encode(['success' => true, 'mantenimientos' => $mantenimientos]);
            } else {
                // Sanitizar filtros
                $filtros = [
                    'estado' => !empty($_GET['estado']) ? Validator::sanitizeString($_GET['estado'], 50) : '',
                    'tipo_mantenimiento' => !empty($_GET['tipo_mantenimiento']) ? Validator::sanitizeString($_GET['tipo_mantenimiento'], 50) : '',
                    'id_equipo' => !empty($_GET['id_equipo']) && Validator::validarId($_GET['id_equipo']) ? intval($_GET['id_equipo']) : ''
                ];
                $mantenimientos = $mantenimientoClass->obtenerTodos($filtros);
                echo json_encode(['success' => true, 'mantenimientos' => $mantenimientos]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null) {
                echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
                break;
            }
            $result = $mantenimientoClass->crear($data);
            echo json_encode($result);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null) {
                echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
                break;
            }
            $id = $data['id_mantenimiento'] ?? null;
            if ($id && Validator::validarId($id)) {
                unset($data['id_mantenimiento']);
                $result = $mantenimientoClass->actualizar($id, $data);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID de mantenimiento requerido y válido']);
            }
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            $id = $data['id_mantenimiento'] ?? null;
            if ($id) {
                // Implementar eliminación si es necesario
                echo json_encode(['success' => false, 'message' => 'Eliminación no implementada']);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID de mantenimiento requerido']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    error_log("Error en API mantenimientos: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
}

