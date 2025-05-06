document.addEventListener('DOMContentLoaded', () => {
    cargarProfesoresAusentes();

    async function cargarProfesoresAusentes() {
        try {
            const response = await fetch('../../server/consultar_guardias.php');
            const data = await response.json();

            if (data.success) {
                mostrarProfesoresAusentes(data.profesores_ausentes);
            } else {
                alert('Error al cargar los profesores ausentes: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar los profesores ausentes');
        }
    }

    async function reservarGuardia(e) {
        const btn = e.target;
        const formData = new FormData();
        formData.append('fecha', new Date().toISOString().split('T')[0]);
        formData.append('docente_ausente', btn.dataset.docente);
        formData.append('hora_inicio', btn.dataset.horaInicio);
        formData.append('hora_fin', btn.dataset.horaFin);
        formData.append('grupo', btn.dataset.grupo);
        formData.append('aula', btn.dataset.aula);
        formData.append('contenido', btn.dataset.contenido);
        formData.append('docente_guardia', document.getElementById('docente_actual').value);

        try {
            const response = await fetch('../../server/registrar_guardia.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                mostrarModalExito();
                btn.disabled = true;
                btn.textContent = 'Reservada';
                btn.classList.replace('btn-success', 'btn-secondary');
            } else {
                alert('Error al registrar la guardia: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al registrar la guardia');
        }
    }

    function mostrarGuardias(guardias) {
        const tbody = document.querySelector('#tabla-guardias tbody');
        tbody.innerHTML = '';

        if (guardias.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">No hay guardias pendientes para hoy</td>
                </tr>`;
            return;
        }

        guardias.forEach(guardia => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${guardia.docente_ausente}</td>
                <td>${guardia.hora_inicio}</td>
                <td>${guardia.hora_fin}</td>
                <td>${guardia.grupo}</td>
                <td>${guardia.aula}</td>
                <td>
                    <button class="btn btn-success btn-sm reservar-guardia" 
                            data-docente="${guardia.docente_ausente}"
                            data-hora-inicio="${guardia.hora_inicio}" 
                            data-hora-fin="${guardia.hora_fin}" 
                            data-grupo="${guardia.grupo}" 
                            data-aula="${guardia.aula}">
                        Reservar
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        document.querySelectorAll('.reservar-guardia').forEach(button => {
            button.addEventListener('click', reservarGuardia);
        });
    }

    function mostrarProfesoresAusentes(profesores) {
        const tbody = document.querySelector('#tabla-profesores-ausentes tbody');
        tbody.innerHTML = '';

        if (profesores.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="4" class="text-center">No hay profesores ausentes hoy</td>
                </tr>`;
            return;
        }

        profesores.forEach(profesor => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${profesor.nombre}</td>
                <td>${profesor.fecha_inicio}</td>
                <td>${profesor.fecha_fin}</td>
                <td>
                    <button class="btn btn-danger ver-horario" 
                            data-documento="${profesor.documento}">
                        Ver Horario
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Añadir eventos a los botones
        document.querySelectorAll('.ver-horario').forEach(btn => {
            btn.addEventListener('click', mostrarHorarioProfesor);
        });
    }

    async function mostrarHorarioProfesor(e) {
        const documento = e.target.dataset.documento;
        const formData = new FormData();
        formData.append('documento', documento);

        try {
            const response = await fetch('../../server/obtener_horario_ausente.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                mostrarHorario(data.horario, documento);
            } else {
                alert('Error al cargar el horario: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al cargar el horario');
        }
    }

    function mostrarHorario(horario, docente_ausente) {
        const tbody = document.querySelector('#tabla-horario tbody');
        tbody.innerHTML = '';

        if (horario.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="6" class="text-center">No hay clases disponibles para hoy</td>
                </tr>`;
            return;
        }

        horario.forEach(clase => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${clase.hora_inicio} - ${clase.hora_fin}</td>
                <td>${clase.asignatura}</td>
                <td>${clase.grupo}</td>
                <td>${clase.aula}</td>
                <td>
                    ${clase.reservada ? 
                        `<button class="btn btn-secondary btn-sm" disabled>
                            Reservada
                        </button>` :
                        `<button class="btn btn-success btn-sm reservar-guardia"
                            data-docente="${docente_ausente}"
                            data-hora-inicio="${clase.hora_inicio}"
                            data-hora-fin="${clase.hora_fin}"
                            data-grupo="${clase.grupo}"
                            data-aula="${clase.aula}"
                            data-contenido="${clase.asignatura}"
                            data-sesion="${clase.sesion_orden}">
                            Reservar Guardia
                        </button>`
                    }
                </td>
            `;
            tbody.appendChild(row);
        });

        document.getElementById('horario-container').style.display = 'block';
        
        // Añadir eventos solo a los botones no deshabilitados
        document.querySelectorAll('.reservar-guardia:not([disabled])').forEach(btn => {
            btn.addEventListener('click', reservarGuardia);
        });
    }

    // Función para mostrar el modal de éxito y recargar al cerrarse
    function mostrarModalExito() {
        const modal = new bootstrap.Modal(document.getElementById('successModal'));
        modal.show();
        document.getElementById('successModal').addEventListener('hidden.bs.modal', function handler() {
            location.reload();
            document.getElementById('successModal').removeEventListener('hidden.bs.modal', handler);
        });
    }
});