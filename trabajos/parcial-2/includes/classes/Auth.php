<?php
/**
 * Clase de Autenticación
 */
class Auth {
    private $db;
    
    public function __construct() {
        $this->db = Database::getLocalConnection();
    }
    
    /**
     * Autenticar usuario
     */
    public function login($usuario, $contrasena) {
        try {
            $stmt = $this->db->prepare("
                SELECT id_usuario, usuario, contrasena, nombre_completo, rol, activo 
                FROM usuarios 
                WHERE usuario = ? AND activo = 1
            ");
            $stmt->execute([$usuario]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($contrasena, $user['contrasena'])) {
                // Actualizar último acceso
                $updateStmt = $this->db->prepare("
                    UPDATE usuarios 
                    SET ultimo_acceso = NOW() 
                    WHERE id_usuario = ?
                ");
                $updateStmt->execute([$user['id_usuario']]);
                
                // Establecer sesión
                $_SESSION['usuario_id'] = $user['id_usuario'];
                $_SESSION['usuario_nombre'] = $user['nombre_completo'];
                $_SESSION['usuario_rol'] = $user['rol'];
                $_SESSION['usuario_login'] = $user['usuario'];
                
                return ['success' => true, 'user' => $user];
            }
            
            return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
        } catch (PDOException $e) {
            error_log("Error en login: " . $e->getMessage());
            return ['success' => false, 'message' => 'Error al procesar la autenticación'];
        }
    }
    
    /**
     * Cerrar sesión
     */
    public function logout() {
        session_unset();
        session_destroy();
        return true;
    }
    
    /**
     * Verificar si el usuario está autenticado
     */
    public function isAuthenticated() {
        return isset($_SESSION['usuario_id']);
    }
    
    /**
     * Obtener información del usuario actual
     */
    public function getCurrentUser() {
        if (!$this->isAuthenticated()) {
            return null;
        }
        
        try {
            $stmt = $this->db->prepare("
                SELECT id_usuario, usuario, nombre_completo, rol, ultimo_acceso 
                FROM usuarios 
                WHERE id_usuario = ?
            ");
            $stmt->execute([$_SESSION['usuario_id']]);
            return $stmt->fetch();
        } catch (PDOException $e) {
            error_log("Error al obtener usuario: " . $e->getMessage());
            return null;
        }
    }
}



