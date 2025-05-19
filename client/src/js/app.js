// Este archivo controla la página principal del sistema de asistencia y guardias.
// Aquí se gestionan los botones de inicio/fin de jornada y la carga del horario del profesor.

document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el usuario está autenticado
    if (!CONFIG.isAuthenticated()) {
        window.location.href = 'login.php';
        return;
    }

    // Obtener elementos del DOM
    const btnInicio = document.getElementById('btnInicio');
    const btnFin = document.getElementById('btnFin');
    const userName = document.getElementById('userName');
    const logoutForm = document.getElementById('logoutForm');
    const alertContainer = document.getElementById('alertContainer');

    // Mostrar el nombre del usuario y manejar visibilidad del nav
    const user = CONFIG.getUser();
    if (user) {
        userName.textContent = user.nombre_completo || user.dni;
        CONFIG.handleNavVisibility();
        
        // Mostrar/ocultar elementos según el rol
        const adminElements = document.querySelectorAll('.admin-only');
        adminElements.forEach(element => {
            element.style.display = user.rol === 'admin' ? 'block' : 'none';
        });
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

    // Función para verificar el estado actual de la jornada
    async function verificarEstadoJornada() {
        try {
            const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.ESTADO_JORNADA}`);
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
            }
        } catch (error) {
            console.error('Error al verificar estado:', error);
            mostrarAlerta(CONFIG.MENSAJES.ERROR_CONEXION);
        }
    }

    // Verificar estado inicial
    verificarEstadoJornada();

    // Cuando se pulsa el botón de inicio de jornada
    if (btnInicio) {
        btnInicio.addEventListener('click', async function(e) {
            e.preventDefault();
            if (btnInicio.disabled) {
                mostrarAlerta('Ya hay una jornada iniciada');
                return;
            }

            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.INICIO_JORNADA}`);
                const data = await response.json();
                
                mostrarAlerta(data.mensaje, data.success ? 'success' : 'error');
                if (data.success) {
                    verificarEstadoJornada();
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(CONFIG.MENSAJES.ERROR_CONEXION);
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
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.FIN_JORNADA}`);
                const data = await response.json();
                
                mostrarAlerta(data.mensaje, data.success ? 'success' : 'error');
                if (data.success) {
                    verificarEstadoJornada();
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(CONFIG.MENSAJES.ERROR_CONEXION);
            }
        });
    }

    // Manejar el logout
    if (logoutForm) {
        logoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.LOGOUT}`);
                const data = await response.json();
                
                if (data.success) {
                    // Limpiar el localStorage
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    
                    // Redirigir al login
                    window.location.href = 'login.php';
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(CONFIG.MENSAJES.ERROR_CONEXION);
            }
        });
    }

    // Cargar el horario del profesor
    cargarHorario();
});

// Esta función pide al servidor el horario del profesor y lo muestra en la tabla
async function cargarHorario() {
    try {
        const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.HORARIOS}`);
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