<?php
// Este archivo devuelve el horario de un profesor para un día concreto.
// Se usa para mostrar las clases del profesor en el registro de ausencias y otros formularios.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => '', 'horario' => []];

try {
    // Verificar el token
    $tokenData = verificarToken();
    
    // Verificar que el usuario tenga permisos para ver horarios
    if ($tokenData['rol'] !== 'admin' && $tokenData['rol'] !== 'profesor') {
        throw new Exception('No tienes permisos para ver horarios');
    }

    // Obtener y validar los parámetros
    $documento = isset($_GET['documento']) ? mysqli_real_escape_string($conexion, $_GET['documento']) : null;
    $fecha = isset($_GET['fecha']) ? mysqli_real_escape_string($conexion, $_GET['fecha']) : null;

    if (!$documento || !$fecha) {
        throw new Exception('Faltan parámetros requeridos');
    }

    // Mapea el número de día de la semana a la letra usada en la base de datos
    $dia_mapping = [
        '1' => 'L',
        '2' => 'M',
        '3' => 'X',
        '4' => 'J',
        '5' => 'V'
    ];

    $dia_numero = date('N', strtotime($fecha));
    $dia_letra = $dia_mapping[$dia_numero];

    // Consulta para obtener el horario del profesor en ese día
    $sql = "SELECT 
                h.id,
                h.hora_desde as hora_inicio,
                h.hora_fins as hora_fin,
                h.grup as grupo,
                COALESCE(c.nom_val, h.contingut) as materia,
                h.aula
            FROM horari_grup h
            LEFT JOIN continguts c ON h.contingut = c.codi 
            WHERE h.docent = '$documento' 
            AND h.dia_setmana = '$dia_letra'
            ORDER BY h.hora_desde ASC";

    $resultado = mysqli_query($conexion, $sql);

    if (!$resultado) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conexion));
    }

    $horario = [];
    while ($row = mysqli_fetch_assoc($resultado)) {
        $horario[] = [
            'id' => $row['id'],
            'hora_inicio' => $row['hora_inicio'],
            'hora_fin' => $row['hora_fin'],
            'grupo' => $row['grupo'],
            'materia' => $row['materia'],
            'aula' => $row['aula']
        ];
    }

    $response['success'] = true;
    $response['horario'] = $horario;

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Devuelve la respuesta en formato JSON
echo json_encode($response);
exit();