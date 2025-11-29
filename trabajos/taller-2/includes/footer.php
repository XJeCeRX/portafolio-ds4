</main>
<footer class="py-8 border-t border-slate-700 mt-6">
  <div class="max-w-6xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <p class="text-sm text-slate-400">© <?php echo date('Y'); ?> Sucesos y Más — Agencia Creativa</p>
    <div class="flex items-center gap-4 text-slate-300">
      <a href="#" title="Facebook" class="hover:text-white"><i class="fa-brands fa-facebook"></i></a>
      <a href="#" title="Instagram" class="hover:text-white"><i class="fa-brands fa-instagram"></i></a>
      <a href="#" title="Correo" class="hover:text-white"><i class="fa-solid fa-envelope"></i></a>
    </div>
  </div>
</footer>
<script>
document.addEventListener('DOMContentLoaded',()=>AOS.init({once:true}));
const btn=document.getElementById('btnMobile'),menu=document.getElementById('mobileMenu');
btn?.addEventListener('click',()=>menu.classList.toggle('hidden'));
</script>
</body>
</html>
