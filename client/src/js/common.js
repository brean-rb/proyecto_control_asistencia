// Este archivo contiene funciones comunes que se usan en todas las páginas

document.addEventListener('DOMContentLoaded', function() {
    // Verificar si el usuario está autenticado
    if (!CONFIG.isAuthenticated()) {
        window.location.href = 'login.php';
        return;
    }

    // Manejar la visibilidad del nav según el rol
    CONFIG.handleNavVisibility();

    // Manejar el logout
    const logoutForm = document.getElementById('logoutForm');
    if (logoutForm) {
        logoutForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const response = await CONFIG.fetch(`${CONFIG.API_URL.replace('index.php', 'logout.php')}`);
                
                // Verificar si la respuesta es JSON
                const contentType = response.headers.get('content-type');
                if (!contentType || !contentType.includes('application/json')) {
                    throw new Error('La respuesta del servidor no es JSON válido');
                }

                // Intentar parsear la respuesta JSON
                let data;
                try {
                    data = await response.json();
                } catch (error) {
                    console.error('Error al parsear la respuesta:', error);
                    throw new Error('Error al procesar la respuesta del servidor');
                }
                
                if (data.status === 'success') {
                    // Limpiar el localStorage
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    
                    // Redirigir al login
                    window.location.href = 'login.php';
                } else if (response.status === 401) {
                    // Si el token no es válido, limpiar y redirigir
                    localStorage.removeItem('token');
                    localStorage.removeItem('user');
                    window.location.href = 'login.php';
                } else {
                    throw new Error(data.message || 'Error al cerrar sesión');
                }
            } catch (error) {
                console.error('Error:', error);
                // Si hay un error, intentar limpiar y redirigir de todos modos
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = 'login.php';
            }
        });
    }
}); 