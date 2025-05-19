// Este archivo controla la página principal del sistema de asistencia y guardias.
// Aquí se gestionan los botones de inicio/fin de jornada y la carga del horario del profesor.

document.addEventListener('DOMContentLoaded', function() {
    // Obtener elementos del DOM
    const btnInicio = document.getElementById('btnInicio');
    const btnFin = document.getElementById('btnFin');
    const userName = document.getElementById('userName');
    const alertContainer = document.getElementById('alertContainer');
    const modalInicioJornada = new bootstrap.Modal(document.getElementById('modalInicioJornada'));
    const confirmarInicio = document.getElementById('confirmarInicio');
    const horaActual = document.getElementById('horaActual');

    // Mostrar el nombre del usuario
    const user = CONFIG.getUser();
    if (user) {
        userName.textContent = user.nombre_completo || user.dni;
    }

    // Función para mostrar alertas
    function mostrarAlerta(mensaje, tipo = 'error') {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${tipo === 'error' ? 'danger' : 'success'} alert-dismissible fade show`;
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        alertContainer.appendChild(alertDiv);
        
        // Auto-cerrar después de 5 segundos
        setTimeout(() => {
            alertDiv.remove();
        }, 5000);
    }

    // Función para actualizar la hora actual
    function actualizarHoraActual() {
        const ahora = new Date();
        horaActual.textContent = ahora.toLocaleTimeString();
    }

    // Actualizar la hora cada segundo
    setInterval(actualizarHoraActual, 1000);
    actualizarHoraActual();

    // Función para verificar el estado actual de la jornada
    async function verificarEstadoJornada() {
        try {
            const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=estado_jornada`);
            const data = await response.json();
            
            if (data.success) {
                // Actualizar estado de los botones según la respuesta
                if (data.jornada_iniciada) {
                    btnInicio.disabled = true;
                    btnInicio.classList.add('disabled');
                    btnFin.disabled = false;
                    btnFin.classList.remove('disabled');
                } else {
                    btnInicio.disabled = false;
                    btnInicio.classList.remove('disabled');
                    btnFin.disabled = true;
                    btnFin.classList.add('disabled');
                }
            } else {
                throw new Error(data.mensaje || 'Error al verificar el estado de la jornada');
            }
        } catch (error) {
            console.error('Error al verificar estado:', error);
            mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
        }
    }

    // Verificar estado inicial
    verificarEstadoJornada();

    // Cuando se pulsa el botón de inicio de jornada
    if (btnInicio) {
        btnInicio.addEventListener('click', function(e) {
            e.preventDefault();
            if (btnInicio.disabled) {
                mostrarAlerta('Ya hay una jornada iniciada');
                return;
            }
            modalInicioJornada.show();
        });
    }

    // Cuando se confirma el inicio de jornada
    if (confirmarInicio) {
        confirmarInicio.addEventListener('click', async function() {
            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=inicio`);
                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta(data.mensaje, 'success');
                    modalInicioJornada.hide();
                    await verificarEstadoJornada();
                } else {
                    throw new Error(data.mensaje || 'Error al iniciar la jornada');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            }
        });
    }

    // Cuando se pulsa el botón de fin de jornada
    if (btnFin) {
        btnFin.addEventListener('click', async function(e) {
            e.preventDefault();
            if (btnFin.disabled) {
                mostrarAlerta('No hay ninguna jornada iniciada');
                return;
            }

            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=fin`);
                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta(data.mensaje, 'success');
                    await verificarEstadoJornada();
                } else {
                    throw new Error(data.mensaje || 'Error al finalizar la jornada');
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            }
        });
    }

    // Cargar el horario del profesor
    cargarHorario();
});

// Esta función pide al servidor el horario del profesor y lo muestra en la tabla
async function cargarHorario() {
    try {
        const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=horarios`);
        const data = await response.json();
        
        if (data.success) {
            const tbody = document.getElementById('tablaHorario');
            tbody.innerHTML = '';

            if (data.horario.length === 0) {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="6" class="text-center">No hay horario disponible</td>
                    </tr>`;
                return;
            }

            // Diccionario para traducir la letra del día a su nombre
            const diasSemana = {
                'L': 'Lunes',
                'M': 'Martes',
                'X': 'Miércoles',
                'J': 'Jueves',
                'V': 'Viernes'
            };

            data.horario.forEach(clase => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${diasSemana[clase.dia_setmana] || clase.dia_setmana}</td>
                    <td>${clase.hora_desde}</td>
                    <td>${clase.hora_fins}</td>
                    <td>${clase.asignatura || 'No disponible'}</td>
                    <td>${clase.grupo}</td>
                    <td>${clase.aula}</td>
                `;
                tbody.appendChild(tr);
            });
        } else {
            throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
        }
    } catch (error) {
        console.error('Error:', error);
        const tbody = document.getElementById('tablaHorario');
        tbody.innerHTML = `
            <tr>
                <td colspan="6" class="text-center text-danger">
                    Error al cargar el horario: ${error.message}
                </td>
            </tr>`;
    }
} 