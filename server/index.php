<?php
// server/index.php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Activar el manejo de errores
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__.'/config/config.php';
require_once __DIR__.'/Authentication.php';

// Función para sanitizar inputs
function sanitizarInput($input) {
    return filter_var($input, FILTER_SANITIZE_STRING);
}

// Función para manejar errores
function manejarError($mensaje, $code = 500) {
    http_response_code($code);
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

try {
    if(!isset($_REQUEST['accion'])) {
        manejarError('No se especificó una acción', 400);
    }

    $accion = sanitizarInput($_REQUEST['accion']);
    
    // Verificar token para todas las acciones excepto login
    if ($accion !== 'login') {
        $headers = getallheaders();
        $token = isset($headers['Authorization']) ? str_replace('Bearer ', '', $headers['Authorization']) : null;
        
        if (!$token) {
            manejarError('No se proporcionó token de autenticación', 401);
        }
        
        try {
            $auth = new Authentication();
            $error = $auth->validaToken();
            if ($error !== '') {
                manejarError($error, 401);
            }
        } catch (Exception $e) {
            manejarError('Token inválido o expirado', 401);
        }
    }
    
    switch($accion) {
        case 'verify_token':
            $auth = new Authentication();
            $error = $auth->validaToken();
            if ($error === '') {
                manejarExito(null, 'Token válido');
            } else {
                manejarError($error, 401);
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
            manejarError('Acción no reconocida', 400);
    }
} catch (Exception $e) {
    manejarError('Error en el servidor: ' . $e->getMessage());
} catch (Error $e) {
    manejarError('Error interno del servidor: ' . $e->getMessage());
} catch (Throwable $e) {
    manejarError('Error inesperado: ' . $e->getMessage());
}
