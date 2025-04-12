document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('loginForm');

    if (form) {
        form.addEventListener('submit', async (e) => {
            e.preventDefault();

            const documentValue = document.getElementById('document').value;
            const passwordValue = document.getElementById('password').value;

            try {
                const response = await fetch('/api/auth/login.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({
                        document: documentValue,
                        password: passwordValue
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    // Redirigir o mostrar mensaje
                    document.getElementById('mensaje').textContent = 'Login correcto. Redirigiendo...';
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1000);
                } else {
                    document.getElementById('mensaje').textContent = data.error || 'Error de login';
                }

            } catch (error) {
                document.getElementById('mensaje').textContent = 'Error de conexión con el servidor';
            }
        });
    }

    // Código para el logout
    const logoutBtn = document.getElementById('logoutBtn');

    if (logoutBtn) {
        logoutBtn.addEventListener('click', async () => {
            try {
                const response = await fetch('/api/auth/logout.php');
                const data = await response.json();

                if (response.ok) {
                    window.location.href = 'login.php';
                } else {
                    alert(data.error || 'Error al cerrar sesión');
                }
            } catch (error) {
                alert('Error de conexión');
            }
        });
    }
});
