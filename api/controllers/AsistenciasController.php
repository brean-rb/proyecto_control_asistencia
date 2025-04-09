<?php
require_once __DIR__ . '/../config/database.php';

class AsistenciasController {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = new Database();
        $this->conn = $this->db->getConnection();
    }

    // Obtener todas las asistencias
    public function getAll() {
        try {
            $query = "SELECT r.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                        FROM registro_jornada r 
                        LEFT JOIN docent d ON r.documento = d.document";
            
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

    // Obtener asistencias por docente
    public function getByDocente($documento) {
        try {
            $query = "SELECT r.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                        FROM registro_jornada r 
                        LEFT JOIN docent d ON r.documento = d.document 
                        WHERE r.documento = '$documento'";
            
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

    // Registrar nueva asistencia
    public function registrar($data) {
        try {
            $query = "INSERT INTO registro_jornada (documento, fecha, hora_entrada, hora_salida) 
                        VALUES ('".$data['documento']."', '".$data['fecha']."', 
                               '".$data['hora_entrada']."', '".$data['hora_salida']."')";
            
            if($this->conn->query($query)) {
                return [
                    'success' => true,
                    'message' => 'Asistencia registrada correctamente',
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

    // Consultar asistencias por fecha
    public function getByFecha($fecha) {
        try {
            $query = "SELECT r.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
                        FROM registro_jornada r 
                        LEFT JOIN docent d ON r.documento = d.document 
                        WHERE DATE(r.fecha) = '$fecha'";
            
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

    // Actualizar asistencia
    public function actualizar($id, $data) {
        try {
            $query = "UPDATE registro_jornada 
                        SET hora_entrada = '".$data['hora_entrada']."', 
                            hora_salida = '".$data['hora_salida']."' 
                        WHERE id = '$id'";
            
            if($this->conn->query($query)) {
                return [
                    'success' => true,
                    'message' => 'Asistencia actualizada correctamente',
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
}
?>