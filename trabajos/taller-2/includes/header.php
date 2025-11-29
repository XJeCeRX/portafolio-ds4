<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width,initial-scale=1" />
  <title>Sucesos y Más — Agencia Creativa</title>

  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600;700&display=swap" rel="stylesheet">
  <script src="https://cdn.tailwindcss.com"></script>
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <script defer src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css"/>

  <style>
    :root { --glass: rgba(255,255,255,0.06); }
    body { font-family:'Poppins',system-ui; background:linear-gradient(to bottom,#0f172a,#1e293b); color:#f8fafc; }
    .glass { backdrop-filter:blur(6px); background:rgba(255,255,255,0.03); border:1px solid rgba(255,255,255,0.05); }
    body,html{scroll-behavior:smooth}
    body{-webkit-user-select:none;user-select:none}
  </style>
</head>
<body oncontextmenu="return false">
<header class="w-full fixed top-0 left-0 z-50 glass">
  <nav class="max-w-6xl mx-auto px-6 py-4 flex items-center justify-between">
    <a href="index.php" class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-lg">
        <i class="fa-solid fa-bolt text-white"></i>
      </div>
      <div>
        <p class="text-sm font-semibold">Sucesos</p>
        <p class="text-xs -mt-1 text-slate-300">& Más</p>
      </div>
    </a>
    <div class="hidden md:flex items-center gap-6 text-slate-300">
      <a href="index.php" class="hover:text-white">Inicio</a>
      <a href="servicios.php" class="hover:text-white">Servicios</a>
      <a href="productos.php" class="hover:text-white">Productos</a>
      <a href="clientes.php" class="hover:text-white">Clientes</a>
      <a href="contacto.php" class="hover:text-white">Contacto</a>
    </div>
    <button id="btnMobile" class="md:hidden p-2 rounded bg-slate-800 glass"><i class="fa-solid fa-bars"></i></button>
  </nav>
  <div id="mobileMenu" class="md:hidden px-6 pb-6 hidden glass">
    <a href="index.php" class="block py-2 text-slate-200">Inicio</a>
    <a href="servicios.php" class="block py-2 text-slate-200">Servicios</a>
    <a href="productos.php" class="block py-2 text-slate-200">Productos</a>
    <a href="clientes.php" class="block py-2 text-slate-200">Clientes</a>
    <a href="contacto.php" class="block py-2 text-slate-200">Contacto</a>
  </div>
</header>
<main class="pt-28">
