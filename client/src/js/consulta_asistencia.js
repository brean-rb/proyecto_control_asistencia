// Este archivo controla la página de consulta de asistencias.
// Aquí se gestiona el formulario para buscar asistencias por docente, día o mes, y se muestran los resultados.

document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('formConsultaAsistencia');
    const btnBuscar = document.getElementById('btnBuscar');
    const btnCancelar = document.getElementById('btnCancelar');
    const fechaInput = document.getElementById('fecha');
    const tablaAsistencia = document.getElementById('tablaAsistencia').getElementsByTagName('tbody')[0];

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
            mostrarAlerta('No se pueden consultar asistencias para fechas pasadas');
            return false;
        }

        return true;
    }

    // Función para obtener la asistencia
    function obtenerAsistencia() {
        const fecha = fechaInput.value;
        
        return fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.ASISTENCIA}&fecha=${fecha}`)
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    throw new Error(data.message || CONFIG.MENSAJES.ERROR_SERVIDOR);
                }
                return data.asistencia;
            });
    }

    // Función para mostrar la asistencia en la tabla
    function mostrarAsistencia(asistencia) {
        tablaAsistencia.innerHTML = '';

        if (asistencia.length === 0) {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td colspan="5" class="text-center">
                    No hay registros de asistencia para la fecha seleccionada
                </td>
            `;
            tablaAsistencia.appendChild(row);
            return;
        }

        asistencia.forEach(registro => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${registro.nombre}</td>
                <td>${registro.hora_inicio}</td>
                <td>${registro.hora_fin}</td>
                <td>${registro.estado}</td>
                <td>${registro.observaciones || '-'}</td>
            `;
            tablaAsistencia.appendChild(row);
        });
    }

    // Evento submit del formulario
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        if (!validarFormulario()) {
            return;
        }

        btnBuscar.disabled = true;
        btnBuscar.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Buscando...';

        try {
            const asistencia = await obtenerAsistencia();
            mostrarAsistencia(asistencia);
        } catch (error) {
            console.error('Error:', error);
            mostrarAlerta(error.message || CONFIG.MENSAJES.ERROR_CONEXION);
        } finally {
            btnBuscar.disabled = false;
            btnBuscar.innerHTML = 'Buscar';
        }
    });

    // Evento click del botón cancelar
    btnCancelar.addEventListener('click', function() {
        window.location.href = 'index.php';
    });
});