// Este archivo controla la página de informe de ausencias.
// Permite generar un informe de ausencias por docente o por fecha y muestra los resultados en una tabla.

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formInformeAusencias');
    const btnGenerar = document.getElementById('btnGenerar');
    const btnCancelar = document.getElementById('btnCancelar');
    const fechaInicioInput = document.getElementById('fecha_inicio');
    const fechaFinInput = document.getElementById('fecha_fin');

    // Establecer fechas predeterminadas (último mes)
    const hoy = new Date();
    const mesAnterior = new Date(hoy.getFullYear(), hoy.getMonth() - 1, 1);
    fechaInicioInput.value = mesAnterior.toISOString().split('T')[0];
    fechaFinInput.value = hoy.toISOString().split('T')[0];

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
    function generarInforme(datos) {
        return fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.INFORME}`, {
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

        btnGenerar.disabled = true;
        btnGenerar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Generando...';

        try {
            // Preparar los datos para el informe
            const datos = {
                fecha_inicio: fechaInicioInput.value,
                fecha_fin: fechaFinInput.value
            };

            // Generar el informe
            const resultado = await generarInforme(datos);
            
            // Mostrar el informe en una nueva ventana
            const ventanaInforme = window.open('', '_blank');
            ventanaInforme.document.write(`
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Informe de Ausencias</title>
                    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
                    <style>
                        body { padding: 20px; }
                        .table { margin-top: 20px; }
                    </style>
                </head>
                <body>
                    <h1>Informe de Ausencias</h1>
                    <p>Período: ${fechaInicioInput.value} al ${fechaFinInput.value}</p>
                    ${resultado.html}
                    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
                </body>
                </html>
            `);
            ventanaInforme.document.close();

        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
        } finally {
            btnGenerar.disabled = false;
            btnGenerar.innerHTML = 'Generar Informe';
        }
    });

    // Evento click del botón cancelar
    btnCancelar.addEventListener('click', function() {
        window.location.href = 'index.php';
    });
});