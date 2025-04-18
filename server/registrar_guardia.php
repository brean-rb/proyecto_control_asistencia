<?php
session_start();
require_once __DIR__ . '/config/config.php';

$response = ['success' => false, 'message' => ''];

try {
    // Escapar todos los valores recibidos
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $hora_inicio = mysqli_real_escape_string($conexion, $_POST['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($conexion, $_POST['hora_fin']);
    $grupo = mysqli_real_escape_string($conexion, $_POST['grupo']);
    $aula = mysqli_real_escape_string($conexion, $_POST['aula']);
    $docente_ausente = mysqli_real_escape_string($conexion, $_POST['docente_ausente']);
    $docente_guardia = mysqli_real_escape_string($conexion, $_POST['docente_guardia']);
    $contenido = mysqli_real_escape_string($conexion, $_POST['contenido']);
    $dia_semana = ['', 'L', 'M', 'X', 'J', 'V'][date('N', strtotime($fecha))];
    
    // Generar horario_grupo
    $horario_grupo = $fecha . '_' . $hora_inicio . '_' . $grupo;

    // Verificar si la guardia ya está reservada
    $sql_check = "SELECT id FROM registro_guardias 
                  WHERE fecha = '$fecha' 
                  AND hora_inicio = '$hora_inicio' 
                  AND docente_ausente = '$docente_ausente'
                  AND grupo = '$grupo'";

    $result_check = mysqli_query($conexion, $sql_check);

    if (mysqli_num_rows($result_check) > 0) {
        $response['message'] = 'Esta guardia ya ha sido reservada';
        echo json_encode($response);
        exit();
    }

    $sql = "INSERT INTO registro_guardias (
                horario_grupo, fecha, docente_ausente, docente_guardia, 
                aula, grupo, contenido, dia_semana, hora_inicio, hora_fin
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, 'ssssssssss', 
        $horario_grupo, 
        $fecha, 
        $docente_ausente, 
        $docente_guardia, 
        $aula, 
        $grupo, 
        $contenido, 
        $dia_semana, 
        $hora_inicio, 
        $hora_fin
    );

    if (mysqli_stmt_execute($stmt)) {
        $response['success'] = true;
    } else {
        throw new Exception(mysqli_error($conexion));
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit();