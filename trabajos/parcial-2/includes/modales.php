<!-- Modal: Equipo -->
<div id="modal-equipo" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
  <div class="modal-content glass rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto accent-border">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-white">
        <i class="fa-solid fa-laptop mr-2 text-emerald-400"></i>
        <span id="modal-equipo-titulo">Nuevo Equipo</span>
      </h3>
      <button onclick="cerrarModalEquipo()" class="text-slate-400 hover:text-white">
        <i class="fa-solid fa-times text-xl"></i>
      </button>
    </div>
    
    <form id="form-equipo" onsubmit="guardarEquipo(event)">
      <input type="hidden" id="equipo-id" name="id_equipo">
      
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-slate-300 mb-2">Fecha de Ingreso *</label>
          <input type="date" id="equipo-fecha-ingreso" name="fecha_ingreso" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Equipo *</label>
          <input type="text" id="equipo-nombre" name="equipo" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white"
            placeholder="Nombre del equipo">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Marca *</label>
          <input type="text" id="equipo-marca" name="marca" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white"
            placeholder="Marca del equipo">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Serie *</label>
          <input type="text" id="equipo-serie" name="serie" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white"
            placeholder="Número de serie">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Tipo de Servicio *</label>
          <select id="equipo-tipo-servicio" name="tipo_servicio" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
            <option value="mantenimiento">Mantenimiento</option>
            <option value="reparacion">Reparación</option>
            <option value="calibracion">Calibración</option>
            <option value="revision">Revisión</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Estado</label>
          <select id="equipo-estado" name="estado"
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
            <option value="ingresado">Ingresado</option>
            <option value="en_proceso">En Proceso</option>
            <option value="completado">Completado</option>
            <option value="entregado">Entregado</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Fecha de Salida</label>
          <input type="date" id="equipo-fecha-salida" name="fecha_salida"
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Costo Inicial</label>
          <input type="number" step="0.01" id="equipo-costo-inicial" name="costo_inicial" value="0"
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Costo Final</label>
          <input type="number" step="0.01" id="equipo-costo-final" name="costo_final" value="0"
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
        </div>
      </div>
      
      <div class="mt-4">
        <label class="block text-sm text-slate-300 mb-2">Observación</label>
        <textarea id="equipo-observacion" name="observacion" rows="3"
          class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white"
          placeholder="Observaciones adicionales"></textarea>
      </div>
      
      <div class="mt-6 flex gap-4 justify-end">
        <button type="button" onclick="cerrarModalEquipo()" 
          class="px-6 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 transition text-white">
          Cancelar
        </button>
        <button type="submit" 
          class="px-6 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 transition text-white font-semibold">
          <i class="fa-solid fa-save mr-2"></i>Guardar
        </button>
      </div>
    </form>
  </div>
</div>

<!-- Modal: Mantenimiento -->
<div id="modal-mantenimiento" class="fixed inset-0 z-50 hidden items-center justify-center bg-black/50 backdrop-blur-sm">
  <div class="modal-content glass rounded-2xl p-6 max-w-2xl w-full mx-4 max-h-[90vh] overflow-y-auto accent-border">
    <div class="flex items-center justify-between mb-6">
      <h3 class="text-xl font-bold text-white">
        <i class="fa-solid fa-wrench mr-2 text-emerald-400"></i>
        <span id="modal-mantenimiento-titulo">Nuevo Mantenimiento</span>
      </h3>
      <button onclick="cerrarModalMantenimiento()" class="text-slate-400 hover:text-white">
        <i class="fa-solid fa-times text-xl"></i>
      </button>
    </div>
    
    <form id="form-mantenimiento" onsubmit="guardarMantenimiento(event)">
      <input type="hidden" id="mantenimiento-id" name="id_mantenimiento">
      
      <div class="grid md:grid-cols-2 gap-4">
        <div>
          <label class="block text-sm text-slate-300 mb-2">Equipo *</label>
          <select id="mantenimiento-equipo" name="id_equipo" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
            <option value="">Seleccione un equipo</option>
            <?php foreach ($equipos as $eq): ?>
            <option value="<?php echo $eq['id_equipo']; ?>">
              <?php echo htmlspecialchars($eq['equipo'] . ' - ' . $eq['marca'] . ' (' . $eq['numero_ingreso'] . ')'); ?>
            </option>
            <?php endforeach; ?>
          </select>
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Tipo de Mantenimiento *</label>
          <select id="mantenimiento-tipo" name="tipo_mantenimiento" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
            <option value="predictivo">Predictivo</option>
            <option value="preventivo">Preventivo</option>
            <option value="correctivo">Correctivo</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Estado *</label>
          <select id="mantenimiento-estado" name="estado" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
            <option value="por_hacer">Por Hacer</option>
            <option value="en_espera_material">En Espera de Material</option>
            <option value="en_revision">En Revisión</option>
            <option value="terminada">Terminada</option>
          </select>
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Porcentaje de Avance</label>
          <input type="range" id="mantenimiento-porcentaje" name="porcentaje_avance" min="0" max="100" value="0"
            class="w-full" oninput="document.getElementById('porcentaje-valor').textContent = this.value + '%'">
          <div class="text-center text-sm text-emerald-400 mt-1">
            <span id="porcentaje-valor">0%</span>
          </div>
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Fecha de Inicio *</label>
          <input type="date" id="mantenimiento-fecha-inicio" name="fecha_inicio" required
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Fecha Fin Prevista</label>
          <input type="date" id="mantenimiento-fecha-fin" name="fecha_fin_prevista"
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
        </div>
        
        <div>
          <label class="block text-sm text-slate-300 mb-2">Costo de Mantenimiento</label>
          <input type="number" step="0.01" id="mantenimiento-costo" name="costo_mantenimiento" value="0"
            class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white">
        </div>
      </div>
      
      <div class="mt-4">
        <label class="block text-sm text-slate-300 mb-2">Descripción</label>
        <textarea id="mantenimiento-descripcion" name="descripcion" rows="3"
          class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white"
          placeholder="Descripción del mantenimiento"></textarea>
      </div>
      
      <div class="mt-4">
        <label class="block text-sm text-slate-300 mb-2">Material Requerido</label>
        <textarea id="mantenimiento-material" name="material_requerido" rows="2"
          class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white"
          placeholder="Materiales necesarios para el mantenimiento"></textarea>
      </div>
      
      <div class="mt-4">
        <label class="block text-sm text-slate-300 mb-2">Observaciones</label>
        <textarea id="mantenimiento-observaciones" name="observaciones" rows="2"
          class="w-full p-3 rounded-lg bg-slate-800 border border-slate-700 text-white"
          placeholder="Observaciones adicionales"></textarea>
      </div>
      
      <div class="mt-6 flex gap-4 justify-end">
        <button type="button" onclick="cerrarModalMantenimiento()" 
          class="px-6 py-2 rounded-lg bg-slate-700 hover:bg-slate-600 transition text-white">
          Cancelar
        </button>
        <button type="submit" 
          class="px-6 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 transition text-white font-semibold">
          <i class="fa-solid fa-save mr-2"></i>Guardar
        </button>
      </div>
    </form>
  </div>
</div>

