<?php
require_once __DIR__ . '/../config/database.php';

class AusenciasController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Obtener todas las ausencias
    public function getAll() {
        try {
            $query = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                        FROM ausencias a 
                        LEFT JOIN docent d ON a.documento = d.document";
            
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

    // Registrar nueva ausencia
    public function create($data) {
        try {
            $query = "INSERT INTO ausencias (documento, fecha_inicio, fecha_fin, 
                                            hora_inicio, hora_fin, motivo, 
                                            jornada_completa, justificada, registrado_por) 
                    VALUES (:documento, :fecha_inicio, :fecha_fin, 
                            :hora_inicio, :hora_fin, :motivo, 
                            :jornada_completa, :justificada, :registrado_por)";
            
            $stmt = $this->conn->prepare($query);
            
            $stmt->bindParam(':documento', $data['documento']);
            $stmt->bindParam(':fecha_inicio', $data['fecha_inicio']);
            $stmt->bindParam(':fecha_fin', $data['fecha_fin']);
            $stmt->bindParam(':hora_inicio', $data['hora_inicio']);
            $stmt->bindParam(':hora_fin', $data['hora_fin']);
            $stmt->bindParam(':motivo', $data['motivo']);
            $stmt->bindParam(':jornada_completa', $data['jornada_completa']);
            $stmt->bindParam(':justificada', $data['justificada']);
            $stmt->bindParam(':registrado_por', $data['registrado_por']);
            
            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Ausencia registrada correctamente',
                    'error' => null
                ];
            }
        } catch(PDOException $e) {
            return [
                'success' => false,
                'message' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    // Obtener ausencias por docente
    public function getByDocente($documento) {
        try {
            $query = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                    FROM ausencias a 
                    LEFT JOIN docent d ON a.documento = d.document 
                    WHERE a.documento = :documento";
            
            $result = $this->conn->query($query);
            
            return [
                'success' => true,
                'data' => $result->fetchAll(PDO::FETCH_ASSOC),
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

    // Obtener ausencias por fecha
    public function getByFecha($fecha) {
        try {
            $query = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                    FROM ausencias a 
                    LEFT JOIN docent d ON a.documento = d.document 
                    WHERE DATE(a.fecha_inicio) = :fecha";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':fecha', $fecha);
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
}
?>