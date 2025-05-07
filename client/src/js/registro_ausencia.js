// Este archivo controla la página de registro de ausencias de docentes.
// Permite registrar una ausencia para un día o un periodo, seleccionar horas concretas y ver el horario del profesor.

document.addEventListener('DOMContentLoaded', () => {
    // Referencias a los campos del formulario para mostrar/ocultar según el tipo de ausencia
    const campoMismoDia = document.getElementById('campo-mismo-dia');
    const campoPeriodo = document.getElementById('campo-periodo');
    const tipoRadios = document.querySelectorAll('input[name="tipo"]');

    // Esta función muestra los campos correctos según si la ausencia es de un día o de varios días
    function toggleCampos() {
        const tipo = document.querySelector('input[name="tipo"]:checked').value;

        if (tipo === 'dia') {
            campoMismoDia.style.display = 'block';
            campoPeriodo.style.display = 'none';
            document.getElementById('fecha').required = true;
            document.getElementById('fecha-inicio').required = false;
            document.getElementById('fecha-fin').required = false;
        } else {
            campoMismoDia.style.display = 'none';
            campoPeriodo.style.display = 'block';
            document.getElementById('fecha').required = false;
            document.getElementById('fecha-inicio').required = true;
            document.getElementById('fecha-fin').required = true;
        }
    }

    // Cuando se cambia el tipo de ausencia, actualiza los campos visibles
    tipoRadios.forEach(radio => radio.addEventListener('change', toggleCampos));
    toggleCampos();

    // Evento para cuando se selecciona una fecha: busca el horario del profesor para ese día
    document.getElementById('fecha').addEventListener('change', async function () {
        const documento = document.getElementById('documento').value;
        const fecha = this.value;

        if (documento && fecha) {
            const formData = new FormData();
            formData.append('documento', documento);
            formData.append('fecha', fecha);

            try {
                const response = await fetch('../../server/obtener_horario_profesor.php', {
                    method: 'POST',
                    body: formData
                });

                const data = await response.json();

                if (data.success) {
                    mostrarHorario(data.horario);
                } else {
                    console.error('Error:', data.message);
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }
    });

    // Esta función muestra el horario del profesor en una tabla y permite seleccionar horas concretas
    function mostrarHorario(horario) {
        const contenedor = document.getElementById('horario-profesor');
        contenedor.innerHTML = '';

        if (horario.length === 0) {
            contenedor.innerHTML = '<p class="text-muted">No hay clases para este día</p>';
            return;
        }

        const tabla = document.createElement('table');
        tabla.className = 'table table-hover mt-3';

        const thead = document.createElement('thead');
        thead.innerHTML = `
            <tr class="table-dark">
                <th>Seleccionar</th>
                <th>Horario</th>
                <th>Asignatura</th>
                <th>Grupo</th>
                <th>Aula</th>
            </tr>
        `;
        tabla.appendChild(thead);

        const tbody = document.createElement('tbody');
        horario.forEach(clase => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <input class="form-check-input" type="checkbox" 
                            name="horas_seleccionadas[]" 
                            value="${clase.hora_inicio}-${clase.hora_fin}"
                            data-inicio="${clase.hora_inicio}"
                            data-fin="${clase.hora_fin}">
                </td>
                <td>${clase.hora_inicio} - ${clase.hora_fin}</td>
                <td>${clase.asignatura || 'No disponible'}</td>
                <td>${clase.grupo}</td>
                <td>${clase.aula}</td>
            `;
            tbody.appendChild(tr);
        });
        tabla.appendChild(tbody);
        contenedor.appendChild(tabla);

        // Inputs ocultos para guardar la primera y última hora seleccionada
        const inputHoraInicio = document.createElement('input');
        inputHoraInicio.type = 'hidden';
        inputHoraInicio.name = 'hora_inicio';
        inputHoraInicio.id = 'hora_inicio';

        const inputHoraFin = document.createElement('input');
        inputHoraFin.type = 'hidden';
        inputHoraFin.name = 'hora_fin';
        inputHoraFin.id = 'hora_fin';

        contenedor.appendChild(inputHoraInicio);
        contenedor.appendChild(inputHoraFin);

        // Cuando se seleccionan/desmarcan horas, actualiza los valores de inicio y fin
        const checkboxes = contenedor.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            checkbox.addEventListener('change', actualizarHoras);
        });
    }

    // Esta función calcula la primera y última hora seleccionada y las guarda en los inputs ocultos
    function actualizarHoras() {
        const checkboxes = document.querySelectorAll('input[name="horas_seleccionadas[]"]:checked');
        let horaInicio = null;
        let horaFin = null;

        checkboxes.forEach(checkbox => {
            const inicio = checkbox.dataset.inicio;
            const fin = checkbox.dataset.fin;

            if (!horaInicio || inicio < horaInicio) horaInicio = inicio;
            if (!horaFin || fin > horaFin) horaFin = fin;
        });

        document.getElementById('hora_inicio').value = horaInicio || '';
        document.getElementById('hora_fin').value = horaFin || '';
    }

    // --- CARGAR DOCENTES EN EL SELECT ---
    const selectDocente = document.getElementById('select-docente');
    if (selectDocente) {
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

    // Referencias para buscar el horario al cambiar docente o fecha
    const inputFecha = document.getElementById('fecha');
    const horarioProfesor = document.getElementById('horario-profesor');

    // Esta función busca el horario del docente seleccionado para la fecha elegida
    async function buscarHorarioDocente() {
        const documento = selectDocente ? selectDocente.value : '';
        const fecha = inputFecha ? inputFecha.value : '';
        if (documento && fecha) {
            const formData = new FormData();
            formData.append('documento', documento);
            formData.append('fecha', fecha);
            try {
                const response = await fetch('../../server/obtener_horario_profesor.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await response.json();
                if (data.success) {
                    mostrarHorario(data.horario);
                } else {
                    horarioProfesor.innerHTML = '<p class="text-muted">No hay clases para este día</p>';
                }
            } catch (error) {
                horarioProfesor.innerHTML = '<p class="text-danger">Error al buscar el horario</p>';
            }
        } else {
            horarioProfesor.innerHTML = '';
        }
    }

    // Cuando se cambia el docente o la fecha, busca el horario automáticamente
    if (selectDocente && inputFecha) {
        selectDocente.addEventListener('change', buscarHorarioDocente);
        inputFecha.addEventListener('change', buscarHorarioDocente);
    }
});