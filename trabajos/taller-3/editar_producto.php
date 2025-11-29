<?php
require_once 'config/database.php';

// Validar que se reciba un ID
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: inventario.php?error=no_id');
    exit;
}

$id_producto = intval($_GET['id']);
$conn = getDB();

// Obtener datos del producto a editar
$stmt = $conn->prepare("SELECT * FROM inventario WHERE id_producto = ?");
$stmt->bind_param("i", $id_producto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Location: inventario.php?error=no_encontrado');
    exit;
}
$producto = $result->fetch_assoc();
$stmt->close();

// Obtener proveedores para el formulario
$proveedores = [];
$result_prov = $conn->query("SELECT * FROM proveedores ORDER BY nombre");
if ($result_prov) {
    while ($row = $result_prov->fetch_assoc()) {
        $proveedores[] = $row;
    }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Editar Producto — Chinos Café</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --coffee1: #3e2723; --coffee2: #5d4037; --coffee3: #795548; --cream: #f7f3ee; --gold: #d7b98c; }
    body { font-family: 'Poppins', sans-serif; background: linear-gradient(180deg, var(--coffee1), var(--coffee2)); color: var(--cream); }
    .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); }
    input, textarea, select { color: var(--cream) !important; }
    input::placeholder, textarea::placeholder { color: var(--cream) !important; opacity: 0.5; }
  </style>
</head>
<body>
  <header class="fixed top-0 left-0 w-full z-50 glass">
    <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-[var(--gold)] rounded-lg flex items-center justify-center text-[var(--coffee1)] text-xl shadow-lg">
          <i class="fa-solid fa-mug-saucer"></i>
        </div>
        <span class="font-semibold text-[var(--gold)] text-lg">Chinos Café — Editar Inventario</span>
      </a>
      <a href="inventario.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-arrow-left"></i> Volver a Inventario</a>
    </nav>
  </header>

  <main class="pt-24 max-w-2xl mx-auto px-6 pb-8">
    <div class="p-6 rounded-xl glass">
      <h2 class="text-xl font-bold text-[var(--gold)] mb-4 flex items-center gap-2">
        <i class="fas fa-edit"></i> Editando: <?= htmlspecialchars($producto['nombre']) ?>
      </h2>
      
      <form action="procesar_inventario.php" method="POST" class="space-y-3">
        <input type="hidden" name="accion" value="editar">
        <input type="hidden" name="id_producto" value="<?= $producto['id_producto'] ?>">
        
        <div>
          <label class="block text-xs mb-1">Nombre</label>
          <input name="nombre" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm" value="<?= htmlspecialchars($producto['nombre']) ?>">
        </div>

        <div>
          <label class="block text-xs mb-1">Descripción</label>
          <textarea name="descripcion" rows="2" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm"><?= htmlspecialchars($producto['descripcion']) ?></textarea>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs mb-1">P. Compra</label>
            <input type="number" step="0.01" name="precio_compra" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm" value="<?= $producto['precio_compra'] ?>">
          </div>
          <div>
            <label class="block text-xs mb-1">P. Venta</label>
            <input type="number" step="0.01" name="precio_venta" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm" value="<?= $producto['precio_venta'] ?>">
          </div>
        </div>

        <div class="grid grid-cols-2 gap-2">
          <div>
            <label class="block text-xs mb-1">Stock</label>
            <input type="number" name="stock" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm" value="<?= $producto['stock'] ?>">
          </div>
          <div>
            <label class="block text-xs mb-1">Unidad</label>
            <input name="unidad" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm" value="<?= htmlspecialchars($producto['unidad']) ?>">
          </div>
        </div>

        <div>
          <label class="block text-xs mb-1">Categoría</label>
          <input name="categoria" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm" value="<?= htmlspecialchars($producto['categoria']) ?>">
        </div>

        <div>
          <label class="block text-xs mb-1">Proveedor</label>
          <select name="id_proveedor" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-sm">
            <option value="">Ninguno</option>
            <?php foreach ($proveedores as $p): ?>
            <option value="<?= $p['id_proveedor'] ?>" <?= ($p['id_proveedor'] == $producto['id_proveedor']) ? 'selected' : '' ?>>
                <?= htmlspecialchars($p['nombre']) ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>

        <button type="submit" class="w-full px-3 py-2 rounded-full bg-[var(--gold)] text-[var(--coffee1)] font-semibold hover:bg-amber-400 transition text-sm">
          <i class="fas fa-save"></i> Guardar Cambios
        </button>
      </form>
    </div>
  </main>
</body>
</html>
