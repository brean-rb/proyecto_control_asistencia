<?php
// Este archivo procesa el registro de una ausencia de un docente.
// Recibe los datos del formulario, los guarda en la base de datos y devuelve el resultado en JSON.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

try {
    // Verificar el token
    $tokenData = verificarToken();
    
    // Verificar que el usuario tenga rol de administrador
    if ($tokenData['rol'] !== 'admin') {
        throw new Exception('No tienes permisos para registrar ausencias');
    }

    // Obtener los datos del cuerpo de la petición
    $json = file_get_contents('php://input');
    $datos = json_decode($json, true);

    if (!$datos) {
        throw new Exception('Datos inválidos');
    }

    $documento = mysqli_real_escape_string($conexion, $datos['documento']);
    $tipo = $datos['tipo'];
    $motivo = mysqli_real_escape_string($conexion, $datos['motivo']);
    $registrado_por = $tokenData['id'];
    $justificada = isset($datos['justificada']) ? 1 : 0;

    if ($tipo === 'dia') {
        // Si la ausencia es de un solo día, recoge las horas seleccionadas
        $fecha = $datos['fecha'];
        $clases = $datos['clases'];

        if (empty($clases)) {
            throw new Exception('Debe seleccionar al menos una clase');
        }

        // Insertar cada clase seleccionada
        foreach ($clases as $clase) {
            $hora_inicio = mysqli_real_escape_string($conexion, $clase['hora_inicio']);
            $hora_fin = mysqli_real_escape_string($conexion, $clase['hora_fin']);

            $sql = "INSERT INTO ausencias (
                        documento, 
                        fecha_inicio, 
                        fecha_fin,
                        hora_inicio, 
                        hora_fin, 
                        motivo,
                        jornada_completa,
                        justificada,
                        registrado_por
                    ) VALUES (
                        '$documento', 
                        '$fecha', 
                        '$fecha', 
                        '$hora_inicio', 
                        '$hora_fin', 
                        '$motivo', 
                        0,
                        $justificada,
                        '$registrado_por'
                    )";

            if (!mysqli_query($conexion, $sql)) {
                throw new Exception('Error al registrar la ausencia: ' . mysqli_error($conexion));
            }
        }
    } else {
        // Si la ausencia es de varios días, no se guardan horas concretas
        $fecha_inicio = $datos['fecha_inicio'];
        $fecha_fin = $datos['fecha_fin'];

        $sql = "INSERT INTO ausencias (
                    documento, 
                    fecha_inicio, 
                    fecha_fin,
                    motivo,
                    jornada_completa,
                    justificada,
                    registrado_por
                ) VALUES (
                    '$documento', 
                    '$fecha_inicio', 
                    '$fecha_fin', 
                    '$motivo', 
                    1,
                    $justificada,
                    '$registrado_por'
                )";

        if (!mysqli_query($conexion, $sql)) {
            throw new Exception('Error al registrar la ausencia: ' . mysqli_error($conexion));
        }
    }

    $response['success'] = true;
    $response['message'] = 'Ausencia registrada correctamente';

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit();