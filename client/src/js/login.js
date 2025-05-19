document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('loginForm');
    const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));

    // Función para mostrar el error en el modal
    function mostrarError(mensaje) {
        document.querySelector('.error-message').textContent = mensaje;
        errorModal.show();
    }

    // Función para manejar el login
    async function handleLogin(e) {
        e.preventDefault();
        
        const dni = document.getElementById('dni').value;
        const password = document.getElementById('password').value;

        if (!dni || !password) {
            mostrarError('Por favor, complete todos los campos');
            return;
        }

        try {
            // Crear un objeto FormData para enviar los datos
            const formData = new FormData();
            formData.append('dni', dni);
            formData.append('password', password);

            const response = await fetch(`${CONFIG.API_URL}?accion=${CONFIG.ACCIONES.LOGIN}`, {
                method: 'POST',
                body: formData
            });

            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }

            const data = await response.json();
            console.log('Respuesta del servidor:', data); // Para depuración

            if (data.status === 'success' && data.token) {
                // Guardar el token en localStorage
                localStorage.setItem('token', data.token);
                localStorage.setItem('user', JSON.stringify(data.user));
                
                // Verificar que el token sea válido antes de redirigir
                try {
                    const verifyResponse = await fetch(`${CONFIG.API_URL}?accion=verify_token`, {
                        headers: {
                            'Authorization': `Bearer ${data.token}`
                        }
                    });
                    
                    if (!verifyResponse.ok) {
                        throw new Error('Error al verificar el token');
                    }

                    const verifyData = await verifyResponse.json();
                    console.log('Respuesta de verificación:', verifyData); // Para depuración
                    
                    if (verifyData.status === 'success') {
                        // Redirigir al usuario según su rol
                        window.location.href = 'index.php';
                    } else {
                        mostrarError('Error de autenticación');
                        localStorage.removeItem('token');
                        localStorage.removeItem('user');
                    }
                } catch (error) {
                    console.error('Error al verificar token:', error);
                    mostrarError('Error al verificar la autenticación');
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                }
            } else {
                mostrarError(data.message || 'Error al iniciar sesión');
            }
        } catch (error) {
            console.error('Error:', error);
            mostrarError('Error de conexión con el servidor');
        }
    }

    // Evento submit del formulario
    form.addEventListener('submit', handleLogin);

    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const iconEye = document.getElementById('icon-eye');

    togglePassword.addEventListener('click', function() {
        const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
        passwordInput.setAttribute('type', type);
        iconEye.classList.toggle('fa-eye');
        iconEye.classList.toggle('fa-eye-slash');
    });
}); 