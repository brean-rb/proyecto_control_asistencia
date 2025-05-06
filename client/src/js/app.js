document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado');
    
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

    // Función simple para mostrar alerta
    function mostrarAlerta(mensaje) {
        const modal = new bootstrap.Modal(document.getElementById('alertModal'));
        document.getElementById('alertModalBody').innerHTML = mensaje;
        modal.show();
    }

    // Interceptar clicks en los botones de jornada
    const btnInicio = document.getElementById('btn-inicio-jornada');
    const btnFin = document.getElementById('btn-fin-jornada');
    
    console.log('Botones encontrados:', { btnInicio, btnFin });

    if (btnInicio) {
        btnInicio.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Click en inicio jornada');
            fetch('../../server/registrar_jornada.php?accion=inicio&format=json')
                .then(res => res.json())
                .then(data => {
                    mostrarAlerta(data.mensaje);
                    if (data.success) {
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(() => mostrarAlerta('Error al iniciar la jornada'));
        });
    }
    if (btnFin) {
        btnFin.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Click en fin jornada');
            fetch('../../server/registrar_jornada.php?accion=fin&format=json')
                .then(res => res.json())
                .then(data => {
                    mostrarAlerta(data.mensaje);
                    if (data.success) {
                        setTimeout(() => location.reload(), 1500);
                    }
                })
                .catch(() => mostrarAlerta('Error al finalizar la jornada'));
        });
    }

    // Cargar horario
    cargarHorario();
});

function cargarHorario() {
    fetch('../../server/horarios.php')
        .then(response => {
            console.log('Estado de la respuesta:', response.status);
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.text().then(text => {
                console.log('Respuesta recibida:', text);
                try {
                    return JSON.parse(text);
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                    throw new Error('La respuesta no es un JSON válido');
                }
            });
        })
        .then(data => {
            console.log('Datos recibidos:', data); // Verifica que aquí se imprimen los datos
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
            console.error('Error al procesar el horario:', error);
            const tbody = document.getElementById('tablaHorario');
            tbody.innerHTML = `<tr><td colspan="6" class="text-center text-danger">
                Error al cargar el horario: ${error.message}
            </td></tr>`;
        });
}