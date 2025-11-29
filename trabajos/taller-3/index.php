<?php
$mensaje = '';
if (isset($_GET['ok'])) {
  $mensaje = '✅ ¡Gracias! Tu mensaje fue enviado correctamente.';
} elseif (isset($_GET['error'])) {
  $mensaje = '⚠️ Ocurrió un error. Verifica los campos e inténtalo de nuevo.';
}
?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Chinos Café — Sistema POS</title>

  <!-- Google Font -->
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">

  <!-- Tailwind CDN -->
  <script src="https://cdn.tailwindcss.com"></script>

  <!-- AOS -->
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script defer src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>

  <!-- Font Awesome -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

  <style>
    :root {
      --coffee1: #3e2723;
      --coffee2: #5d4037;
      --coffee3: #795548;
      --cream: #f7f3ee;
      --gold: #d7b98c;
    }
    body {
      font-family: 'Poppins', sans-serif;
      background: linear-gradient(180deg, var(--coffee1), var(--coffee2));
      color: var(--cream);
    }
    .glass {
      background: rgba(255,255,255,0.05);
      backdrop-filter: blur(10px);
      border: 1px solid rgba(255,255,255,0.08);
    }
  </style>
</head>

<body>
  <!-- NAV -->
  <header class="fixed top-0 left-0 w-full z-50 glass">
    <nav class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
      <a href="#" class="flex items-center gap-3">
        <div class="w-10 h-10 bg-[var(--gold)] rounded-lg flex items-center justify-center text-[var(--coffee1)] text-xl shadow-lg">
          <i class="fa-solid fa-mug-saucer"></i>
        </div>
        <span class="font-semibold text-[var(--gold)] text-lg">Chinos Café</span>
      </a>

      <div class="hidden md:flex items-center gap-4 text-[var(--cream)]/90">
        <a href="#inicio" class="hover:text-[var(--gold)] transition">Inicio</a>
        <a href="ventas.php" class="px-3 py-2 rounded-full bg-[var(--gold)] text-[var(--coffee1)] font-semibold hover:bg-amber-400 transition"><i class="fas fa-shopping-cart"></i> Ventas</a>
        <a href="inventario.php" class="px-3 py-2 rounded-full border border-[var(--gold)]/50 text-[var(--gold)] hover:bg-[var(--gold)] hover:text-[var(--coffee1)] transition"><i class="fas fa-boxes-stacked"></i> Inventario</a>
        <a href="proveedores.php" class="px-3 py-2 rounded-full border border-[var(--gold)]/50 text-[var(--gold)] hover:bg-[var(--gold)] hover:text-[var(--coffee1)] transition"><i class="fas fa-truck-field"></i> Proveedores</a>
        <a href="#contacto" class="hover:text-[var(--gold)] transition">Contacto</a>
      </div>

      <button id="btnMobile" class="md:hidden text-[var(--gold)] text-xl">
        <i class="fa-solid fa-bars"></i>
      </button>
    </nav>

    <div id="mobileMenu" class="hidden md:hidden flex flex-col bg-[var(--coffee2)] text-[var(--cream)] p-4">
      <a href="#inicio" class="py-2"><i class="fas fa-home"></i> Inicio</a>
      <a href="ventas.php" class="py-2"><i class="fas fa-shopping-cart"></i> Ventas</a>
      <a href="inventario.php" class="py-2"><i class="fas fa-boxes-stacked"></i> Inventario</a>
      <a href="proveedores.php" class="py-2"><i class="fas fa-truck-field"></i> Proveedores</a>
      <a href="#contacto" class="py-2"><i class="fas fa-envelope"></i> Contacto</a>
    </div>
  </header>

  <!-- HERO -->
  <main id="inicio" class="pt-24">
    <section class="max-w-6xl mx-auto px-6 py-16 grid md:grid-cols-2 gap-10 items-center">
      <div data-aos="fade-right" data-aos-duration="1000">
        <h1 class="text-4xl md:text-5xl font-extrabold leading-tight text-[var(--gold)]">Chinos Café ☕</h1>
        <p class="mt-4 text-[var(--cream)]/90">Sistema POS Web moderno para gestionar ventas, proveedores e inventario con replicación entre sucursales. Sencillo, rápido y elegante.</p>
        <div class="mt-8 flex flex-wrap gap-4">
          <a href="ventas.php" class="px-5 py-3 rounded-full bg-[var(--gold)] text-[var(--coffee1)] font-semibold hover:scale-105 transition">
            <i class="fas fa-shopping-cart"></i> Abrir Ventas
          </a>
          <a href="inventario.php" class="px-5 py-3 rounded-full border border-[var(--gold)] text-[var(--gold)] hover:bg-[var(--gold)] hover:text-[var(--coffee1)] transition">
            <i class="fas fa-boxes-stacked"></i> Inventario
          </a>
          <a href="proveedores.php" class="px-5 py-3 rounded-full border border-[var(--gold)] text-[var(--gold)] hover:bg-[var(--gold)] hover:text-[var(--coffee1)] transition">
            <i class="fas fa-truck-field"></i> Proveedores
          </a>
        </div>
      </div>

      <div data-aos="fade-left" data-aos-duration="1000" class="relative">
        <div class="absolute -z-10 -top-8 -left-8 w-72 h-72 bg-[var(--gold)] opacity-20 blur-3xl rounded-full"></div>
        <img src="https://cdn-icons-png.flaticon.com/512/2935/2935416.png" class="w-64 mx-auto animate-pulse" alt="café">
      </div>
    </section>

    <!-- SERVICIOS -->
    <section id="servicios" class="max-w-6xl mx-auto px-6 py-16">
      <h2 class="text-3xl font-bold text-[var(--gold)] mb-8" data-aos="fade-up">Nuestros Servicios</h2>
      <div class="grid md:grid-cols-3 gap-6">
        <div class="p-6 rounded-xl glass hover:scale-[1.03] transition" data-aos="zoom-in">
          <i class="fa-solid fa-cash-register text-3xl text-[var(--gold)]"></i>
          <h3 class="mt-3 font-semibold text-lg">Punto de Venta</h3>
          <p class="text-[var(--cream)]/80 mt-2 text-sm">Ventas rápidas, facturación y control de tickets.</p>
        </div>
        <div class="p-6 rounded-xl glass hover:scale-[1.03] transition" data-aos="zoom-in" data-aos-delay="100">
          <i class="fa-solid fa-boxes-stacked text-3xl text-[var(--gold)]"></i>
          <h3 class="mt-3 font-semibold text-lg">Inventario</h3>
          <p class="text-[var(--cream)]/80 mt-2 text-sm">Gestión de existencias, precios y alertas de stock.</p>
        </div>
        <div class="p-6 rounded-xl glass hover:scale-[1.03] transition" data-aos="zoom-in" data-aos-delay="200">
          <i class="fa-solid fa-truck-field text-3xl text-[var(--gold)]"></i>
          <h3 class="mt-3 font-semibold text-lg">Proveedores</h3>
          <p class="text-[var(--cream)]/80 mt-2 text-sm">Registra y controla los proveedores de suministros.</p>
        </div>
      </div>
    </section>

    <!-- CONTACTO -->
    <section id="contacto" class="max-w-6xl mx-auto px-6 py-16">
      <h2 class="text-3xl font-bold text-[var(--gold)] mb-6" data-aos="fade-up">Contáctanos</h2>

      <?php if ($mensaje): ?>
        <div class="mb-6 p-3 rounded bg-[var(--gold)]/10 border border-[var(--gold)]/20 text-[var(--gold)] text-sm text-center">
          <?= htmlspecialchars($mensaje) ?>
        </div>
      <?php endif; ?>

      <div class="grid md:grid-cols-2 gap-10 items-center">
        <div data-aos="fade-right">
          <p class="text-[var(--cream)]/90 mb-4">📍 Calle Central #123, David — Chiriquí</p>
          <p class="text-[var(--cream)]/90 mb-4">📞 +507 6000-0000</p>
          <p class="text-[var(--cream)]/90 mb-4">✉️ contacto@chinoscafe.com</p>
        </div>

        <form action="contacto.php" method="POST" class="p-6 glass rounded-xl space-y-4" data-aos="fade-left">
          <div>
            <label class="block text-sm mb-1">Nombre</label>
            <input name="nombre" required class="w-full p-3 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] focus:ring-2 focus:ring-[var(--gold)]">
          </div>
          <div>
            <label class="block text-sm mb-1">Correo</label>
            <input type="email" name="correo" required class="w-full p-3 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] focus:ring-2 focus:ring-[var(--gold)]">
          </div>
          <div>
            <label class="block text-sm mb-1">Mensaje</label>
            <textarea name="mensaje" rows="4" required class="w-full p-3 rounded bg-[var(--coffee3)]/40 border border-[var(--gold)]/20 text-[var(--cream)] focus:ring-2 focus:ring-[var(--gold)]"></textarea>
          </div>
          <button type="submit" class="px-4 py-2 rounded-full bg-[var(--gold)] text-[var(--coffee1)] font-semibold hover:bg-amber-400 transition">Enviar</button>
        </form>
      </div>
    </section>

    <!-- FOOTER -->
    <footer class="border-t border-[var(--gold)]/20 py-8 text-center text-[var(--cream)]/70">
      © <?php echo date('Y'); ?> Chinos Café — Todos los derechos reservados ☕
    </footer>
  </main>

  <!-- JS -->
  <script>
    document.addEventListener("DOMContentLoaded", () => {
      AOS.init({ once: true, duration: 800 });
      document.getElementById('btnMobile').addEventListener('click', () => {
        document.getElementById('mobileMenu').classList.toggle('hidden');
      });
    });
  </script>

  <script>
// 🔒 Protección básica: bloquea clic derecho y selección de texto
document.addEventListener('contextmenu', e => e.preventDefault());
document.addEventListener('selectstart', e => e.preventDefault());
document.addEventListener('copy', e => e.preventDefault());
document.addEventListener('cut', e => e.preventDefault());
document.addEventListener('keydown', e => {
  // bloquea F12, Ctrl+Shift+I, Ctrl+U, Ctrl+S
  if (
    e.key === "F12" ||
    (e.ctrlKey && e.shiftKey && e.key === "I") ||
    (e.ctrlKey && e.key === "U") ||
    (e.ctrlKey && e.key === "S")
  ) {
    e.preventDefault();
  }
});
</script>
</body>
</html>


