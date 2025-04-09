document.addEventListener('DOMContentLoaded', () => {
    const formGuardias = document.getElementById('form-guardias');

    formGuardias.addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = new FormData(formGuardias);
        try {
            const response = await fetch('../../server/consultar_guardias.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                mostrarResultados(data.guardias);
            } else {
                alert('Error al consultar las guardias: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al realizar la consulta');
        }
    });

    function mostrarResultados(guardias) {
        const tbody = document.querySelector('#tabla-guardias tbody');
        tbody.innerHTML = '';

        guardias.forEach(guardia => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${guardia.docente_ausente}</td>
                <td>${guardia.hora_inicio}</td>
                <td>${guardia.hora_fin}</td>
                <td>${guardia.grupo}</td>
                <td>${guardia.aula}</td>
                <td>
                    <button class="btn btn-success btn-sm registrar-guardia" data-docente="${guardia.docente_ausente}" data-hora-inicio="${guardia.hora_inicio}" data-hora-fin="${guardia.hora_fin}" data-grupo="${guardia.grupo}" data-aula="${guardia.aula}">
                        Registrar Guardia
                    </button>
                </td>
            `;
            tbody.appendChild(row);
        });

        // Añadir eventos a los botones de registrar guardia
        document.querySelectorAll('.registrar-guardia').forEach(button => {
            button.addEventListener('click', registrarGuardia);
        });
    }

    async function registrarGuardia(e) {
        const button = e.target;
        const docenteGuardia = prompt('Introduce tu DNI para registrar la guardia:');
        const formData = new FormData();
        formData.append('fecha', document.getElementById('fecha').value);
        formData.append('hora_inicio', button.dataset.horaInicio);
        formData.append('hora_fin', button.dataset.horaFin);
        formData.append('docente_guardia', docenteGuardia);
        formData.append('docente_ausente', button.dataset.docente);
        formData.append('grupo', button.dataset.grupo);
        formData.append('aula', button.dataset.aula);

        try {
            const response = await fetch('../../server/registrar_guardia.php', {
                method: 'POST',
                body: formData
            });

            const data = await response.json();

            if (data.success) {
                alert('Guardia registrada correctamente');
                button.disabled = true;
            } else {
                alert('Error al registrar la guardia: ' + data.message);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error al registrar la guardia');
        }
    }
});