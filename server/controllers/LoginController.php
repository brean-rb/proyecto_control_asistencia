<?php
require_once __DIR__ . '/../config/DatabaseConnection.php';

class LoginController {
    public static function login($documento, $password) {
        try {
            session_start();
            
            $db = DatabaseConnection::getInstance();
            $conn = $db->getConnection();

            // Simplificamos la consulta SQL
            $sql = "SELECT * FROM usuarios WHERE documento = '$documento' AND password = '$password'";
            $result = $conn->query($sql);
            $user = $result->fetch();

            if ($user) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['documento'] = $user['documento'];
                $_SESSION['rol'] = $user['rol'];

                return ['success' => true, 'message' => 'Login exitoso'];
            } else {
                return ['success' => false, 'message' => 'Usuario o contraseña incorrectos'];
            }
        } catch (PDOException $e) {
            return ['success' => false, 'message' => 'Error de conexión'];
        }
    }

    public static function logout() {
        session_start();
        session_destroy();
        return ['success' => true, 'message' => 'Sesión cerrada'];
    }
}
?>