// Sistema de pestañas
function showTab(tabName) {
    // Ocultar todos los contenidos
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.remove('active');
    });
    
    // Remover clase active de todos los botones
    document.querySelectorAll('.tab-btn').forEach(btn => {
        btn.classList.remove('active', 'bg-emerald-500/20');
    });
    
    // Mostrar contenido seleccionado
    const content = document.getElementById('tab-' + tabName);
    if (content) {
        content.classList.add('active');
    }
    
    // Activar botón seleccionado
    const btn = document.querySelector(`[data-tab="${tabName}"]`);
    if (btn) {
        btn.classList.add('active', 'bg-emerald-500/20');
    }
    
    // Si es calendario, inicializar
    if (tabName === 'calendario') {
        setTimeout(() => {
            inicializarCalendario();
            cargarDatosCalendario();
        }, 100);
    }
}

// ========== EQUIPOS ==========

function abrirModalEquipo(id = null) {
    const modal = document.getElementById('modal-equipo');
    const titulo = document.getElementById('modal-equipo-titulo');
    const form = document.getElementById('form-equipo');
    
    form.reset();
    document.getElementById('equipo-id').value = '';
    
    if (id) {
        titulo.textContent = 'Editar Equipo';
        cargarEquipo(id);
    } else {
        titulo.textContent = 'Nuevo Equipo';
        document.getElementById('equipo-fecha-ingreso').value = new Date().toISOString().split('T')[0];
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModalEquipo() {
    const modal = document.getElementById('modal-equipo');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function cargarEquipo(id) {
    fetch(`api/equipos.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const eq = data.equipo;
                document.getElementById('equipo-id').value = eq.id_equipo;
                document.getElementById('equipo-fecha-ingreso').value = eq.fecha_ingreso;
                document.getElementById('equipo-nombre').value = eq.equipo;
                document.getElementById('equipo-marca').value = eq.marca;
                document.getElementById('equipo-serie').value = eq.serie;
                document.getElementById('equipo-tipo-servicio').value = eq.tipo_servicio;
                document.getElementById('equipo-estado').value = eq.estado;
                document.getElementById('equipo-fecha-salida').value = eq.fecha_salida || '';
                document.getElementById('equipo-costo-inicial').value = eq.costo_inicial;
                document.getElementById('equipo-costo-final').value = eq.costo_final;
                document.getElementById('equipo-observacion').value = eq.observacion || '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar el equipo');
        });
}

function guardarEquipo(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const id = formData.get('id_equipo');
    const method = id ? 'PUT' : 'POST';
    
    // Validaciones del lado del cliente
    const errores = [];
    
    if (!formData.get('equipo') || formData.get('equipo').trim().length === 0) {
        errores.push('El nombre del equipo es requerido');
    }
    
    if (!formData.get('marca') || formData.get('marca').trim().length === 0) {
        errores.push('La marca es requerida');
    }
    
    if (!formData.get('serie') || formData.get('serie').trim().length === 0) {
        errores.push('El número de serie es requerido');
    }
    
    if (!formData.get('fecha_ingreso')) {
        errores.push('La fecha de ingreso es requerida');
    } else {
        const fechaIngreso = new Date(formData.get('fecha_ingreso'));
        const hoy = new Date();
        hoy.setHours(23, 59, 59, 999);
        if (fechaIngreso > hoy) {
            errores.push('La fecha de ingreso no puede ser futura');
        }
    }
    
    // Validar fecha de salida
    if (formData.get('fecha_salida')) {
        const fechaSalida = new Date(formData.get('fecha_salida'));
        const fechaIngreso = new Date(formData.get('fecha_ingreso'));
        if (fechaSalida < fechaIngreso) {
            errores.push('La fecha de salida no puede ser anterior a la fecha de ingreso');
        }
    }
    
    // Validar costos
    const costoInicial = parseFloat(formData.get('costo_inicial') || 0);
    if (costoInicial < 0) {
        errores.push('El costo inicial no puede ser negativo');
    }
    
    const costoFinal = parseFloat(formData.get('costo_final') || 0);
    if (costoFinal < 0) {
        errores.push('El costo final no puede ser negativo');
    }
    
    if (errores.length > 0) {
        mostrarNotificacion('error', errores.join('<br>'));
        return;
    }
    
    const data = {};
    formData.forEach((value, key) => {
        if (key !== 'id_equipo' || id) {
            data[key] = value;
        }
    });
    
    if (id) {
        data.id_equipo = id;
    }
    
    // Mostrar loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Guardando...';
    
    fetch('api/equipos.php', {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarNotificacion('success', 'Equipo guardado correctamente');
            cerrarModalEquipo();
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacion('error', data.message || 'Error al guardar el equipo');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('error', 'Error de conexión. Por favor, intente nuevamente.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function editarEquipo(id) {
    abrirModalEquipo(id);
}

function eliminarEquipo(id) {
    if (!id || !Number.isInteger(parseInt(id))) {
        mostrarNotificacion('error', 'ID de equipo no válido');
        return;
    }
    
    if (!confirm('¿Está seguro de eliminar este equipo?\n\nEsta acción no se puede deshacer.')) {
        return;
    }
    
    fetch(`api/equipos.php`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id_equipo: parseInt(id) })
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarNotificacion('success', 'Equipo eliminado correctamente');
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacion('error', data.message || 'Error al eliminar el equipo');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('error', 'Error de conexión. Por favor, intente nuevamente.');
    });
}

function filtrarEquipos() {
    const busqueda = document.getElementById('filtro-busqueda').value;
    const estado = document.getElementById('filtro-estado').value;
    
    // Implementar filtrado en el cliente o hacer petición al servidor
    location.reload(); // Por ahora recargar con filtros
}

// ========== MANTENIMIENTOS ==========

function abrirModalMantenimiento(id = null) {
    const modal = document.getElementById('modal-mantenimiento');
    const titulo = document.getElementById('modal-mantenimiento-titulo');
    const form = document.getElementById('form-mantenimiento');
    
    form.reset();
    document.getElementById('mantenimiento-id').value = '';
    document.getElementById('mantenimiento-fecha-inicio').value = new Date().toISOString().split('T')[0];
    
    if (id) {
        titulo.textContent = 'Editar Mantenimiento';
        cargarMantenimiento(id);
    } else {
        titulo.textContent = 'Nuevo Mantenimiento';
    }
    
    modal.classList.remove('hidden');
    modal.classList.add('flex');
}

function cerrarModalMantenimiento() {
    const modal = document.getElementById('modal-mantenimiento');
    modal.classList.add('hidden');
    modal.classList.remove('flex');
}

function abrirModalEditarMantenimiento(id) {
    abrirModalMantenimiento(id);
}

function cargarMantenimiento(id) {
    fetch(`api/mantenimientos.php?id=${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const m = data.mantenimiento;
                document.getElementById('mantenimiento-id').value = m.id_mantenimiento;
                document.getElementById('mantenimiento-equipo').value = m.id_equipo;
                document.getElementById('mantenimiento-tipo').value = m.tipo_mantenimiento;
                document.getElementById('mantenimiento-estado').value = m.estado;
                document.getElementById('mantenimiento-porcentaje').value = m.porcentaje_avance || 0;
                document.getElementById('porcentaje-valor').textContent = (m.porcentaje_avance || 0) + '%';
                document.getElementById('mantenimiento-fecha-inicio').value = m.fecha_inicio;
                document.getElementById('mantenimiento-fecha-fin').value = m.fecha_fin_prevista || '';
                document.getElementById('mantenimiento-costo').value = m.costo_mantenimiento || 0;
                document.getElementById('mantenimiento-descripcion').value = m.descripcion || '';
                document.getElementById('mantenimiento-material').value = m.material_requerido || '';
                document.getElementById('mantenimiento-observaciones').value = m.observaciones || '';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar el mantenimiento');
        });
}

function guardarMantenimiento(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const id = formData.get('id_mantenimiento');
    const method = id ? 'PUT' : 'POST';
    
    // Validaciones del lado del cliente
    const errores = [];
    
    if (!formData.get('id_equipo') || formData.get('id_equipo') === '') {
        errores.push('Debe seleccionar un equipo');
    }
    
    if (!formData.get('tipo_mantenimiento')) {
        errores.push('El tipo de mantenimiento es requerido');
    }
    
    if (!formData.get('fecha_inicio')) {
        errores.push('La fecha de inicio es requerida');
    }
    
    // Validar fecha fin prevista
    if (formData.get('fecha_fin_prevista')) {
        const fechaFin = new Date(formData.get('fecha_fin_prevista'));
        const fechaInicio = new Date(formData.get('fecha_inicio'));
        if (fechaFin < fechaInicio) {
            errores.push('La fecha fin prevista no puede ser anterior a la fecha de inicio');
        }
    }
    
    // Validar porcentaje
    const porcentaje = parseInt(formData.get('porcentaje_avance') || 0);
    if (porcentaje < 0 || porcentaje > 100) {
        errores.push('El porcentaje de avance debe estar entre 0 y 100');
    }
    
    if (errores.length > 0) {
        mostrarNotificacion('error', errores.join('<br>'));
        return;
    }
    
    const data = {};
    formData.forEach((value, key) => {
        if (key !== 'id_mantenimiento' || id) {
            data[key] = value;
        }
    });
    
    if (id) {
        data.id_mantenimiento = id;
    }
    
    // Mostrar loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i>Guardando...';
    
    fetch('api/mantenimientos.php', {
        method: method,
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Error en la respuesta del servidor');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            mostrarNotificacion('success', 'Mantenimiento guardado correctamente');
            cerrarModalMantenimiento();
            setTimeout(() => location.reload(), 1000);
        } else {
            mostrarNotificacion('error', data.message || 'Error al guardar el mantenimiento');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        mostrarNotificacion('error', 'Error de conexión. Por favor, intente nuevamente.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

function filtrarMantenimientos(tipo) {
    // Implementar filtrado
    location.reload();
}

// ========== CALENDARIO ==========

let calendar;

function inicializarCalendario() {
    const calendarEl = document.getElementById('calendar');
    if (!calendarEl) return;
    
    calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,timeGridDay'
        },
        events: [],
        eventClick: function(info) {
            abrirModalEditarMantenimiento(info.event.extendedProps.id_mantenimiento);
        },
        eventColor: '#10b981'
    });
    
    calendar.render();
}

function cargarDatosCalendario() {
    if (!calendar) return;
    
    fetch('api/mantenimientos.php?calendario=1')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const events = data.mantenimientos.map(m => ({
                    title: m.equipo + ' - ' + m.tipo_mantenimiento,
                    start: m.fecha_inicio,
                    end: m.fecha_fin_prevista || m.fecha_inicio,
                    color: m.tipo_mantenimiento === 'predictivo' ? '#3b82f6' :
                           m.tipo_mantenimiento === 'preventivo' ? '#10b981' : '#ef4444',
                    extendedProps: {
                        id_mantenimiento: m.id_mantenimiento
                    }
                }));
                
                calendar.removeAllEvents();
                calendar.addEventSource(events);
            }
        })
        .catch(error => {
            console.error('Error al cargar calendario:', error);
        });
}

// ========== CHATBOT ==========

function toggleChatbot() {
    const window = document.getElementById('chatbot-window');
    window.classList.toggle('active');
}

function enviarMensaje() {
    const input = document.getElementById('chat-input');
    const message = input.value.trim();
    
    if (!message) return;
    
    // Agregar mensaje del usuario
    agregarMensajeChat(message, 'user');
    input.value = '';
    
    // Simular respuesta del bot
    setTimeout(() => {
        const respuesta = generarRespuestaBot(message);
        agregarMensajeChat(respuesta, 'bot');
    }, 500);
}

function agregarMensajeChat(mensaje, tipo) {
    const messagesDiv = document.getElementById('chat-messages');
    const messageDiv = document.createElement('div');
    messageDiv.className = `chat-message ${tipo}`;
    
    const bubble = document.createElement('div');
    bubble.className = `message-bubble ${tipo}`;
    bubble.textContent = mensaje;
    
    messageDiv.appendChild(bubble);
    messagesDiv.appendChild(messageDiv);
    messagesDiv.scrollTop = messagesDiv.scrollHeight;
}

function generarRespuestaBot(mensaje) {
    const msg = mensaje.toLowerCase();
    
    if (msg.includes('hola') || msg.includes('buenos días') || msg.includes('buenas tardes')) {
        return '¡Hola! Soy el asistente virtual de NIBARRA. ¿En qué puedo ayudarte?';
    }
    
    if (msg.includes('equipo') || msg.includes('equipos')) {
        return 'Puedo ayudarte con la gestión de equipos. Puedes crear, editar o eliminar equipos desde la pestaña "Tabla Equipos".';
    }
    
    if (msg.includes('mantenimiento') || msg.includes('mantenimientos')) {
        return 'Los mantenimientos se pueden gestionar desde la pestaña "Mantenimiento". Puedes crear nuevos mantenimientos y ver su progreso.';
    }
    
    if (msg.includes('calendario')) {
        return 'El calendario muestra todos los mantenimientos programados. Puedes verlos por mes, semana o día.';
    }
    
    if (msg.includes('ayuda') || msg.includes('help')) {
        return 'Puedo ayudarte con:\n- Gestión de equipos\n- Mantenimientos\n- Calendario\n- Trabajos realizados\n\n¿Sobre qué tema necesitas ayuda?';
    }
    
    return 'Entiendo tu consulta. ¿Podrías ser más específico? Puedo ayudarte con equipos, mantenimientos, calendario o trabajos realizados.';
}

// Función para mostrar notificaciones
function mostrarNotificacion(tipo, mensaje) {
    // Remover notificaciones existentes
    const notificacionesExistentes = document.querySelectorAll('.notificacion-toast');
    notificacionesExistentes.forEach(n => n.remove());
    
    const notificacion = document.createElement('div');
    notificacion.className = `notificacion-toast fixed top-20 right-6 z-50 p-4 rounded-lg shadow-lg max-w-md transform transition-all duration-300 translate-x-full`;
    
    const iconos = {
        success: '<i class="fa-solid fa-check-circle"></i>',
        error: '<i class="fa-solid fa-exclamation-circle"></i>',
        warning: '<i class="fa-solid fa-exclamation-triangle"></i>',
        info: '<i class="fa-solid fa-info-circle"></i>'
    };
    
    const colores = {
        success: 'bg-green-500 text-white',
        error: 'bg-red-500 text-white',
        warning: 'bg-yellow-500 text-white',
        info: 'bg-blue-500 text-white'
    };
    
    notificacion.className += ` ${colores[tipo]}`;
    notificacion.innerHTML = `
        <div class="flex items-center gap-3">
            <div class="text-xl">${iconos[tipo] || iconos.info}</div>
            <div class="flex-1">${mensaje}</div>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>
    `;
    
    document.body.appendChild(notificacion);
    
    // Animar entrada
    setTimeout(() => {
        notificacion.classList.remove('translate-x-full');
    }, 10);
    
    // Auto-remover después de 5 segundos
    setTimeout(() => {
        notificacion.classList.add('translate-x-full');
        setTimeout(() => notificacion.remove(), 300);
    }, 5000);
}

// Permitir Enter para enviar mensaje
document.addEventListener('DOMContentLoaded', function() {
    const chatInput = document.getElementById('chat-input');
    if (chatInput) {
        chatInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                enviarMensaje();
            }
        });
    }
    
    // Agregar animaciones suaves a los modales
    const modales = document.querySelectorAll('[id^="modal-"]');
    modales.forEach(modal => {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                // Cerrar modal al hacer click fuera
                const modalId = modal.id;
                if (modalId === 'modal-equipo') {
                    cerrarModalEquipo();
                } else if (modalId === 'modal-mantenimiento') {
                    cerrarModalMantenimiento();
                }
            }
        });
    });
    
    // Agregar animación de entrada a los modales
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                const target = mutation.target;
                if (target.classList.contains('flex') && !target.classList.contains('animate-in')) {
                    target.classList.add('animate-in');
                    setTimeout(() => target.classList.remove('animate-in'), 300);
                }
            }
        });
    });
    
    modales.forEach(modal => {
        observer.observe(modal, { attributes: true });
    });
});

