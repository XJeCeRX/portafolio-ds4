<?php include("includes/header.php"); ?>
<section class="max-w-6xl mx-auto px-6 py-16">
  <h2 class="text-3xl font-bold mb-6" data-aos="fade-up">Contáctanos</h2>
  <div class="grid md:grid-cols-2 gap-10 items-start">
    <div data-aos="fade-right">
      <ul class="text-slate-300 space-y-3">
        <li><i class="fa-solid fa-location-dot text-emerald-400 mr-2"></i> Av. Principal #12, Ciudad Panamá</li>
        <li><i class="fa-solid fa-phone text-emerald-400 mr-2"></i> +507 6789-0000</li>
        <li><i class="fa-solid fa-envelope text-emerald-400 mr-2"></i> contacto@sucesosymas.com</li>
      </ul>
    </div>
    <form class="p-6 glass rounded-xl" data-aos="fade-left" onsubmit="event.preventDefault();alert('Formulario simulado — datos no enviados');">
      <label class="block text-sm text-slate-300">Nombre</label>
      <input class="mt-2 w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white" required>
      <label class="block text-sm text-slate-300 mt-4">Correo</label>
      <input type="email" class="mt-2 w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white" required>
      <label class="block text-sm text-slate-300 mt-4">Mensaje</label>
      <textarea rows="4" class="mt-2 w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white" required></textarea>
      <button type="submit" class="mt-4 px-4 py-2 rounded-full bg-emerald-500 hover:bg-emerald-600 transition text-white font-semibold">Enviar</button>
    </form>
  </div>
</section>
<?php include("includes/footer.php"); ?>
