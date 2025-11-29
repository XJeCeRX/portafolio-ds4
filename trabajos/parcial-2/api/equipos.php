<?php
require_once '../config/config.php';
requireAuth();

header('Content-Type: application/json');

$equipoClass = new Equipo();
$method = $_SERVER['REQUEST_METHOD'];

try {
    switch ($method) {
        case 'GET':
            $id = $_GET['id'] ?? null;
            if ($id) {
                // Validar ID
                if (!Validator::validarId($id)) {
                    echo json_encode(['success' => false, 'message' => 'ID de equipo no válido']);
                    break;
                }
                
                $equipo = $equipoClass->obtenerPorId($id);
                if ($equipo) {
                    echo json_encode(['success' => true, 'equipo' => $equipo]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Equipo no encontrado']);
                }
            } else {
                // Sanitizar filtros
                $filtros = [
                    'estado' => !empty($_GET['estado']) ? Validator::sanitizeString($_GET['estado'], 50) : '',
                    'busqueda' => !empty($_GET['busqueda']) ? Validator::sanitizeString($_GET['busqueda'], 100) : ''
                ];
                $equipos = $equipoClass->obtenerTodos($filtros);
                echo json_encode(['success' => true, 'equipos' => $equipos]);
            }
            break;
            
        case 'POST':
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null) {
                echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
                break;
            }
            $result = $equipoClass->crear($data);
            echo json_encode($result);
            break;
            
        case 'PUT':
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null) {
                echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
                break;
            }
            $id = $data['id_equipo'] ?? null;
            if ($id && Validator::validarId($id)) {
                unset($data['id_equipo']);
                $result = $equipoClass->actualizar($id, $data);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID de equipo requerido y válido']);
            }
            break;
            
        case 'DELETE':
            $data = json_decode(file_get_contents('php://input'), true);
            if ($data === null) {
                echo json_encode(['success' => false, 'message' => 'Datos JSON inválidos']);
                break;
            }
            $id = $data['id_equipo'] ?? null;
            if ($id && Validator::validarId($id)) {
                $result = $equipoClass->eliminar($id);
                echo json_encode($result);
            } else {
                echo json_encode(['success' => false, 'message' => 'ID de equipo requerido y válido']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    }
} catch (Exception $e) {
    error_log("Error en API equipos: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud']);
}

