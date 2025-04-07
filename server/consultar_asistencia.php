<?php
session_start();
require_once __DIR__ . '/config/config.php';

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$response = ['success' => false, 'message' => '', 'asistencias' => []];

try {
    $tipo_consulta = $_POST['tipo_consulta'];
    $tipo_fecha = $_POST['tipo_fecha'];
    
    $sql = "SELECT r.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
            FROM registro_jornada r 
            LEFT JOIN docent d ON r.documento = d.document 
            WHERE 1=1";

    // Filtrar por docente si es necesario
    if ($tipo_consulta === 'docente' && !empty($_POST['documento'])) {
        $documento = mysqli_real_escape_string($conexion, $_POST['documento']);
        $sql .= " AND r.documento = '$documento'";
    }

    // Filtrar por fecha
    if ($tipo_fecha === 'dia' && !empty($_POST['fecha'])) {
        $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
        $sql .= " AND DATE(r.fecha) = '$fecha'";
    } elseif ($tipo_fecha === 'mes' && !empty($_POST['mes'])) {
        $mes = mysqli_real_escape_string($conexion, $_POST['mes']);
        $sql .= " AND DATE_FORMAT(r.fecha, '%Y-%m') = '$mes'";
    }

    $sql .= " ORDER BY r.fecha DESC, r.hora_entrada ASC";

    $resultado = mysqli_query($conexion, $sql);
    
    if ($resultado) {
        $asistencias = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $asistencias[] = [
                'documento' => $row['documento'],
                'nombre' => $row['nombre'],
                'fecha' => date('d/m/Y', strtotime($row['fecha'])),
                'hora_entrada' => $row['hora_entrada'],
                'hora_salida' => $row['hora_salida']
            ];
        }
        
        $response['success'] = true;
        $response['asistencias'] = $asistencias;
    } else {
        throw new Exception(mysqli_error($conexion));
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit();