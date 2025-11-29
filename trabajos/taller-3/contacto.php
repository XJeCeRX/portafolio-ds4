<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $nombre = trim($_POST['nombre'] ?? '');
  $correo = trim($_POST['correo'] ?? '');
  $mensaje = trim($_POST['mensaje'] ?? '');
  
  if (empty($nombre) || empty($correo) || empty($mensaje)) {
    header('Location: index.php?error=campos_vacios');
    exit;
  }
  
  // Sanitizar entrada
  $conn = getDB();
  $stmt = $conn->prepare("INSERT INTO contactos (nombre, correo, mensaje) VALUES (?, ?, ?)");
  
  if ($stmt) {
    $stmt->bind_param("sss", $nombre, $correo, $mensaje);
    
    if ($stmt->execute()) {
      $stmt->close();
      header('Location: index.php?ok=enviado');
    } else {
      $stmt->close();
      header('Location: index.php?error=bd_error');
    }
  } else {
    header('Location: index.php?error=preparacion');
  }
  
  exit;
} else {
  header('Location: index.php');
  exit;
}
?>


