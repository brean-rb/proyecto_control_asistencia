document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/api/asistencias/index.php');
        const data = await response.json();

        if (response.ok) {
            const tbody = document.querySelector('#tablaAsistencias tbody');
            tbody.innerHTML = '';

            data.forEach(asistencia => {
                const fila = `
                    <tr>
                        <td>${asistencia.document}</td>
                        <td>${asistencia.fecha}</td>
                        <td>${asistencia.hora}</td>a
                        <td>${asistencia.tipo}</td>
                    </tr>`;
                tbody.innerHTML += fila;
            });
        } else {
            alert(data.error || 'No se pudieron cargar las asistencias');
        }
    } catch (error) {
        alert('Error de conexión');
    }
});
