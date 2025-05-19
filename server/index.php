<?php
// server/index.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

require_once __DIR__.'/config/config.php';
require_once __DIR__.'/verify_token.php';

// Función para sanitizar inputs
function sanitizarInput($input) {
    return filter_var($input, FILTER_SANITIZE_STRING);
}

// Función para manejar errores
function manejarError($mensaje) {
    echo json_encode(['status' => 'error', 'message' => $mensaje]);
    exit();
}

// Función para manejar respuesta exitosa
function manejarExito($datos, $mensaje = '') {
    $respuesta = ['status' => 'success'];
    if ($mensaje) $respuesta['message'] = $mensaje;
    if ($datos) $respuesta['data'] = $datos;
    echo json_encode($respuesta);
    exit();
}

if(isset($_REQUEST['accion'])) {
    $accion = sanitizarInput($_REQUEST['accion']);
    
    // Verificar token para todas las acciones excepto login
    if ($accion !== 'login') {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
        
        if (!$token) {
            manejarError('No se proporcionó token de autenticación');
        }
        
        try {
            $auth = new Authentication();
            $error = $auth->validaToken();
            if ($error !== '') {
                manejarError($error);
            }
        } catch (Exception $e) {
            manejarError('Token inválido o expirado');
        }
    }
    
    switch($accion) {
        case 'verify_token':
            $auth = new Authentication();
            $error = $auth->validaToken();
            if ($error === '') {
                manejarExito(null, 'Token válido');
            } else {
                manejarError($error);
            }
            break;
            
        // Acciones GET
        case 'horarios':
            include 'horarios.php';
            break;
            
        case 'guardias':
            include 'consultar_guardias.php';
            break;
            
        case 'asistencia':
            include 'consultar_asistencia.php';
            break;
            
        case 'docentes':
            include 'listar_docentes.php';
            break;
            
        case 'horario_profesor':
            include 'obtener_horario_profesor.php';
            break;
            
        case 'horario_ausente':
            include 'obtener_horario_ausente.php';
            break;
            
        case 'guardias_realizadas':
            include 'obtener_guardias_realizadas.php';
            break;
            
        case 'estado_jornada':
            include 'estado_jornada.php';
            break;
            
        // Acciones POST
        case 'inicio':
        case 'fin':
            include 'registrar_jornada.php';
            break;
            
        case 'guardia':
            include 'registrar_guardia.php';
            break;
            
        case 'ausencia':
            include 'procesar_ausencia.php';
            break;
            
        case 'login':
            include 'login.php';
            break;
            
        case 'logout':
            include 'logout.php';
            break;
            
        case 'informe':
            include 'generar_informe.php';
            break;
            
        default:
            manejarError('Acción no reconocida');
    }
} else {
    manejarError('No se especificó una acción');
}
