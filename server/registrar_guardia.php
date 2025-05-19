<?php
// Este archivo registra una guardia realizada por un profesor.
// Recibe los datos de la guardia, verifica que no esté duplicada y la guarda en la base de datos.
// Devuelve el resultado en formato JSON.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    // Verificar el token
    $tokenData = verificarToken();
    
    // Verificar que el usuario tenga permisos para registrar guardias
    if ($tokenData['rol'] !== 'admin' && $tokenData['rol'] !== 'profesor') {
        throw new Exception('No tienes permisos para registrar guardias');
    }

    // Escapar todos los valores recibidos del formulario
    $fecha = mysqli_real_escape_string($conexion, $_POST['fecha']);
    $hora_inicio = mysqli_real_escape_string($conexion, $_POST['hora_inicio']);
    $hora_fin = mysqli_real_escape_string($conexion, $_POST['hora_fin']);
    $grupo = mysqli_real_escape_string($conexion, $_POST['grupo']);
    $aula = mysqli_real_escape_string($conexion, $_POST['aula']);
    $docente_ausente = mysqli_real_escape_string($conexion, $_POST['docente_ausente']);
    $docente_guardia = mysqli_real_escape_string($conexion, $_POST['docente_guardia']);
    $contenido = mysqli_real_escape_string($conexion, $_POST['contenido']);
    $dia_semana = ['', 'L', 'M', 'X', 'J', 'V'][date('N', strtotime($fecha))];
    
    // Generar un identificador único para la guardia
    $horario_grupo = $fecha . '_' . $hora_inicio . '_' . $grupo;

    // Verificar si la guardia ya está reservada para ese día, hora y grupo
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

    // Insertar la nueva guardia en la base de datos
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

// Devuelve la respuesta en formato JSON
echo json_encode($response);
exit();