<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
echo "Prueba: la página carga<br>";
require_once 'config/database.php';
session_start();

$mensaje = '';
if (isset($_GET['ok'])) $mensaje = '✅ Venta procesada correctamente';
if (isset($_GET['error'])) $mensaje = '⚠️ Error al procesar la venta';

// Obtener productos disponibles
$productos = [];
$conn = getDB();
$result = $conn->query("SELECT * FROM inventario WHERE stock > 0 ORDER BY nombre");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $productos[] = $row;
  }
}

// Obtener últimas ventas
$ventas = [];
$result = $conn->query("SELECT v.*, 
  (SELECT COUNT(*) FROM detalle_venta WHERE id_venta = v.id_venta) as items
  FROM ventas v ORDER BY fecha_venta DESC LIMIT 10");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $ventas[] = $row;
  }
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Ventas — Chinos Café</title>
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
        <span class="font-semibold text-[var(--gold)] text-lg">Chinos Café — Ventas</span>
      </a>
      <div class="flex items-center gap-4 text-[var(--cream)]/90">
        <a href="index.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-home"></i> Inicio</a>
        <a href="inventario.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-boxes-stacked"></i> Inventario</a>
        <a href="proveedores.php" class="hover:text-[var(--gold)] transition"><i class="fas fa-truck-field"></i> Proveedores</a>
      </div>
    </nav>
  </header>

  <main class="pt-24 max-w-7xl mx-auto px-6">
    <?php if ($mensaje): ?>
      <div class="mb-4 p-3 rounded bg-[var(--gold)]/10 border border-[var(--gold)]/20 text-[var(--gold)] text-sm text-center">
        <?= htmlspecialchars($mensaje) ?>
      </div>
    <?php endif; ?>

    <div class="grid lg:grid-cols-3 gap-6">
      <!-- COLUMNA 1: Carrito -->
      <div class="lg:col-span-2 space-y-6">
        <div class="p-6 rounded-xl glass">
          <h2 class="text-xl font-bold text-[var(--gold)] mb-4 flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i> Carrito de Compras
          </h2>
          
          <form id="formVenta" method="POST" action="procesar_venta.php">
            <div class="mb-4">
              <label class="block text-sm mb-1">Cliente</label>
              <input name="cliente" placeholder="Cliente General" class="w-full p-3 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)]">
            </div>

            <div id="carrito" class="space-y-2 max-h-96 overflow-y-auto">
              <p class="text-center text-[var(--cream)]/50 py-4">El carrito está vacío</p>
            </div>

            <div class="mt-4 p-4 glass rounded border-t border-[var(--gold)]/20">
              <div class="flex justify-between text-lg font-bold text-[var(--gold)] mb-2">
                <span>TOTAL:</span>
                <span id="total">$0.00</span>
              </div>
              <select name="tipo_pago" class="w-full p-3 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] mb-3">
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
                <option value="transferencia">Transferencia</option>
              </select>
              <button type="submit" class="w-full px-4 py-3 rounded-full bg-[var(--gold)] text-[var(--coffee1)] font-semibold hover:bg-amber-400 transition">
                <i class="fas fa-cash-register"></i> Procesar Venta
              </button>
            </div>
          </form>
        </div>

        <!-- Historial -->
        <div class="p-6 rounded-xl glass">
          <h3 class="text-lg font-bold text-[var(--gold)] mb-3 flex items-center gap-2">
            <i class="fas fa-history"></i> Últimas Ventas
          </h3>
          <div class="space-y-2">
            <?php foreach ($ventas as $v): ?>
            <div class="p-3 rounded glass flex justify-between items-center">
              <div>
                <p class="text-sm font-semibold">#<?= htmlspecialchars($v['numero_factura']) ?></p>
                <p class="text-xs text-[var(--cream)]/60"><?= date('d/m/Y H:i', strtotime($v['fecha_venta'])) ?></p>
              </div>
              <span class="text-[var(--gold)] font-bold">$<?= number_format($v['total'], 2) ?></span>
            </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>

      <!-- COLUMNA 2: Productos -->
      <div class="p-6 rounded-xl glass">
        <h2 class="text-xl font-bold text-[var(--gold)] mb-4 flex items-center gap-2">
          <i class="fas fa-boxes-stacked"></i> Productos Disponibles
        </h2>
        <div class="space-y-2 max-h-[600px] overflow-y-auto">
          <?php foreach ($productos as $p): ?>
          <button onclick="agregarProducto(<?= htmlspecialchars(json_encode($p)) ?>)" 
                  class="w-full p-3 rounded glass hover:bg-[var(--gold)]/10 transition text-left">
            <div class="flex justify-between items-start">
              <div>
                <p class="font-semibold"><?= htmlspecialchars($p['nombre']) ?></p>
                <p class="text-xs text-[var(--cream)]/60">Stock: <?= $p['stock'] ?></p>
              </div>
              <span class="text-[var(--gold)] font-bold">$<?= number_format($p['precio_venta'], 2) ?></span>
            </div>
          </button>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </main>

  <script>
    let carrito = [];
    let contadorItem = 0;

    function agregarProducto(p) {
      if (p.stock <= 0) { alert('❌ Sin stock disponible'); return; }
      
      const item = { id: ++contadorItem, producto: p.id_producto, nombre: p.nombre, precio: parseFloat(p.precio_venta), cantidad: 1 };
      carrito.push(item);
      renderCarrito();
    }

    function renderCarrito() {
      const cont = document.getElementById('carrito');
      const total = document.getElementById('total');
      
      if (carrito.length === 0) {
        cont.innerHTML = '<p class="text-center text-[var(--cream)]/50 py-4">El carrito está vacío</p>';
        total.textContent = '$0.00';
        return;
      }

      cont.innerHTML = carrito.map(item => `
        <div class="p-3 rounded glass flex justify-between items-center">
          <div class="flex-1">
            <input type="hidden" name="producto_${item.id}" value="${item.producto}">
            <input type="hidden" name="cantidad_${item.id}" value="${item.cantidad}">
            <input type="hidden" name="precio_${item.id}" value="${item.precio}">
            <p class="text-sm font-semibold">${item.nombre}</p>
          </div>
          <div class="flex items-center gap-3">
            <button type="button" onclick="quitarItem(${item.id})" class="text-red-400 hover:text-red-300">
              <i class="fas fa-trash"></i>
            </button>
            <span class="text-[var(--gold)] font-bold">$${item.precio.toFixed(2)}</span>
          </div>
        </div>
      `).join('');

      const suma = carrito.reduce((acc, item) => acc + item.precio, 0);
      total.textContent = '$' + suma.toFixed(2);
    }

    function quitarItem(id) {
      carrito = carrito.filter(item => item.id !== id);
      renderCarrito();
    }

    document.getElementById('formVenta').addEventListener('submit', function(e) {
      if (carrito.length === 0) {
        e.preventDefault();
        alert('⚠️ El carrito está vacío');
        return false;
      }
    });
  </script>

  <script>
    document.addEventListener('contextmenu', e => e.preventDefault());
    document.addEventListener('selectstart', e => e.preventDefault());
  </script>
</body>
</html>


