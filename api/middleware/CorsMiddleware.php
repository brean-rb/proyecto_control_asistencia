<?php

class CorsMiddleware {
    public static function handleCors() {
        // Permitir solicitudes desde cualquier origen
        header('Access-Control-Allow-Origin: *');
        
        // Permitir métodos HTTP específicos
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        
        // Permitir ciertos headers en la solicitud
        header('Access-Control-Allow-Headers: Content-Type, Authorization');
        
        // Permitir credenciales
        header('Access-Control-Allow-Credentials: true');
        
        // Establecer tiempo de caché para respuestas preflight
        header('Access-Control-Max-Age: 86400'); // 24 horas

        // Manejar solicitudes OPTIONS (preflight)
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            header('HTTP/1.1 200 OK');
            exit();
        }
    }

    public static function setJsonHeader() {
        header('Content-Type: application/json; charset=UTF-8');
    }
}
?>