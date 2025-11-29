<?php
require_once 'config/database.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ventas.php');
  exit;
}

$conn = getDB();
$conn->begin_transaction();

try {
  $cliente = trim($_POST['cliente'] ?? 'Cliente General');
  $tipo_pago = $_POST['tipo_pago'] ?? 'efectivo';
  $numero_factura = 'FAC-' . date('YmdHis') . '-' . rand(100, 999);
  $total = 0;
  
  // Calcular total
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'producto_') === 0) {
      $id = str_replace('producto_', '', $key);
      $cantidad = intval($_POST['cantidad_' . $id] ?? 1);
      $precio = floatval($_POST['precio_' . $id] ?? 0);
      $total += $precio;
    }
  }

  // Insertar venta
  $stmt = $conn->prepare("INSERT INTO ventas (numero_factura, cliente, total, tipo_pago) VALUES (?, ?, ?, ?)");
  $stmt->bind_param("ssds", $numero_factura, $cliente, $total, $tipo_pago);
  $stmt->execute();
  $id_venta = $stmt->insert_id;
  $stmt->close();

  // Insertar detalles
  foreach ($_POST as $key => $value) {
    if (strpos($key, 'producto_') === 0) {
      $id = str_replace('producto_', '', $key);
      $id_producto = intval($value);
      $cantidad = intval($_POST['cantidad_' . $id] ?? 1);
      $precio_unitario = floatval($_POST['precio_' . $id] ?? 0);
      $subtotal = $cantidad * $precio_unitario;

      $stmt = $conn->prepare("INSERT INTO detalle_venta (id_venta, id_producto, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)");
      $stmt->bind_param("iiidd", $id_venta, $id_producto, $cantidad, $precio_unitario, $subtotal);
      $stmt->execute();
      $stmt->close();

      // Actualizar stock
      $stmt = $conn->prepare("UPDATE inventario SET stock = stock - ? WHERE id_producto = ?");
      $stmt->bind_param("ii", $cantidad, $id_producto);
      $stmt->execute();
      $stmt->close();
    }
  }

  $conn->commit();
  header('Location: ventas.php?ok=1');
} catch (Exception $e) {
  $conn->rollback();
  error_log("Error en venta: " . $e->getMessage());
  header('Location: ventas.php?error=1');
}

exit;
?>


