// Este archivo controla la página de informe de ausencias.
// Permite generar un informe de ausencias por docente o por fecha y muestra los resultados en una tabla.

document.addEventListener('DOMContentLoaded', () => {
    // Referencias a los elementos del formulario y campos de filtro
    const formInforme = document.getElementById('form-informe');
    const tipoInforme = document.getElementById('tipo-informe');
    const campoDocente = document.getElementById('campo-docente');
    const campoFecha = document.getElementById('campo-fecha');
    const documento = document.getElementById('documento');
    const fecha = document.getElementById('fecha');

    // Esta función muestra u oculta los campos según el tipo de informe seleccionado
    function toggleCampos() {
        if (tipoInforme.value === 'docente') {
            campoDocente.style.display = 'block';
            campoFecha.style.display = 'none';
            documento.required = true;
            fecha.required = false;
        } else {
            campoDocente.style.display = 'none';
            campoFecha.style.display = 'block';
            documento.required = false;
            fecha.required = true;
        }
    }

    // Mostrar/ocultar campos según el tipo de informe
    tipoInforme.addEventListener('change', toggleCampos);

    // Ejecutar la función al cargar la página para mostrar los campos correctos
    toggleCampos();

    // --- CARGAR DOCENTES EN EL SELECT DE INFORME (igual que en consulta_asistencia.js) ---
    // Al cargar la página, pide la lista de docentes al servidor y los añade al desplegable
    if (documento && documento.tagName === 'SELECT') {
        fetch('../../server/listar_docentes.php')
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    documento.innerHTML = '<option value="">Selecciona un docente...</option>';
                    data.docentes.forEach(docente => {
                        const option = document.createElement('option');
                        option.value = docente.document;
                        option.textContent = docente.nombre;
                        documento.appendChild(option);
                    });
                }
            });
    }

    // Cuando se envía el formulario, pide los datos del informe al servidor y muestra los resultados
    formInforme.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const formData = new FormData(formInforme);
        try {
            const response = await fetch('../../server/generar_informe.php', {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                mostrarResultados(data.ausencias);
            } else {
                alert('Error al generar el informe: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al generar el informe');
        }
    });

    // Esta función muestra en la tabla los resultados del informe de ausencias
    function mostrarResultados(ausencias) {
        const tbody = document.querySelector('#tabla-ausencias tbody');
        tbody.innerHTML = '';

        ausencias.forEach(ausencia => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${ausencia.nombre}</td>
                <td>${ausencia.fecha_inicio}</td>
                <td>${ausencia.fecha_fin}</td>
                <td>${ausencia.motivo}</td>
                <td>
                    <span class="badge ${ausencia.justificada ? 'bg-success' : 'bg-danger'}">
                        ${ausencia.justificada ? 'Sí' : 'No'}
                    </span>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
});