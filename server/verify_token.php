<?php
require_once __DIR__ . '/Authentication.php';

function verificarToken() {
    $auth = new Authentication();
    $error = $auth->validaToken();
    
    if ($error !== '') {
        http_response_code(401);
        echo json_encode(['status' => 'error', 'message' => $error]);
        exit();
    }
    
    return $auth->getDecodedToken();
}

// Función para obtener el token de la cabecera
function getTokenFromHeader() {
    $headers = getallheaders();
    if (isset($headers['Authorization'])) {
        $auth = $headers['Authorization'];
        $parts = explode(' ', $auth);
        if (count($parts) === 2 && $parts[0] === 'Bearer') {
            return $parts[1];
        }
    }
    return null;
}
?> 