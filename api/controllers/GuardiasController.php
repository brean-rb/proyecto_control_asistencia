<?php
require_once __DIR__ . '/../config/database.php';

class GuardiasController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Obtener todas las guardias disponibles
    public function getAll() {
        try {
            $query = "SELECT g.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre_docente,
                             a.aula, h.hora_inicio, h.hora_fin
                     FROM horari_ocupacions g
                     LEFT JOIN docent d ON g.documento = d.document
                     LEFT JOIN aules a ON g.codi_aula = a.codi
                     LEFT JOIN horari_grup h ON g.id_horario = h.id
                     WHERE g.tipo = 'GUARDIA'
                     ORDER BY h.hora_inicio ASC";
            
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

    // Obtener guardias por docente
    public function getByDocente($documento) {
        try {
            $query = "SELECT g.*, h.hora_inicio, h.hora_fin, a.aula
                     FROM horari_ocupacions g
                     LEFT JOIN horari_grup h ON g.id_horario = h.id
                     LEFT JOIN aules a ON g.codi_aula = a.codi
                     WHERE g.documento = :documento AND g.tipo = 'GUARDIA'
                     ORDER BY h.hora_inicio ASC";
            
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

    // Registrar una guardia realizada
    public function registrarGuardia($data) {
        try {
            $query = "INSERT INTO registro_guardias 
                     (documento_docente, fecha, hora_inicio, hora_fin, aula, observaciones) 
                     VALUES (:documento, :fecha, :hora_inicio, :hora_fin, :aula, :observaciones)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':documento', $data['documento']);
            $stmt->bindParam(':fecha', $data['fecha']);
            $stmt->bindParam(':hora_inicio', $data['hora_inicio']);
            $stmt->bindParam(':hora_fin', $data['hora_fin']);
            $stmt->bindParam(':aula', $data['aula']);
            $stmt->bindParam(':observaciones', $data['observaciones']);
            
            if($stmt->execute()) {
                return [
                    'success' => true,
                    'message' => 'Guardia registrada correctamente',
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

    // Obtener guardias realizadas por fecha
    public function getGuardiasRealizadas($fecha) {
        try {
            $query = "SELECT r.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre_docente 
                     FROM registro_guardias r
                     LEFT JOIN docent d ON r.documento_docente = d.document
                     WHERE DATE(r.fecha) = :fecha
                     ORDER BY r.hora_inicio ASC";
            
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