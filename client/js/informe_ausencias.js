document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/api/ausencias/index.php');
        const data = await response.json();

        if (response.ok) {
            const tbody = document.querySelector('#tablaAusencias tbody');
            tbody.innerHTML = '';

            data.forEach(ausencia => {
                const fila = `
                    <tr>
                        <td>${ausencia.documento}</td>
                        <td>${ausencia.fecha_inicio}</td>
                        <td>${ausencia.fecha_fin}</td>
                        <td>${ausencia.hora_inicio}</td>
                        <td>${ausencia.hora_fin}</td>
                        <td>${ausencia.motivo}</td>
                    </tr>`;
                tbody.innerHTML += fila;
            });
        } else {
            alert(data.error || 'No se pudieron cargar las ausencias');
        }
    } catch (error) {
        alert('Error de conexión');
    }
});
