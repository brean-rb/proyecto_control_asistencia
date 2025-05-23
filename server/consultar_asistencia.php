<?php
// Este archivo recibe la petición para consultar asistencias desde el panel de administración.
// Permite buscar asistencias por docente, día o mes y devuelve los resultados en formato JSON.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'asistencias' => []];

// Si la petición es JSON, decodifica el body
if (stripos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $input = json_decode(file_get_contents('php://input'), true);
    if (is_array($input)) {
        $_POST = array_merge($_POST, $input);
    }
}

try {
    // Verificar el token
    $tokenData = verificarToken();
    
    // Verificar que el usuario tenga rol de administrador
    if ($tokenData['rol'] !== 'admin') {
        throw new Exception('No tienes permisos para consultar asistencias');
    }

    // Recoge los filtros enviados desde el formulario
    $tipo_consulta = $_POST['tipo_consulta'];
    $tipo_fecha = $_POST['tipo_fecha'];
    
    // Consulta base para buscar asistencias y el nombre del docente
    $sql = "SELECT r.*, CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre 
            FROM registro_jornada r 
            LEFT JOIN docent d ON r.documento = d.document 
            WHERE 1=1";

    // Si se busca por docente, añade el filtro
    if ($tipo_consulta === 'docente' && !empty($_POST['documento'])) {
        $documento = mysqli_real_escape_string($conexion, $_POST['documento']);
        $sql .= " AND r.documento = '$documento'";
    }

    // Si se busca por día o por mes, añade el filtro correspondiente
    if ($tipo_fecha === 'dia' && !empty($_POST['fecha'])) {
        $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
        $sql .= " AND DATE(r.fecha) = '$fecha'";
    } elseif ($tipo_fecha === 'mes' && !empty($_POST['mes'])) {
        $mes = mysqli_real_escape_string($conexion, $_POST['mes']);
        $sql .= " AND DATE_FORMAT(r.fecha, '%Y-%m') = '$mes'";
    }

    // Ordena los resultados por fecha y hora de entrada
    $sql .= " ORDER BY r.fecha DESC, r.hora_entrada ASC";

    $resultado = mysqli_query($conexion, $sql);
    
    if ($resultado) {
        $asistencias = [];
        // Recorre los resultados y los guarda en un array
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

// Devuelve la respuesta en formato JSON
echo json_encode($response);
exit();