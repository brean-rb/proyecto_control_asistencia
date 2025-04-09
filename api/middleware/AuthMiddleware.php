<?php

class AuthMiddleware {
    public static function verificarSesion() {
        session_start();
        
        if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'No autorizado',
                'redirect' => './login.php'
            ]);
            exit();
        }
    }

    public static function verificarAdmin() {
        self::verificarSesion();
        
        if ($_SESSION['rol'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Acceso denegado - Se requieren permisos de administrador',
                'redirect' => './index.php'
            ]);
            exit();
        }
    }

    public static function verificarProfesor() {
        self::verificarSesion();
        
        if ($_SESSION['rol'] !== 'profesor' && $_SESSION['rol'] !== 'admin') {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => 'Acceso denegado - Se requieren permisos de profesor',
                'redirect' => './index.php'
            ]);
            exit();
        }
    }
}
?>