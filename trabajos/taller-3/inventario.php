<?php
require_once 'config/database.php';

$mensaje = '';
if (isset($_GET['ok'])) $mensaje = '✅ Operación realizada correctamente';
if (isset($_GET['error'])) $mensaje = '⚠️ Error en la operación';

// Obtener productos
$conn = getDB();
$productos = [];
$result = $conn->query("SELECT i.*, p.nombre as nombre_proveedor FROM inventario i 
  LEFT JOIN proveedores p ON i.id_proveedor = p.id_proveedor ORDER BY i.nombre");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
  }
}

// Obtener proveedores para el formulario
$proveedores = [];
$result = $conn->query("SELECT * FROM proveedores");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $proveedores[] = $row;
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Inventario — Chinos Café</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --coffee1: #3e2723; --coffee2: #5d4037; --coffee3: #795548; --cream: #f7f3ee; --gold: #d7b98c; }
    body { font-family: 'Poppins', sans-serif; background: linear-gradient(180deg, var(--coffee1), var(--coffee2)); color: var(--cream); }
    .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); }
    /* Estilo para que el texto del input sea siempre claro */
    input, textarea, select {
        color: var(--coffee1) !important;
        background-color: var(--cream) !important;
    }
    input::placeholder, textarea::placeholder {
        color: var(--coffee1) !important;
        opacity: 0.5;
    }
  </style>
</head>
<body>
  <header class="fixed top-0 left-0 w-full z-50 glass">
    <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-[var(--gold)] rounded-lg flex items-center justify-center text-[var(--coffee1)] text-xl shadow-lg">
          <i class="fa-solid fa-mug-saucer"></i>
        </div>
        <span class="font-semibold text-[var(--gold)] text-lg">Chinos Café — Inventario</span>
      </a>
      <div class="flex items-center gap-4 text-[var(--cream)]/90">
        <a href="index.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-home"></i> Inicio</a>
        <a href="ventas.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-shopping-cart"></i> Ventas</a>
        <a href="proveedores.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-truck-field"></i> Proveedores</a>
      </div>
    </nav>
  </header>

  <main class="pt-24 max-w-7xl mx-auto px-6 pb-8">
    <?php if ($mensaje): ?>
      <div class="mb-4 p-3 rounded bg-[var(--gold)]/10 border border-[var(--gold)]/20 text-[var(--gold)] text-sm text-center">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-4 gap-6">
      <!-- Formulario -->
      <div class="lg:col-span-1">
        <div class="p-6 rounded-xl glass sticky top-24">
          <h2 class="text-xl font-bold text-[var(--gold)] mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Agregar Producto
          </h2>
          
          <form action="procesar_inventario.php" method="POST" class="space-y-3">
            <input type="hidden" name="accion" value="agregar">
            
            <div>
              <label class="block text-xs mb-1">Nombre</label>
              <input name="nombre" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
            </div>

            <div>
              <label class="block text-xs mb-1">Descripción</label>
              <textarea name="descripcion" rows="2" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm"></textarea>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-xs mb-1">P. Compra</label>
                <input type="number" step="0.01" name="precio_compra" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
              </div>
              <div>
                <label class="block text-xs mb-1">P. Venta</label>
                <input type="number" step="0.01" name="precio_venta" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
              </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
              <div>
                <label class="block text-xs mb-1">Stock</label>
                <input type="number" name="stock" required value="0" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
              </div>
              <div>
                <label class="block text-xs mb-1">Unidad</label>
                <input name="unidad" value="unidad" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
              </div>
            </div>

            <div>
              <label class="block text-xs mb-1">Categoría</label>
              <input name="categoria" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
            </div>

            <div>
              <label class="block text-xs mb-1">Proveedor</label>
              <select name="id_proveedor" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
                <option value="">Ninguno</option>
                <?php foreach ($proveedores as $p): ?>
                <option value="<?= $p['id_proveedor'] ?>"><?= htmlspecialchars($p['nombre']) ?></option>
                <?php endforeach; ?>
              </select>
            </div>

            <button type="submit" class="w-full px-3 py-2 rounded-full bg-[var(--gold)] text-[var(--coffee1)] font-semibold hover:bg-amber-400 transition text-sm">
              <i class="fas fa-save"></i> Guardar
            </button>
          </form>
        </div>
      </div>

      <!-- Lista de Productos -->
      <div class="lg:col-span-3">
        <div class="p-6 rounded-xl glass">
          <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-[var(--gold)] flex items-center gap-2">
              <i class="fas fa-boxes-stacked"></i> Inventario (<?= count($productos) ?> productos)
            </h2>
          </div>

          <div class="overflow-x-auto">
            <table class="w-full text-sm">
              <thead>
                <tr class="border-b border-[var(--gold)]/20">
                  <th class="text-left p-2 text-[var(--gold)]">Producto</th>
                  <th class="text-left p-2 text-[var(--gold)]">Precios</th>
                  <th class="text-center p-2 text-[var(--gold)]">Stock</th>
                  <th class="text-left p-2 text-[var(--gold)]">Proveedor</th>
                  <th class="text-center p-2 text-[var(--gold)]">Acciones</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($productos as $p): ?>
                <tr class="border-b border-[var(--gold)]/10 hover:bg-[var(--gold)]/5">
                  <td class="p-2">
                    <p class="font-semibold"><?= htmlspecialchars($p['nombre']) ?></p>
                    <p class="text-xs text-[var(--cream)]/50"><?= htmlspecialchars($p['categoria'] ?? 'N/A') ?></p>
                  </td>
                  <td class="p-2">
                    <p class="text-[var(--gold)]">$<?= number_format($p['precio_venta'], 2) ?></p>
                    <p class="text-xs text-[var(--cream)]/50">Compra: $<?= number_format($p['precio_compra'], 2) ?></p>
                  </td>
                  <td class="p-2 text-center">
                    <span class="px-2 py-1 rounded <?= $p['stock'] < 10 ? 'bg-red-500/20 text-red-400' : 'bg-green-500/20 text-green-400' ?>">
                      <?= $p['stock'] ?>
                    </span>
                  </td>
                  <td class="p-2">
                    <p class="text-xs"><?= htmlspecialchars($p['nombre_proveedor'] ?? 'N/A') ?></p>
                  </td>
                  <td class="p-2 text-center">
                    <a href="editar_producto.php?id=<?= $p['id_producto'] ?>" class="text-[var(--gold)] hover:text-amber-400 px-2" title="Editar">
                      <i class="fas fa-edit"></i>
                    </a>
                    <form action="procesar_inventario.php" method="POST" class="inline-block" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este producto?');">
                        <input type="hidden" name="id_producto" value="<?= $p['id_producto'] ?>">
                        <input type="hidden" name="accion" value="eliminar">
                        <button type="submit" class="text-red-400 hover:text-red-300 px-2" title="Eliminar">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                  </td>
                </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </main>

  <script>
    document.addEventListener('contextmenu', e => e.preventDefault());
  </script>
</body>
</html>


