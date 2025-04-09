<?php
require_once __DIR__ . '/../config/database.php';

class AuthController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    public function login($documento, $password) {
        try {
            $query = "SELECT u.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre_completo 
                        FROM usuarios u
                        LEFT JOIN docent d ON u.documento = d.document 
                        WHERE u.documento = :documento";
            
            $result = $this->conn->query($query);
            
            $user = $result->fetch(PDO::FETCH_ASSOC);
            
            if ($user && $password === $user['password']) {
                // Registrar el inicio de sesión
                $this->registrarSesion($documento, 'inicio');
                
                // Devolver datos del usuario
                return [
                    'success' => true,
                    'data' => [
                        'documento' => $user['documento'],
                        'rol' => $user['rol'],
                        'nombre_completo' => $user['nombre_completo']
                    ],
                    'error' => null
                ];
            }
            
            return [
                'success' => false,
                'data' => null,
                'error' => 'Credenciales inválidas'
            ];
            
        } catch(PDOException $e) {
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    public function logout($documento) {
        try {
            // Registrar el cierre de sesión
            $this->registrarSesion($documento, 'cierre');
            
            return [
                'success' => true,
                'message' => 'Sesión cerrada correctamente',
                'error' => null
            ];
        } catch(Exception $e) {
            return [
                'success' => false,
                'message' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    private function registrarSesion($documento, $tipo) {
        $log = date('Y-m-d H:i:s') . " - " . $documento . " - " . $tipo . " de sesión\n";
        file_put_contents(__DIR__ . '/../../logs/registro_sesion.txt', $log, FILE_APPEND);
    }
}
?>