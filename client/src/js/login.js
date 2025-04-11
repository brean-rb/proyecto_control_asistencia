document.getElementById('login-form').addEventListener('submit', async (e) => {
    e.preventDefault();

    const documento = document.getElementById('documento').value;
    const password = document.getElementById('password').value;

    try {
        const response = await fetch('../../server/api/login.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ documento, password })
        });

        const data = await response.json();

        const messageDiv = document.getElementById('login-message');
        if (data.success) {
            messageDiv.textContent = 'Login exitoso. Redirigiendo...';
            messageDiv.style.color = 'green';
            setTimeout(() => {
                window.location.href = './index.php'; // Redirigir a la página principal
            }, 2000);
        } else {
            messageDiv.textContent = data.message;
            messageDiv.style.color = 'red';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Error al iniciar sesión');
    }
});