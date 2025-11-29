<?php include("includes/header.php"); ?>
<section class="max-w-6xl mx-auto px-6 py-16">
  <h2 class="text-3xl font-bold mb-6" data-aos="fade-up">Nuestros Clientes</h2>
  <p class="text-slate-300 mb-10">Empresas que confían en Sucesos y Más para impulsar su presencia digital.</p>
  <div class="grid sm:grid-cols-2 md:grid-cols-3 gap-8">
    <?php
    $clientes = ['EcoPanamá', 'Tienda Luna', 'InnovaTech', 'Café Central', 'Vía Express', 'SmartHost'];
    foreach($clientes as $nombre){
      echo "<div class='p-6 glass rounded-xl text-center' data-aos='zoom-in'>
              <i class='fa-solid fa-building text-3xl text-emerald-400'></i>
              <h4 class='mt-3 font-semibold'>$nombre</h4>
              <p class='text-sm text-slate-400'>Cliente satisfecho</p>
            </div>";
    }
    ?>
  </div>
</section>
<?php include("includes/footer.php"); ?>
