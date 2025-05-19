<?php
// Este archivo genera un informe de ausencias según los filtros elegidos (docente, día, semana, mes o trimestre).
// Devuelve los resultados en formato JSON para mostrarlos en la tabla del informe.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'ausencias' => []];

try {
    // Verificar el token
    $tokenData = verificarToken();
    
    // Verificar que el usuario tenga rol de administrador
    if ($tokenData['rol'] !== 'admin') {
        throw new Exception('No tienes permisos para generar informes');
    }

    // Recoge el tipo de informe y los filtros enviados desde el formulario
    $tipo_informe = $_POST['tipo_informe'];
    
    // Consulta base para buscar ausencias y el nombre del docente
    $sql = "SELECT a.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
            FROM ausencias a 
            LEFT JOIN docent d ON a.documento = d.document 
            WHERE 1=1";

    // Según el tipo de informe, añade el filtro correspondiente
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

    // Ordena los resultados por fecha de inicio de la ausencia
    $sql .= " ORDER BY a.fecha_inicio DESC";

    $resultado = mysqli_query($conexion, $sql);
    
    if ($resultado) {
        $ausencias = [];
        // Recorre los resultados y los guarda en un array
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

// Devuelve la respuesta en formato JSON
echo json_encode($response);
exit();