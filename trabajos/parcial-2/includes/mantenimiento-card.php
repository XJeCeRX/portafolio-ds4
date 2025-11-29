<?php
// Card de mantenimiento para mostrar en las columnas
$estado = $m['estado'];
$porcentaje = $m['porcentaje_avance'] ?? 0;
$tipo = $m['tipo_mantenimiento'];
?>
<div class="glass rounded-lg p-4 border border-slate-700 hover:border-emerald-500/50 transition cursor-move" 
     draggable="true" 
     data-id="<?php echo $m['id_mantenimiento']; ?>"
     data-estado="<?php echo $estado; ?>"
     onclick="abrirModalEditarMantenimiento(<?php echo $m['id_mantenimiento']; ?>)">
  
  <div class="flex items-start justify-between mb-2">
    <div class="flex-1">
      <h4 class="font-semibold text-white text-sm"><?php echo htmlspecialchars($m['equipo']); ?></h4>
      <p class="text-xs text-slate-400"><?php echo htmlspecialchars($m['marca']); ?> - <?php echo htmlspecialchars($m['serie']); ?></p>
    </div>
    <span class="px-2 py-1 rounded-full text-xs <?php
      echo $tipo === 'predictivo' ? 'bg-blue-500/20 text-blue-200' :
          ($tipo === 'preventivo' ? 'bg-green-500/20 text-green-200' : 'bg-red-500/20 text-red-200');
    ?>">
      <?php echo ucfirst($tipo); ?>
    </span>
  </div>
  
  <?php if (!empty($m['descripcion'])): ?>
  <p class="text-xs text-slate-300 mb-3 line-clamp-2"><?php echo htmlspecialchars($m['descripcion']); ?></p>
  <?php endif; ?>
  
  <!-- Barra de progreso -->
  <div class="mb-3">
    <div class="flex items-center justify-between mb-1">
      <span class="text-xs text-slate-400">Progreso</span>
      <span class="text-xs font-semibold text-emerald-400"><?php echo $porcentaje; ?>%</span>
    </div>
    <div class="progress-bar">
      <div class="progress-fill" style="width: <?php echo $porcentaje; ?>%"></div>
    </div>
  </div>
  
  <div class="flex items-center justify-between text-xs text-slate-400">
    <span><i class="fa-solid fa-calendar mr-1"></i><?php echo formatDate($m['fecha_inicio']); ?></span>
    <?php if ($m['tecnico_asignado']): ?>
    <span><i class="fa-solid fa-user mr-1"></i><?php echo htmlspecialchars($m['tecnico_asignado']); ?></span>
    <?php endif; ?>
  </div>
  
  <?php if (!empty($m['material_requerido'])): ?>
  <div class="mt-2 pt-2 border-t border-slate-700">
    <p class="text-xs text-orange-300">
      <i class="fa-solid fa-box mr-1"></i>Material: <?php echo htmlspecialchars(substr($m['material_requerido'], 0, 50)); ?>
    </p>
  </div>
  <?php endif; ?>
</div>



