// Este archivo controla la página de consulta de asistencias.
// Aquí se gestiona el formulario para buscar asistencias por docente, día o mes, y se muestran los resultados.

document.addEventListener('DOMContentLoaded', () => {
    // Referencias a los elementos del formulario y campos de filtro
    const formConsulta = document.getElementById('form-consulta');
    const tipoConsulta = document.getElementById('tipo-consulta');
    const campoDocente = document.getElementById('campo-docente');
    const tipoFecha = document.getElementById('tipo-fecha');
    const campoFecha = document.getElementById('campo-fecha');
    const campoMes = document.getElementById('campo-mes');
    const selectDocente = document.getElementById('documento');

    // Cuando se cambia el tipo de consulta, muestra u oculta el campo de docente
    tipoConsulta.addEventListener('change', () => {
        if (tipoConsulta.value === 'docente') {
            campoDocente.style.display = 'block';
            selectDocente.required = true;
        } else {
            campoDocente.style.display = 'none';
            selectDocente.required = false;
        }
    });

    // Cuando se cambia el tipo de fecha, muestra el campo de día o de mes según la opción
    tipoFecha.addEventListener('change', () => {
        if (tipoFecha.value === 'dia') {
            campoFecha.style.display = 'block';
            campoMes.style.display = 'none';
            document.getElementById('fecha').required = true;
            document.getElementById('mes').required = false;
        } else {
            campoFecha.style.display = 'none';
            campoMes.style.display = 'block';
            document.getElementById('fecha').required = false;
            document.getElementById('mes').required = true;
        }
    });

    // Cuando se envía el formulario, pide los datos de asistencia al servidor y muestra los resultados
    formConsulta.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(formConsulta);
        try {
            const response = await fetch('../../server/consultar_asistencia.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarResultados(data.asistencias);
            } else {
                alert('Error al consultar las asistencias: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al realizar la consulta');
        }
    });

    // Esta función muestra en la tabla los resultados de la consulta de asistencias
    function mostrarResultados(asistencias) {
        const tbody = document.querySelector('#tabla-asistencias tbody');
        tbody.innerHTML = '';

        asistencias.forEach(asistencia => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${asistencia.nombre}</td>
                <td>${asistencia.fecha}</td>
                <td>${asistencia.hora_entrada || '-'}</td>
                <td>${asistencia.hora_salida || '-'}</td>
                <td>
                    <span class="badge ${asistencia.hora_entrada ? 'bg-success' : 'bg-danger'}">
                        ${asistencia.hora_entrada ? 'Presente' : 'Ausente'}
                    </span>
                </td>
            `;
            tbody.appendChild(row);
        });
    }

    // --- CARGAR DOCENTES EN EL SELECT DE CONSULTA ---
    // Al cargar la página, pide la lista de docentes al servidor y los añade al desplegable
    if (selectDocente && selectDocente.tagName === 'SELECT') {
        fetch('../../server/listar_docentes.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    data.docentes.forEach(docente => {
                        const option = document.createElement('option');
                        option.value = docente.document;
                        option.textContent = docente.nombre;
                        selectDocente.appendChild(option);
                    });
                }
        });
    }
});