document.getElementById('start-work-btn').addEventListener('click', async () => {
    try {
        const response = await fetch('../../server/api/jornada.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: 'start' })
        });

        const data = await response.json();
        if (data.success) {
            alert('Jornada iniciada correctamente');
        } else {
            alert('Error al iniciar la jornada: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al iniciar la jornada');
    }
});

document.getElementById('end-work-btn').addEventListener('click', async () => {
    try {
        const response = await fetch('../../server/api/jornada.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ action: 'end' })
        });

        const data = await response.json();
        if (data.success) {
            alert('Jornada finalizada correctamente');
        } else {
            alert('Error al finalizar la jornada: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al finalizar la jornada');
    }
});

document.getElementById('logout-btn').addEventListener('click', async () => {
    try {
        const response = await fetch('../../server/api/login.php?logout=true', {
            method: 'POST'
        });

        const data = await response.json();
        if (data.success) {
            window.location.href = 'login.php';
        } else {
            alert('Error al cerrar sesión: ' + data.message);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al cerrar sesión');
    }
});