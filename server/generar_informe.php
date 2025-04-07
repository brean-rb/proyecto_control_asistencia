<?php
session_start();
require_once __DIR__ . '/config/config.php';

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$response = ['success' => false, 'message' => '', 'ausencias' => []];

try {
    $tipo_informe = $_POST['tipo_informe'];
    
    $sql = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
            FROM ausencias a 
            LEFT JOIN docent d ON a.documento = d.document 
            WHERE 1=1";

    switch ($tipo_informe) {
        case 'docente':
            if (!empty($_POST['documento'])) {
                $documento = mysqli_real_escape_string($conexion, $_POST['documento']);
                $sql .= " AND a.documento = '$documento'";
            }
            break;

        case 'dia':
            $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
            $sql .= " AND DATE(a.fecha_inicio) = '$fecha'";
            break;

        case 'semana':
            $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
            $sql .= " AND YEARWEEK(a.fecha_inicio, 1) = YEARWEEK('$fecha', 1)";
            break;

        case 'mes':
            $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
            $sql .= " AND MONTH(a.fecha_inicio) = MONTH('$fecha') 
                     AND YEAR(a.fecha_inicio) = YEAR('$fecha')";
            break;

        case 'trimestre':
            $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
            $sql .= " AND QUARTER(a.fecha_inicio) = QUARTER('$fecha')
                     AND YEAR(a.fecha_inicio) = YEAR('$fecha')";
            break;
    }

    $sql .= " ORDER BY a.fecha_inicio DESC";

    $resultado = mysqli_query($conexion, $sql);
    
    if ($resultado) {
        $ausencias = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $ausencias[] = [
                'nombre' => $row['nombre'],
                'fecha_inicio' => date('d/m/Y', strtotime($row['fecha_inicio'])),
                'fecha_fin' => date('d/m/Y', strtotime($row['fecha_fin'])),
                'motivo' => $row['motivo'],
                'justificada' => $row['justificada'] == 1  
            ];
        }
        
        $response['success'] = true;
        $response['ausencias'] = $ausencias;
    } else {
        throw new Exception(mysqli_error($conexion));
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit();