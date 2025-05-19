<?php
// Este archivo gestiona el inicio y fin de la jornada laboral de un profesor.
// Permite registrar el inicio y fin de la jornada, tanto por petición normal como por AJAX (JSON).

require_once __DIR__.'/config/config.php';
require_once __DIR__ . '/verify_token.php';

// Siempre devolver JSON
header('Content-Type: application/json');
$response = ['success' => false, 'mensaje' => ''];

try {
    // Verificar el token
    $tokenData = verificarToken();

    $accion = $_GET['accion'] ?? '';
    $dni = mysqli_real_escape_string($conexion, $tokenData['id']);
    $fecha = date('Y-m-d');
    $hora = date('H:i:s');

    if ($accion === 'inicio') {
        // 1) Comprobar si ya hay un registro pendiente (sin hora de salida)
        $sqlCheck = "SELECT id FROM registro_jornada 
                        WHERE documento = '$dni' 
                        AND fecha = '$fecha' 
                        AND hora_salida = '00:00:00' 
                        LIMIT 1";
        
        $resCheck = mysqli_query($conexion, $sqlCheck);

        if (!$resCheck) {
            throw new Exception('Error al verificar jornada existente: ' . mysqli_error($conexion));
        }

        if (mysqli_num_rows($resCheck) > 0) {
            throw new Exception('Ya hay una jornada iniciada');
        }

        // 2) Insertar nuevo inicio de jornada
        $sqlInsert = "INSERT INTO registro_jornada (documento, fecha, hora_entrada, hora_salida) 
                        VALUES ('$dni', '$fecha', '$hora', '00:00:00')";
        
        if (!mysqli_query($conexion, $sqlInsert)) {
            throw new Exception('Error al iniciar la jornada: ' . mysqli_error($conexion));
        }

        $response['success'] = true;
        $response['mensaje'] = 'Jornada iniciada correctamente';

    } elseif ($accion === 'fin') {
        // Actualizar la hora de salida de la jornada abierta
        $sqlUpdate = "UPDATE registro_jornada 
                        SET hora_salida = '$hora' 
                        WHERE documento = '$dni' 
                        AND fecha = '$fecha' 
                        AND hora_salida = '00:00:00' 
                        ORDER BY id DESC 
                        LIMIT 1";
        
        if (!mysqli_query($conexion, $sqlUpdate)) {
            throw new Exception('Error al finalizar la jornada: ' . mysqli_error($conexion));
        }

        if (mysqli_affected_rows($conexion) === 0) {
            throw new Exception('No hay ninguna jornada abierta para finalizar');
        }

        $response['success'] = true;
        $response['mensaje'] = 'Jornada finalizada correctamente';

    } else {
        throw new Exception('Acción inválida');
    }

} catch (Exception $e) {
    $response['mensaje'] = $e->getMessage();
}

echo json_encode($response);
exit();
