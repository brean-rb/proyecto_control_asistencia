// Este archivo controla la página donde se consultan las guardias ya realizadas.
// Permite filtrar por fecha y hora, y muestra los resultados en una tabla.

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formGuardiasRealizadas');
    const btnGuardar = document.getElementById('btnGuardar');
    const btnCancelar = document.getElementById('btnCancelar');
    const fechaInput = document.getElementById('fecha');
    const horaInicioInput = document.getElementById('hora_inicio');
    const horaFinInput = document.getElementById('hora_fin');
    const observacionesInput = document.getElementById('observaciones');

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

    // Evento submit del formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return;
        }

        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Guardando...';

        try {
            // Obtener las guardias disponibles
            const guardias = await obtenerGuardiasDisponibles();

            // Preparar los datos para el registro
            const datos = {
                fecha: fechaInput.value,
                hora_inicio: horaInicioInput.value,
                hora_fin: horaFinInput.value,
                observaciones: observacionesInput.value,
                guardias: guardias
            };

            // Registrar la guardia
            const resultado = await registrarGuardia(datos);
            
            mostrarAlerta(resultado.message, 'success');
            form.reset();
            fechaInput.value = new Date().toISOString().split('T')[0];

        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = 'Guardar';
        }
    });

    // Evento click del botón cancelar
    btnCancelar.addEventListener('click', function() {
        window.location.href = 'index.php';
    });
}); 