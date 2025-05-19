<?php
// Desactivar la visualización de errores
error_reporting(0);
ini_set('display_errors', 0);

// Establecer headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Función para enviar respuesta JSON
function sendJsonResponse($status, $message, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'status' => $status,
        'message' => $message
    ]);
    exit();
}

try {
    require_once __DIR__ . '/Authentication.php';

    // Obtener el token de la cabecera
    $headers = getallheaders();
    if (!isset($headers['Authorization'])) {
        sendJsonResponse('error', 'No se proporcionó token de autorización', 401);
    }

    $token = str_replace('Bearer ', '', $headers['Authorization']);
    if (empty($token)) {
        sendJsonResponse('error', 'Token vacío', 401);
    }

    // Registrar el cierre de sesión en el archivo de texto
    $auth = new Authentication();
    try {
        $decoded = $auth->decodeToken($token);
        if (isset($decoded['id'])) {
            $log = date('Y-m-d H:i:s') . " - " . $decoded['id'] . " cerró sesión\n";
            file_put_contents(__DIR__ . '/registro_sesion.txt', $log, FILE_APPEND);
        }
    } catch (Exception $e) {
        // Si el token es inválido, simplemente continuamos con el logout
    }

    // Devolver respuesta exitosa
    sendJsonResponse('success', 'Sesión cerrada correctamente');
} catch (Exception $e) {
    // Devolver respuesta de error
    sendJsonResponse('error', 'Error al cerrar sesión: ' . $e->getMessage(), 500);
}
