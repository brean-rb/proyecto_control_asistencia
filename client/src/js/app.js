// Este archivo controla la página principal del sistema de asistencia y guardias.
// Aquí se gestionan los botones de inicio/fin de jornada y la carga del horario del profesor.

document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM cargado');
    
    // Evita que el menú de administración recargue la página al hacer clic
    document.querySelector('#adminDropdown')?.addEventListener('click', function(e) {
        e.preventDefault();
    });

    // Mejora la experiencia en pantallas táctiles para el menú de administración
    if('ontouchstart' in document.documentElement) {
        document.querySelector('#adminDropdown')?.addEventListener('click', function() {
            this.parentElement.classList.toggle('show');
            document.querySelector('.dropdown-menu').classList.toggle('show');
        });
    }

    // Muestra una ventana emergente con un mensaje
    function mostrarAlerta(mensaje) {
        const modal = new bootstrap.Modal(document.getElementById('alertModal'));
        document.getElementById('alertModalBody').innerHTML = mensaje;
        modal.show();
    }

    // Busca los botones de inicio y fin de jornada en la página
    const btnInicio = document.getElementById('btn-inicio-jornada');
    const btnFin = document.getElementById('btn-fin-jornada');
    
    console.log('Botones encontrados:', { btnInicio, btnFin });

    // Cuando se pulsa el botón de inicio de jornada
    if (btnInicio) {
        btnInicio.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Click en inicio jornada');
            // Llama al servidor para registrar el inicio de la jornada
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
    // Cuando se pulsa el botón de fin de jornada
    if (btnFin) {
        btnFin.addEventListener('click', function(e) {
            e.preventDefault();
            console.log('Click en fin jornada');
            // Llama al servidor para registrar el fin de la jornada
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

    // Carga el horario del profesor al entrar en la página
    cargarHorario();
});

// Esta función pide al servidor el horario del profesor y lo muestra en la tabla
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

            // Si no hay datos, muestra un mensaje
            if (!data || data.length === 0) {
                tbody.innerHTML = '<tr><td colspan="6" class="text-center">No hay horario disponible</td></tr>';
                return;
            }

            // Diccionario para traducir la letra del día a su nombre
            const diasSemana = {
                'L': 'Lunes',
                'M': 'Martes',
                'X': 'Miércoles',
                'J': 'Jueves',
                'V': 'Viernes'
            };

            // Calcula qué día es hoy para resaltarlo en la tabla
            const hoy = new Date();
            const diaActual = hoy.getDay(); // 1=Lunes, 2=Martes, 3=Miércoles, 4=Jueves, 5=Viernes
            const mapDia = {1: 'L', 2: 'M', 3: 'X', 4: 'J', 5: 'V'};

            // Recorre cada clase del horario y la añade a la tabla
            data.forEach(horario => {
                const esHoy = horario.dia_setmana === mapDia[diaActual];
                const tr = document.createElement('tr');
                if (esHoy) tr.classList.add('tr-dia-hoy'); // Resalta si es hoy
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