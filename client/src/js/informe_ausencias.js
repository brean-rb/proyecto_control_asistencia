// Este archivo controla la página de generación de informes de ausencias.
// Permite generar un informe de ausencias por docente o por fecha y muestra los resultados en una tabla.

document.addEventListener('DOMContentLoaded', function() {
    try {
        const form = document.getElementById('formInformeAusencias');
        const btnGenerar = document.getElementById('btnGenerar');
        const btnCancelar = document.getElementById('btnCancelar');
        const fechaInicioInput = document.getElementById('fecha_inicio');
        const fechaFinInput = document.getElementById('fecha_fin');

        if (!form) {
            console.warn('Formulario no encontrado');
            return;
        }

        if (!fechaInicioInput || !fechaFinInput) {
            console.warn('Campos de fecha no encontrados');
            return;
        }

        // Establecer fechas predeterminadas
        const hoy = new Date();
        const inicioMes = new Date(hoy.getFullYear(), hoy.getMonth(), 1);
        const finMes = new Date(hoy.getFullYear(), hoy.getMonth() + 1, 0);

        fechaInicioInput.value = inicioMes.toISOString().split('T')[0];
        fechaFinInput.value = finMes.toISOString().split('T')[0];

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
            const fechaInicio = new Date(fechaInicioInput.value);
            const fechaFin = new Date(fechaFinInput.value);

            if (fechaInicio > fechaFin) {
                mostrarAlerta('La fecha de inicio debe ser anterior a la fecha de fin');
                return false;
            }

            return true;
        }

        // Función para generar el informe
        async function generarInforme() {
            const datos = {
                fecha_inicio: fechaInicioInput.value,
                fecha_fin: fechaFinInput.value
            };

            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.INFORME}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datos)
                });

                const data = await response.json();
                
                if (data.success) {
                    // Crear un enlace para descargar el informe
                    const link = document.createElement('a');
                    link.href = data.url;
                    link.download = 'informe_ausencias.pdf';
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                } else {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            }
        }

        // Evento submit del formulario
        form.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            if (!validarFormulario()) {
                return;
            }

            if (btnGenerar) {
                btnGenerar.disabled = true;
                btnGenerar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...';
            }

            try {
                await generarInforme();
            } catch (error) {
                console.error('Error:', error);
                mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
            } finally {
                if (btnGenerar) {
                    btnGenerar.disabled = false;
                    btnGenerar.innerHTML = 'Generar Informe';
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
        console.warn('Error en la inicialización de informe_ausencias:', error);
    }
});