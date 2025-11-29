<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: proveedores.php');
  exit;
}

$conn = getDB();

try {
  $nombre = trim($_POST['nombre']);
  $contacto = trim($_POST['contacto']);
  $telefono = trim($_POST['telefono'] ?? '');
  $email = trim($_POST['email'] ?? '');
  $direccion = trim($_POST['direccion'] ?? '');

  $stmt = $conn->prepare("INSERT INTO proveedores (nombre, contacto, telefono, email, direccion) VALUES (?, ?, ?, ?, ?)");
  $stmt->bind_param("sssss", $nombre, $contacto, $telefono, $email, $direccion);
  
  if ($stmt->execute()) {
    $stmt->close();
    header('Location: proveedores.php?ok=1');
  } else {
    $stmt->close();
    header('Location: proveedores.php?error=1');
  }
} catch (Exception $e) {
  error_log("Error en proveedor: " . $e->getMessage());
  header('Location: proveedores.php?error=1');
}

exit;
?>


