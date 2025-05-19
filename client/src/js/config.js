// Configuración global de la aplicación
const CONFIG = {
    // URL base del servidor
    API_URL: 'http://localhost/proyecto_control_asistencia/server/index.php',
    
    // Acciones comunes
    ACCIONES: {
        ESTADO_JORNADA: 'estado_jornada',
        INICIO_JORNADA: 'inicio',
        FIN_JORNADA: 'fin',
        HORARIOS: 'horarios',
        GUARDIAS: 'guardias',
        ASISTENCIA: 'asistencia',
        DOCENTES: 'docentes',
        HORARIO_PROFESOR: 'horario_profesor',
        HORARIO_AUSENTE: 'horario_ausente',
        GUARDIAS_REALIZADAS: 'guardias_realizadas',
        AUSENCIA: 'ausencia',
        LOGIN: 'login',
        LOGOUT: 'logout',
        INFORME: 'informe'
    },

    // Mensajes comunes
    MENSAJES: {
        ERROR_CONEXION: 'Error de conexión con el servidor',
        ERROR_SERVIDOR: 'Error en el servidor',
        ERROR_DATOS: 'Error al procesar los datos',
        EXITO: 'Operación realizada con éxito'
    },

    // Función para obtener el token del localStorage
    getToken: function() {
        return localStorage.getItem('token');
    },

    // Función para verificar si el usuario está autenticado
    isAuthenticated: function() {
        return !!this.getToken();
    },

    // Función para obtener los datos del usuario
    getUser: function() {
        const user = localStorage.getItem('user');
        return user ? JSON.parse(user) : null;
    },

    // Función para verificar si el usuario es administrador
    isAdmin: function() {
        const user = this.getUser();
        return user && user.rol === 'admin';
    },

    // Función para manejar la visibilidad de elementos del nav
    handleNavVisibility: function() {
        const adminElements = document.querySelectorAll('.admin-only');
        const profesorElements = document.querySelectorAll('.profesor-only');
        
        if (this.isAdmin()) {
            adminElements.forEach(el => el.style.display = 'block');
            profesorElements.forEach(el => el.style.display = 'none');
        } else {
            adminElements.forEach(el => el.style.display = 'none');
            profesorElements.forEach(el => el.style.display = 'block');
        }
    },

    // Función para hacer peticiones al servidor con el token
    fetch: async function(url, options = {}) {
        const token = this.getToken();
        
        // Configurar headers por defecto
        const headers = {
            'Content-Type': 'application/json',
            ...options.headers
        };

        // Añadir el token si existe
        if (token) {
            headers['Authorization'] = `Bearer ${token}`;
        }

        try {
            const response = await fetch(url, {
                ...options,
                headers
            });

            // Si el token no es válido, redirigir al login
            if (response.status === 401) {
                localStorage.removeItem('token');
                localStorage.removeItem('user');
                window.location.href = '/proyecto_control_asistencia/client/src/login.php';
                return;
            }

            return response;
        } catch (error) {
            console.error('Error en la petición:', error);
            throw error;
        }
    }
}; 