// Configuración global de la aplicación
const CONFIG = {
    // URL base del servidor
    API_URL: 'http://localhost/proyecto_control_asistencia/server/index.php',
    
    // Acciones disponibles en la API
    ACCIONES: {
        LOGIN: 'login',
        LOGOUT: 'logout',
        HORARIOS: 'horarios',
        GUARDIAS: 'guardias',
        GUARDIAS_REALIZADAS: 'guardias_realizadas',
        ASISTENCIA: 'asistencia',
        DOCENTES: 'docentes',
        HORARIO_PROFESOR: 'horario_profesor',
        HORARIO_AUSENTE: 'horario_ausente',
        INICIO_JORNADA: 'inicio',
        FIN_JORNADA: 'fin',
        ESTADO_JORNADA: 'estado',
        AUSENCIA: 'ausencia',
        INFORME: 'informe'
    },

    // Mensajes de error comunes
    MENSAJES: {
        ERROR_CONEXION: 'Error de conexión con el servidor',
        ERROR_SERVIDOR: 'Error en el servidor',
        ERROR_AUTENTICACION: 'Error de autenticación',
        ERROR_PERMISOS: 'No tienes permisos para realizar esta acción'
    },

    // Verificar si el usuario está autenticado
    isAuthenticated: function() {
        return localStorage.getItem('token') !== null;
    },

    // Obtener el usuario actual
    getUser: function() {
        const userStr = localStorage.getItem('user');
        return userStr ? JSON.parse(userStr) : null;
    },

    // Verificar si el usuario es admin
    isAdmin: function() {
        const user = this.getUser();
        return user && user.rol === 'admin';
    },

    // Manejar la visibilidad de elementos según el rol
    handleNavVisibility: function() {
        try {
            const isAdmin = this.isAdmin();
            const adminElements = document.querySelectorAll('.admin-only');
            
            if (!adminElements || adminElements.length === 0) {
                console.warn('No se encontraron elementos con la clase admin-only');
                return;
            }

            adminElements.forEach(element => {
                if (element) {
                    element.style.display = isAdmin ? 'block' : 'none';
                }
            });
        } catch (error) {
            console.warn('Error al manejar la visibilidad del nav:', error);
        }
    },

    // Función fetch personalizada que incluye el token
    fetch: function(url, options = {}) {
        const token = localStorage.getItem('token');
        if (!token) {
            throw new Error('No hay token de autenticación');
        }

        // Fusionar headers correctamente
        const defaultHeaders = {
            'Authorization': `Bearer ${token}`,
            'Content-Type': 'application/json'
        };

        options.headers = {
            ...defaultHeaders,
            ...(options.headers || {})
        };

        return fetch(url, options);
    }
}; 