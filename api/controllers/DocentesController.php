<?php
require_once __DIR__ . '/../config/database.php';

class DocentesController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Obtener todos los docentes
    public function getAll() {
        try {
            $query = "SELECT document, CONCAT(nom, ' ', cognom1, ' ', cognom2) as nombre_completo 
                        FROM docent 
                        ORDER BY cognom1, cognom2, nom";
            
            $stmt = $this->conn->prepare($query);
            $stmt->execute();
            
            return [
                'success' => true,
                'data' => $stmt->fetchAll(PDO::FETCH_ASSOC),
                'error' => null
            ];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    // Obtener docente por DNI
    public function getByDNI($documento) {
        try {
            $query = "SELECT document, nom, cognom1, cognom2, tipo_doc, sexe, data_ingres 
                        FROM docent 
                        WHERE document = :documento";
            
            $result = $this->conn->query($query);
            
            return [
                'success' => true,
                'data' => $result->fetch(PDO::FETCH_ASSOC),
                'error' => null
            ];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'data' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    // Verificar si existe un docente
    public function exists($documento) {
        try {
            $query = "SELECT COUNT(*) as count 
                        FROM docent 
                        WHERE document = :documento";
            
            $result = $this->conn->query($query);
            
            $result = $result->fetch(PDO::FETCH_ASSOC);
            
            return [
                'success' => true,
                'exists' => $result['count'] > 0,
                'error' => null
            ];
        } catch(PDOException $e) {
            return [
                'success' => false,
                'exists' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>