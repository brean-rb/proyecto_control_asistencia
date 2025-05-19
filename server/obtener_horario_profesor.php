<?php
// Este archivo devuelve el horario de un profesor para un día concreto.
// Se usa para mostrar las clases del profesor en el registro de ausencias y otros formularios.

session_start();
require_once __DIR__ . '/config/config.php';

// Solo permite el acceso a usuarios autenticados
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$response = ['success' => false, 'message' => '', 'horario' => []];

try {
    $documento = $_POST['documento'];
    $fecha = $_POST['fecha'];

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
    $sql = "SELECT h.hora_desde, h.hora_fins, h.grup, h.contingut, h.aula, 
            COALESCE(c.nom_val, h.contingut) as nombre_asignatura 
            FROM horari_grup h
            LEFT JOIN continguts c ON h.contingut = c.codi AND h.ensenyament = c.ensenyament 
            WHERE h.docent = '$documento' 
            AND h.dia_setmana = '$dia_letra'
            ORDER BY h.hora_desde ASC";

    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        $horario = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $horario[] = [
                'hora_inicio' => $row['hora_desde'],
                'hora_fin' => $row['hora_fins'],
                'grupo' => $row['grup'],
                'asignatura' => $row['nombre_asignatura'],
                'aula' => $row['aula']
            ];
        }

        $response['success'] = true;
        $response['horario'] = $horario;
    } else {
        $response['message'] = 'Error en la consulta: ' . mysqli_error($conexion);
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
exit();