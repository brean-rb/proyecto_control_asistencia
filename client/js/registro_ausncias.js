document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('formAusencia');

    form.addEventListener('submit', async (e) => {
        e.preventDefault();

        const datos = {
            fecha_inicio: document.getElementById('fecha_inicio').value,
            fecha_fin: document.getElementById('fecha_fin').value,
            hora_inicio: document.getElementById('hora_inicio').value,
            hora_fin: document.getElementById('hora_fin').value,
            motivo: document.getElementById('motivo').value
        };

        try {
            const response = await fetch('/api/ausencias/store.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(datos)
            });

            const data = await response.json();

            if (response.ok) {
                document.getElementById('mensaje').textContent = 'Ausencia registrada correctamente';
                form.reset();
            } else {
                document.getElementById('mensaje').textContent = data.error || 'Error al registrar la ausencia';
            }
        } catch (error) {
            document.getElementById('mensaje').textContent = 'Error de conexión con el servidor';
        }
    });
});
