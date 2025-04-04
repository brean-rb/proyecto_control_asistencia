document.addEventListener('DOMContentLoaded', function() {
    // Prevenir que el enlace redirija al hacer clic
    document.querySelector('#adminDropdown')?.addEventListener('click', function(e) {
        e.preventDefault();
    });

    // Mejorar la experiencia en dispositivos táctiles
    if('ontouchstart' in document.documentElement) {
        document.querySelector('#adminDropdown')?.addEventListener('click', function() {
            this.parentElement.classList.toggle('show');
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });
    }

    // Cargar horario
    cargarHorario();
});

function cargarHorario() {
    fetch('/control_asistencia_y_gestion_guardias/server/horarios.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta: ' + response.status);
            }
            return response.json();
        })
        .then(data => {
            const tbody = document.getElementById('tablaHorario');
            tbody.innerHTML = '';

            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay horario disponible</td></tr>';
                return;
            }

            const diasSemana = {
                'L': 'Lunes',
                'M': 'Martes',
                'X': 'Miércoles',
                'J': 'Jueves',
                'V': 'Viernes'
            };

            data.forEach(horario => {
                const tr = document.createElement('tr');
                tr.innerHTML = `
                    <td>${diasSemana[horario.dia_setmana] || horario.dia_setmana}</td>
                    <td>${horario.hora_desde}</td>
                    <td>${horario.hora_fins}</td>
                    <td>${horario.asignatura || 'No disponible'}</td>
                    <td>${horario.grupo || 'No disponible'}</td>
                    <td>${horario.aula || 'No disponible'}</td>
                `;
                tbody.appendChild(tr);
            });
        })
        .catch(error => {
            const tbody = document.getElementById('tablaHorario');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">
                Error al cargar el horario: ${error.message}
            </td></tr>`;
        });
}