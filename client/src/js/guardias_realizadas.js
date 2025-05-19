// Este archivo controla la página donde se consultan las guardias ya realizadas.
// Permite filtrar por fecha y hora, y muestra los resultados en una tabla.

document.addEventListener('DOMContentLoaded', function() {
    try {
        const form = document.getElementById('formGuardiasRealizadas');
        const tablaGuardias = document.getElementById('tablaGuardias');
        const btnBuscar = document.getElementById('btnBuscar');
        const btnCancelar = document.getElementById('btnCancelar');
        const fechaInput = document.getElementById('fecha');
        const horaInicioInput = document.getElementById('hora_inicio');
        const horaFinInput = document.getElementById('hora_fin');
        const observacionesInput = document.getElementById('observaciones');

        if (!form) {
            console.warn('Formulario no encontrado');
            return;
        }

        if (!tablaGuardias) {
            console.warn('Tabla de guardias no encontrada');
            return;
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
            if (!horaInicioInput || !horaFinInput) {
                mostrarAlerta('Faltan campos requeridos');
                return false;
            }

            const fecha = new Date(fechaInput.value);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            if (fecha < hoy) {
                mostrarAlerta('No se pueden registrar guardias para fechas pasadas');
                return false;
            }

            if (horaInicioInput.value >= horaFinInput.value) {
                mostrarAlerta('La hora de inicio debe ser anterior a la hora de fin');
                return false;
            }

            return true;
        }

        // Función para obtener las guardias disponibles
        function obtenerGuardiasDisponibles() {
            const fecha = fechaInput.value;
            
            return fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.GUARDIAS}&fecha=${fecha}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                    }
                    return data.guardias;
                });
        }

        // Función para registrar la guardia realizada
        function registrarGuardia(datos) {
            return fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.GUARDIAS_REALIZADAS}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(datos)
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
                return data;
            });
        }

        // Función para cargar las guardias realizadas
        async function cargarGuardiasRealizadas() {
            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.GUARDIAS_REALIZADAS}`);
                const data = await response.json();

                if (data.success) {
                    const tbody = tablaGuardias.getElementsByTagName('tbody')[0];
                    if (!tbody) {
                        console.warn('Elemento tbody no encontrado');
                        return;
                    }

                    tbody.innerHTML = '';
                    data.guardias.forEach(guardia => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${guardia.fecha}</td>
                            <td>${guardia.hora_inicio}</td>
                            <td>${guardia.hora_fin}</td>
                            <td>${guardia.profesor_ausente}</td>
                            <td>${guardia.asignatura}</td>
                            <td>${guardia.grupo}</td>
                            <td>${guardia.aula}</td>
                        `;
                        tbody.appendChild(tr);
                    });
                } else {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
            } catch (error) {
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            }
        }

        // Evento submit del formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return;
            }

            if (btnBuscar) {
                btnBuscar.disabled = true;
                btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
            }

            try {
                // Obtener las guardias disponibles
                const guardias = await obtenerGuardiasDisponibles();

                // Preparar los datos para el registro
                const datos = {
                    fecha: fechaInput.value,
                    hora_inicio: horaInicioInput.value,
                    hora_fin: horaFinInput.value,
                    observaciones: observacionesInput ? observacionesInput.value : '',
                    guardias: guardias
                };

                // Registrar la guardia
                const resultado = await registrarGuardia(datos);
                
                mostrarAlerta(resultado.message, 'success');
                form.reset();
                fechaInput.value = new Date().toISOString().split('T')[0];

                await cargarGuardiasRealizadas();
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            } finally {
                if (btnBuscar) {
                    btnBuscar.disabled = false;
                    btnBuscar.innerHTML = 'Guardar';
                }
            }
        });

        // Evento click del botón cancelar
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                window.location.href = 'index.php';
            });
        }

        // Cargar guardias al iniciar
        cargarGuardiasRealizadas();
    } catch (error) {
        console.warn('Error en la inicialización de guardias_realizadas:', error);
    }
}); 