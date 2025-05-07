// Este archivo controla la página donde se consultan las guardias ya realizadas.
// Permite filtrar por fecha y hora, y muestra los resultados en una tabla.

document.addEventListener('DOMContentLoaded', function() {
    // Referencias al formulario de filtro y a la tabla donde se muestran las guardias
    const formFiltro = document.getElementById('filtroGuardias');
    const tablaGuardias = document.getElementById('tablaGuardias').getElementsByTagName('tbody')[0];

    // Cargar guardias al cargar la página
    cargarGuardias();

    // Cuando se envía el formulario, recarga la tabla con los nuevos filtros
    formFiltro.addEventListener('submit', function(e) {
        e.preventDefault();
        cargarGuardias();
    });

    // Esta función pide al servidor las guardias realizadas según los filtros y las muestra en la tabla
    function cargarGuardias() {
        const fecha = document.getElementById('fecha').value;
        const hora = document.getElementById('hora').value;

        // Construir la URL con los parámetros
        let url = `../../server/obtener_guardias_realizadas.php?fecha=${fecha}`;
        if (hora) {
            url += `&hora=${hora}`;
        }

        // Limpiar la tabla
        tablaGuardias.innerHTML = '';

        // Mostrar mensaje de carga mientras llegan los datos
        const loadingRow = document.createElement('tr');
        loadingRow.innerHTML = `
            <td colspan="8" class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Cargando...</span>
                </div>
            </td>
        `;
        tablaGuardias.appendChild(loadingRow);

        // Realizar la petición al servidor
        fetch(url)
            .then(response => response.json())
            .then(data => {
                tablaGuardias.innerHTML = '';

                if (data.success) {
                    if (data.guardias.length === 0) {
                        // Si no hay datos, muestra un mensaje
                        const noDataRow = document.createElement('tr');
                        noDataRow.innerHTML = `
                            <td colspan="8" class="text-center">
                                No se encontraron guardias para los criterios seleccionados
                            </td>
                        `;
                        tablaGuardias.appendChild(noDataRow);
                    } else {
                        // Si hay datos, los muestra en la tabla
                        data.guardias.forEach(guardia => {
                            const row = document.createElement('tr');
                            row.innerHTML = `
                                <td>${guardia.fecha}</td>
                                <td>${guardia.hora}</td>
                                <td>${guardia.hora_fin}</td>
                                <td>${guardia.profesor_ausente}</td>
                                <td>${guardia.profesor_guardia}</td>
                                <td>${guardia.asignatura}</td>
                                <td>${guardia.grupo}</td>
                                <td>${guardia.aula}</td>
                            `;
                            tablaGuardias.appendChild(row);
                        });
                    }
                } else {
                    // Si hay un error, muestra el mensaje de error
                    const errorRow = document.createElement('tr');
                    errorRow.innerHTML = `
                        <td colspan="8" class="text-center text-danger">
                            Error: ${data.message}
                        </td>
                    `;
                    tablaGuardias.appendChild(errorRow);
                }
            })
            .catch(error => {
                // Si ocurre un error en la petición, muestra un mensaje de error
                tablaGuardias.innerHTML = '';
                const errorRow = document.createElement('tr');
                errorRow.innerHTML = `
                    <td colspan="8" class="text-center text-danger">
                        Error al cargar los datos: ${error.message}
                    </td>
                `;
                tablaGuardias.appendChild(errorRow);
            });
    }
}); 