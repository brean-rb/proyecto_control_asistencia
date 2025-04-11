<?php
require_once 'db_config.php';

class DatabaseConnection {
    private static $instance = null; // Instancia única de la clase
    private $connection; // Conexión PDO

    // Constructor privado para evitar instanciación directa
    private function __construct() {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, // Manejo de errores con excepciones
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Devuelve resultados como arrays asociativos
                PDO::ATTR_EMULATE_PREPARES => false // Desactiva la emulación de consultas preparadas
            ];

            $this->connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('Error de conexión: ' . $e->getMessage());
        }
    }

    // Método para obtener la conexión PDO
    public function getConnection() {
        return $this->connection;
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
}
?>