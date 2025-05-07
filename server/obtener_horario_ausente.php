<?php
// Este archivo devuelve el horario de un profesor ausente para el día de hoy.
// Se usa para mostrar las clases que necesitan guardia y si ya están reservadas.

session_start();
require_once __DIR__ . '/config/config.php';

$response = ['success' => false, 'message' => '', 'horario' => []];

try {
    $documento = $_POST['documento'];
    $fecha = date('Y-m-d');
    $dia_letra = ['', 'L', 'M', 'X', 'J', 'V'][date('N')];

    // Consulta para obtener las clases del profesor ausente en el día de hoy
    $sql = "SELECT h.hora_desde, h.hora_fins, h.grup, h.contingut, h.aula,
                    COALESCE(c.nom_val, h.contingut) as nombre_asignatura,
                    rg.docente_guardia,
                    a.jornada_completa,
                    a.hora_inicio as ausencia_inicio,
                    a.hora_fin as ausencia_fin
            FROM horari_grup h
            LEFT JOIN continguts c ON h.contingut = c.codi 
                AND h.ensenyament = c.ensenyament
            INNER JOIN ausencias a ON a.documento = '$documento'
                AND '$fecha' BETWEEN a.fecha_inicio AND a.fecha_fin
            LEFT JOIN registro_guardias rg ON rg.fecha = '$fecha' 
                AND rg.hora_inicio = h.hora_desde 
                AND rg.docente_ausente = '$documento'
                AND rg.grupo = h.grup
            WHERE h.docent = '$documento' 
            AND h.dia_setmana = '$dia_letra'
            AND (
                a.jornada_completa = 1 
                OR 
                (a.jornada_completa = 0 AND h.hora_desde >= a.hora_inicio AND h.hora_fins <= a.hora_fin)
            )
            ORDER BY h.hora_desde ASC";

    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $response['horario'][] = [
                'hora_inicio' => $row['hora_desde'],
                'hora_fin' => $row['hora_fins'],
                'grupo' => $row['grup'],
                'aula' => $row['aula'],
                'asignatura' => $row['nombre_asignatura'],
                'reservada' => !is_null($row['docente_guardia']),
                'docente_guardia' => $row['docente_guardia'],
                'jornada_completa' => $row['jornada_completa'],
                'ausencia_inicio' => $row['ausencia_inicio'],
                'ausencia_fin' => $row['ausencia_fin']
            ];
        }
        $response['success'] = true;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
exit();