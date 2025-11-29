<?php
require_once 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: inventario.php');
  exit;
}

$conn = getDB();
$accion = $_POST['accion'] ?? '';

try {
  if ($accion === 'agregar') {
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio_compra = floatval($_POST['precio_compra']);
    $precio_venta = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);
    $unidad = trim($_POST['unidad'] ?? 'unidad');
    $categoria = trim($_POST['categoria'] ?? '');
    $id_proveedor = !empty($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : null;

    $stmt = $conn->prepare("INSERT INTO inventario (nombre, descripcion, precio_compra, precio_venta, stock, unidad, categoria, id_proveedor) 
                            VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssddiss", $nombre, $descripcion, $precio_compra, $precio_venta, $stock, $unidad, $categoria, $id_proveedor);
    
    if ($stmt->execute()) {
      $stmt->close();
      header('Location: inventario.php?ok=1');
    } else {
      $stmt->close();
      header('Location: inventario.php?error=1');
    }
  } else if ($accion === 'editar') {
    $id_producto = intval($_POST['id_producto']);
    $nombre = trim($_POST['nombre']);
    $descripcion = trim($_POST['descripcion'] ?? '');
    $precio_compra = floatval($_POST['precio_compra']);
    $precio_venta = floatval($_POST['precio_venta']);
    $stock = intval($_POST['stock']);
    $unidad = trim($_POST['unidad'] ?? 'unidad');
    $categoria = trim($_POST['categoria'] ?? '');
    $id_proveedor = !empty($_POST['id_proveedor']) ? intval($_POST['id_proveedor']) : null;

    $stmt = $conn->prepare("UPDATE inventario SET nombre = ?, descripcion = ?, precio_compra = ?, precio_venta = ?, 
                            stock = ?, unidad = ?, categoria = ?, id_proveedor = ? WHERE id_producto = ?");
    $stmt->bind_param("ssddissii", $nombre, $descripcion, $precio_compra, $precio_venta, $stock, $unidad, $categoria, $id_proveedor, $id_producto);

    if ($stmt->execute()) {
      $stmt->close();
      header('Location: inventario.php?ok=1');
    } else {
      $stmt->close();
      header('Location: inventario.php?error=1');
    }
  } else if ($accion === 'eliminar') {
    $id_producto = intval($_POST['id_producto']);
    
    $stmt = $conn->prepare("DELETE FROM inventario WHERE id_producto = ?");
    $stmt->bind_param("i", $id_producto);
    
    if ($stmt->execute()) {
      $stmt->close();
      header('Location: inventario.php?ok=1');
    } else {
      $stmt->close();
      header('Location: inventario.php?error=1');
    }
  } else {
    header('Location: inventario.php?error=accion');
  }
} catch (Exception $e) {
  error_log("Error en inventario: " . $e->getMessage());
  header('Location: inventario.php?error=1');
}

exit;
?>


