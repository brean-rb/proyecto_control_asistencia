<?php
// Este archivo verifica si el usuario tiene una jornada iniciada en el día actual.

// Desactivar la salida de errores de PHP
error_reporting(0);
ini_set('display_errors', 0);

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

// Asegurar que siempre devolvemos JSON
header('Content-Type: application/json');

$response = ['success' => false, 'jornada_iniciada' => false];

try {
    // Verificar el token
    $tokenData = verificarToken();
    
    if (!isset($conexion)) {
        throw new Exception('Error de conexión a la base de datos');
    }

    $dni = mysqli_real_escape_string($conexion, $tokenData['id']);
    $fecha = date('Y-m-d');

    // Consultar si hay una jornada iniciada para hoy
    $sql = "SELECT id FROM registro_jornada 
            WHERE documento = '$dni' 
            AND fecha = '$fecha' 
            AND hora_salida = '00:00:00' 
            LIMIT 1";

    $result = mysqli_query($conexion, $sql);

    if ($result === false) {
        throw new Exception('Error al consultar el estado de la jornada: ' . mysqli_error($conexion));
    }

    $response['success'] = true;
    $response['jornada_iniciada'] = (mysqli_num_rows($result) > 0);

} catch (Exception $e) {
    $response['mensaje'] = $e->getMessage();
}

// Asegurar que la salida es JSON válido
echo json_encode($response);
exit(); 