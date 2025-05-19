// Este archivo controla la página de registro de ausencias de docentes.
// Permite registrar una ausencia para un día o un periodo, seleccionar horas concretas y ver el horario del profesor.

document.addEventListener('DOMContentLoaded', function() {
    try {
        const form = document.getElementById('formRegistroAusencia');
        const btnGuardar = document.getElementById('btnGuardar');
        const btnCancelar = document.getElementById('btnCancelar');
        const fechaInput = document.getElementById('fecha');
        const horaInicioInput = document.getElementById('hora_inicio');
        const horaFinInput = document.getElementById('hora_fin');
        const motivoInput = document.getElementById('motivo');
        const observacionesInput = document.getElementById('observaciones');

        if (!form) {
            console.warn('Formulario no encontrado');
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
            if (!fechaInput || !horaInicioInput || !horaFinInput || !motivoInput) {
                mostrarAlerta('Faltan campos requeridos');
                return false;
            }

            const fecha = new Date(fechaInput.value);
            const hoy = new Date();
            hoy.setHours(0, 0, 0, 0);

            if (fecha < hoy) {
                mostrarAlerta('No se pueden registrar ausencias para fechas pasadas');
                return false;
            }

            if (horaInicioInput.value >= horaFinInput.value) {
                mostrarAlerta('La hora de inicio debe ser anterior a la hora de fin');
                return false;
            }

            if (!motivoInput.value.trim()) {
                mostrarAlerta('Debe especificar un motivo');
                return false;
            }

            return true;
        }

        // Función para verificar si ya existe una ausencia registrada
        function verificarAusenciaExistente() {
            const fecha = fechaInput.value;
            
            return fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.HORARIO_AUSENTE}&fecha=${fecha}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.horario && data.horario.length > 0) {
                        throw new Error('Ya existe una ausencia registrada para esta fecha');
                    }
                    return true;
                });
        }

        // Función para obtener el horario del profesor
        function obtenerHorarioProfesor() {
            const fecha = fechaInput.value;
            
            return fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.HORARIO_PROFESOR}&fecha=${fecha}`)
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                    }
                    return data.horario;
                });
        }

        // Función para registrar la ausencia
        function registrarAusencia(datos) {
            return fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.AUSENCIA}`, {
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

        // Evento submit del formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return;
            }

            if (btnGuardar) {
                btnGuardar.disabled = true;
                btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';
            }

            try {
                // Verificar si ya existe una ausencia
                await verificarAusenciaExistente();

                // Obtener el horario del profesor
                const horario = await obtenerHorarioProfesor();

                // Preparar los datos para el registro
                const datos = {
                    fecha: fechaInput.value,
                    hora_inicio: horaInicioInput.value,
                    hora_fin: horaFinInput.value,
                    motivo: motivoInput.value,
                    observaciones: observacionesInput.value,
                    horario: horario
                };

                // Registrar la ausencia
                const resultado = await registrarAusencia(datos);
                
                mostrarAlerta(resultado.message, 'success');
                form.reset();
                fechaInput.value = new Date().toISOString().split('T')[0];

            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            } finally {
                if (btnGuardar) {
                    btnGuardar.disabled = false;
                    btnGuardar.innerHTML = 'Guardar';
                }
            }
        });

        // Evento click del botón cancelar
        if (btnCancelar) {
            btnCancelar.addEventListener('click', function() {
                window.location.href = 'index.php';
            });
        }
    } catch (error) {
        console.warn('Error en la inicialización de registro_ausencia:', error);
    }
});