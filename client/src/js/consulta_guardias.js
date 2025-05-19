// Este archivo controla la página de consulta y reserva de guardias.
// Aquí se muestran los profesores ausentes, sus horarios y permite reservar guardias de forma sencilla.

document.addEventListener('DOMContentLoaded', function() {
    try {
        const form = document.getElementById('formConsultaGuardias');
        const tablaGuardias = document.getElementById('tablaGuardias');
        const btnBuscar = document.getElementById('btnBuscar');
        const btnCancelar = document.getElementById('btnCancelar');
        const fechaInput = document.getElementById('fecha');

        // Verificar si estamos en la página correcta
        if (!form || !tablaGuardias) {
            return; // No estamos en la página de consulta de guardias
        }

        if (!fechaInput) {
            console.warn('Campo de fecha no encontrado');
            return;
        }

        // Establecer la fecha actual como valor predeterminado
        const hoy = new Date();
        fechaInput.value = hoy.toISOString().split('T')[0];

        // Función para mostrar alertas
        function mostrarAlerta(mensaje, tipo = 'error') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
            alertDiv.innerHTML = `
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            form.insertBefore(alertDiv, form.firstChild);
        }

        // Función para validar el formulario
        function validarFormulario() {
            const fecha = new Date(fechaInput.value);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            if (fecha < hoy) {
                mostrarAlerta('No se pueden consultar guardias para fechas pasadas');
                return false;
            }

            return true;
        }

        // Función para obtener las guardias
        async function obtenerGuardias() {
            const fecha = fechaInput.value;
            
            try {
                const response = await fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.GUARDIAS}&fecha=${fecha}`);
                const data = await response.json();

                if (!data.success) {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
                return data.guardias;
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
                return [];
            }
        }

        // Función para mostrar las guardias en la tabla
        function mostrarGuardias(guardias) {
            const tbody = tablaGuardias.getElementsByTagName('tbody')[0];
            if (!tbody) {
                console.warn('Elemento tbody no encontrado');
                return;
            }

            tbody.innerHTML = '';

            if (guardias.length === 0) {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td colspan="7" class="text-center">
                        No hay guardias disponibles para la fecha seleccionada
                    </td>
                `;
                tbody.appendChild(row);
                return;
            }

            guardias.forEach(guardia => {
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${guardia.hora_inicio}</td>
                    <td>${guardia.hora_fin}</td>
                    <td>${guardia.profesor_ausente}</td>
                    <td>${guardia.asignatura}</td>
                    <td>${guardia.grupo}</td>
                    <td>${guardia.aula}</td>
                    <td>
                        <button class="btn btn-primary btn-sm" onclick="realizarGuardia(${guardia.id})">
                            Realizar Guardia
                        </button>
                    </td>
                `;
                tbody.appendChild(row);
            });
        }

        // Evento submit del formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return;
            }

            if (btnBuscar) {
                btnBuscar.disabled = true;
                btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';
            }

            try {
                const guardias = await obtenerGuardias();
                mostrarGuardias(guardias);
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            } finally {
                if (btnBuscar) {
                    btnBuscar.disabled = false;
                    btnBuscar.innerHTML = 'Buscar';
                }
            }
        });

        // Evento click del botón cancelar
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                window.location.href = 'index.php';
            });
        }

        // Función para realizar una guardia
        window.realizarGuardia = async function(id) {
            try {
                const response = await fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.GUARDIAS_REALIZADAS}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ id: id })
                });

                const data = await response.json();
                
                if (data.success) {
                    mostrarAlerta(data.message, 'success');
                    // Recargar la lista de guardias
                    const guardias = await obtenerGuardias();
                    mostrarGuardias(guardias);
                } else {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            }
        };

        // Cargar guardias al iniciar
        obtenerGuardias().then(guardias => mostrarGuardias(guardias));
    } catch (error) {
        console.warn('Error en la inicialización de consulta_guardias:', error);
    }
});