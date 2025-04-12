document.addEventListener('DOMContentLoaded', async () => {
    try {
        const response = await fetch('/api/guardias/index.php');
        const data = await response.json();

        if (response.ok) {
            const tbody = document.querySelector('#tablaGuardias tbody');
            tbody.innerHTML = '';

            data.forEach(guardia => {
                const fila = `
                    <tr>
                        <td>${guardia.documento}</td>
                        <td>${guardia.fecha}</td>
                        <td>${guardia.hora}</td>
                        <td>${guardia.aula}</td>
                        <td>${guardia.motivo}</td>
                    </tr>`;
                tbody.innerHTML += fila;
            });
        } else {
            alert(data.error || 'No se pudieron cargar las guardias');
        }
    } catch (error) {
        alert('Error de conexión');
    }
});
