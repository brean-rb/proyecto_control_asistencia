// Este archivo maneja la inicialización común de todas las páginas
document.addEventListener('DOMContentLoaded', function() {
    try {
        // Verificar si el usuario está autenticado
        if (!CONFIG.isAuthenticated()) {
            window.location.href = 'login.php';
            return;
        }

        // Obtener el usuario actual
        const user = CONFIG.getUser();
        if (!user) {
            console.warn('No se pudo obtener la información del usuario');
            return;
        }

        // Actualizar el nombre de usuario solo en la página principal
        if (window.location.pathname.endsWith('index.php')) {
            const userNameElement = document.getElementById('userName');
            if (userNameElement) {
                userNameElement.textContent = user.nombre || 'Usuario';
            }
        }

        // Manejar la visibilidad del nav según el rol
        try {
            CONFIG.handleNavVisibility();
        } catch (error) {
            console.warn('Error al manejar la visibilidad del nav:', error);
        }

        // Manejar el logout
        const logoutForm = document.getElementById('logoutForm');
        if (logoutForm) {
            logoutForm.addEventListener('submit', async function(e) {
                e.preventDefault();
                
                try {
                    console.log('Iniciando proceso de logout...');
                    const response = await fetch(`${CONFIG.API_URL}?accion=logout`, {
                        method: 'POST',
                        headers: {
                            'Authorization': `Bearer ${localStorage.getItem('token')}`,
                            'Content-Type': 'application/json'
                        }
                    });
                    
                    console.log('Respuesta recibida:', response);
                    console.log('Status:', response.status);
                    console.log('Headers:', response.headers);
                    
                    // Verificar si la respuesta es JSON
                    const contentType = response.headers.get('content-type');
                    console.log('Content-Type:', contentType);
                    
                    if (!contentType || !contentType.includes('application/json')) {
                        throw new Error('La respuesta del servidor no es JSON válido');
                    }

                    // Obtener el texto de la respuesta primero
                    const responseText = await response.text();
                    console.log('Respuesta texto:', responseText);
                    
                    // Intentar parsear la respuesta JSON
                    let data;
                    try {
                        data = JSON.parse(responseText);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                        throw new Error('Error al procesar la respuesta del servidor');
                    }
                    
                    console.log('Datos parseados:', data);
                    
                    if (data.status === 'success') {
                        localStorage.removeItem('token');
                        localStorage.removeItem('user');
                        window.location.href = 'login.php';
                    } else {
                        throw new Error(data.message || 'Error al cerrar sesión');
                    }
                } catch (error) {
                    alert('Error al cerrar sesión: ' + error.message);
                }
            });
        } else {
            console.warn('Formulario de logout no encontrado');
        }
    } catch (error) {
        console.warn('Error en la inicialización de la página:', error);
    }
}); 