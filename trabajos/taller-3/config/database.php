<?php
// Configuración de la base de datos Chinos Café
define('DB_HOST', '127.0.0.1');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'chinos_cafe1');

// Establecer conexión
function getDB() {
  static $conn = null;
  
  if ($conn === null) {
    try {
      $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
      
      if ($conn->connect_error) {
        die("❌ Error de conexión: " . $conn->connect_error);
      }
      
      $conn->set_charset("utf8mb4");
    } catch (Exception $e) {
      die("❌ Error al conectar con la base de datos: " . $e->getMessage());
    }
  }
  
  return $conn;
}

// Función para ejecutar consultas preparadas
function ejecutarQuery($sql, $params = []) {
  $conn = getDB();
  $stmt = $conn->prepare($sql);
  
  if (!$stmt) {
    error_log("Error en la preparación: " . $conn->error);
    return false;
  }
  
  if (!empty($params)) {
    $types = str_repeat('s', count($params));
    $stmt->bind_param($types, ...$params);
  }
  
  if ($stmt->execute()) {
    $result = $stmt->get_result();
    $stmt->close();
    return $result;
  } else {
    error_log("Error en la ejecución: " . $stmt->error);
    $stmt->close();
    return false;
  }
}

// Cerrar conexión
function closeDB() {
  $conn = getDB();
  if ($conn) {
    $conn->close();
  }
}
?>


