document.addEventListener('DOMContentLoaded', () => {
    const formConsulta = document.getElementById('form-consulta');
    const tipoConsulta = document.getElementById('tipo-consulta');
    const campoDocente = document.getElementById('campo-docente');
    const tipoFecha = document.getElementById('tipo-fecha');
    const campoFecha = document.getElementById('campo-fecha');
    const campoMes = document.getElementById('campo-mes');
    const documento = document.getElementById('documento');

    // Mostrar/ocultar campo de docente según el tipo de consulta
    tipoConsulta.addEventListener('change', () => {
        if (tipoConsulta.value === 'docente') {
            campoDocente.style.display = 'block';
            documento.required = true;
        } else {
            campoDocente.style.display = 'none';
            documento.required = false;
        }
    });

    // Mostrar/ocultar campos de fecha según el tipo
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

    // Manejar envío del formulario
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
});