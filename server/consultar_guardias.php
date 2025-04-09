<?php
session_start();
require_once __DIR__ . '/config/config.php';

$response = ['success' => false, 'message' => '', 'profesores_ausentes' => []];

try {
    $fecha = date('Y-m-d'); // Fecha actual
    $dia_semana = date('N'); // 1 (lunes) a 7 (domingo)
    $dia_letra = ['', 'L', 'M', 'X', 'J', 'V'][intval($dia_semana)];

    // Consulta para obtener los profesores ausentes hoy
    $sql = "SELECT DISTINCT 
                a.documento,
                CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) as nombre_docente,
                a.motivo,
                a.fecha_inicio,
                a.fecha_fin
            FROM ausencias a
            INNER JOIN docent d ON a.documento = d.document
            WHERE '$fecha' BETWEEN a.fecha_inicio AND a.fecha_fin
            ORDER BY nombre_docente";

    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $response['profesores_ausentes'][] = [
                'documento' => $row['documento'],
                'nombre' => $row['nombre_docente'],
                'motivo' => $row['motivo'],
                'fecha_inicio' => $row['fecha_inicio'],
                'fecha_fin' => $row['fecha_fin']
            ];
        }
        $response['success'] = true;
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit();