<?php
require_once 'config/database.php';

$mensaje = '';
if (isset($_GET['ok'])) $mensaje = '✅ Operación realizada correctamente';
if (isset($_GET['error'])) $mensaje = '⚠️ Error en la operación';

// Obtener proveedores
$conn = getDB();
$proveedores = [];
$result = $conn->query("SELECT p.*, 
  (SELECT COUNT(*) FROM inventario WHERE id_proveedor = p.id_proveedor) as productos
  FROM proveedores p ORDER BY nombre");
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
  <title>Proveedores — Chinos Café</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
  <style>
    :root { --coffee1: #3e2723; --coffee2: #5d4037; --coffee3: #795548; --cream: #f7f3ee; --gold: #d7b98c; }
    body { font-family: 'Poppins', sans-serif; background: linear-gradient(180deg, var(--coffee1), var(--coffee2)); color: var(--cream); }
    .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.08); }
  </style>
</head>
<body>
  <header class="fixed top-0 left-0 w-full z-50 glass">
    <nav class="max-w-7xl mx-auto px-6 py-4 flex items-center justify-between">
      <a href="index.php" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-[var(--gold)] rounded-lg flex items-center justify-center text-[var(--coffee1)] text-xl shadow-lg">
          <i class="fa-solid fa-mug-saucer"></i>
        </div>
        <span class="font-semibold text-[var(--gold)] text-lg">Chinos Café — Proveedores</span>
      </a>
      <div class="flex items-center gap-4 text-[var(--cream)]/90">
        <a href="index.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-home"></i> Inicio</a>
        <a href="ventas.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-shopping-cart"></i> Ventas</a>
        <a href="inventario.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-boxes-stacked"></i> Inventario</a>
      </div>
    </nav>
  </header>

  <main class="pt-24 max-w-6xl mx-auto px-6 pb-8">
    <?php if ($mensaje): ?>
      <div class="mb-4 p-3 rounded bg-[var(--gold)]/10 border border-[var(--gold)]/20 text-[var(--gold)] text-sm text-center">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-3 gap-6">
      <!-- Formulario -->
      <div class="lg:col-span-1">
        <div class="p-6 rounded-xl glass">
          <h2 class="text-xl font-bold text-[var(--gold)] mb-4 flex items-center gap-2">
            <i class="fas fa-plus-circle"></i> Agregar Proveedor
          </h2>
          
          <form action="procesar_proveedor.php" method="POST" class="space-y-3">
            <div>
              <label class="block text-xs mb-1">Nombre</label>
              <input name="nombre" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
            </div>

            <div>
              <label class="block text-xs mb-1">Contacto</label>
              <input name="contacto" required class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
            </div>

            <div>
              <label class="block text-xs mb-1">Teléfono</label>
              <input type="tel" name="telefono" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
            </div>

            <div>
              <label class="block text-xs mb-1">Email</label>
              <input type="email" name="email" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm">
            </div>

            <div>
              <label class="block text-xs mb-1">Dirección</label>
              <textarea name="direccion" rows="2" class="w-full p-2 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] text-sm"></textarea>
            </div>

            <button type="submit" class="w-full px-3 py-2 rounded-full bg-[var(--gold)] text-[var(--coffee1)] font-semibold hover:bg-amber-400 transition text-sm">
              <i class="fas fa-save"></i> Guardar Proveedor
            </button>
          </form>
        </div>
      </div>

      <!-- Lista -->
      <div class="lg:col-span-2">
        <div class="p-6 rounded-xl glass">
          <h2 class="text-xl font-bold text-[var(--gold)] mb-4 flex items-center gap-2">
            <i class="fas fa-truck-field"></i> Proveedores Registrados (<?= count($proveedores) ?>)
          </h2>

          <div class="space-y-3">
            <?php foreach ($proveedores as $p): ?>
            <div class="p-4 rounded glass hover:bg-[var(--gold)]/5 transition">
              <div class="flex justify-between items-start">
                <div class="flex-1">
                  <h3 class="font-semibold text-[var(--gold)]"><?= htmlspecialchars($p['nombre']) ?></h3>
                  <p class="text-sm text-[var(--cream)]/70 mt-1">Contacto: <?= htmlspecialchars($p['contacto']) ?></p>
                  <div class="flex gap-4 mt-2 text-xs text-[var(--cream)]/60">
                    <?php if ($p['telefono']): ?>
                    <span><i class="fas fa-phone"></i> <?= htmlspecialchars($p['telefono']) ?></span>
                    <?php endif; ?>
                    <?php if ($p['email']): ?>
                    <span><i class="fas fa-envelope"></i> <?= htmlspecialchars($p['email']) ?></span>
                    <?php endif; ?>
                  </div>
                  <?php if ($p['direccion']): ?>
                  <p class="text-xs text-[var(--cream)]/50 mt-1"><i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($p['direccion']) ?></p>
                  <?php endif; ?>
                </div>
                <div class="text-right">
                  <span class="px-3 py-1 rounded-full bg-[var(--gold)]/20 text-[var(--gold)] text-xs">
                    <?= $p['productos'] ?> productos
                  </span>
                </div>
              </div>
            </div>
            <?php endforeach; ?>

            <?php if (empty($proveedores)): ?>
            <p class="text-center text-[var(--cream)]/50 py-8">No hay proveedores registrados</p>
            <?php endif; ?>
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


