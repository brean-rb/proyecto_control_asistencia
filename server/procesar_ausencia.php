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

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $documento = mysqli_real_escape_string($conexion, $_POST['documento']);
        $tipo = $_POST['tipo'];
        $motivo = mysqli_real_escape_string($conexion, $_POST['motivo']);
        $registrado_por = $tokenData['id'];
        $justificada = isset($_POST['justificada']) ? 1 : 0;

        if ($tipo === 'dia') {
            // Si la ausencia es de un solo día, recoge las horas seleccionadas
            $fecha = $_POST['fecha'];
            $hora_inicio = $_POST['hora_inicio'];
            $hora_fin = $_POST['hora_fin'];

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

        } else {
            // Si la ausencia es de varios días, no se guardan horas concretas
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];

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
        }

        // Ejecuta la consulta y devuelve el resultado
        if (mysqli_query($conexion, $sql)) {
            $response['success'] = true;
            $response['message'] = 'Ausencia registrada correctamente';
        } else {
            throw new Exception('Error al registrar la ausencia: ' . mysqli_error($conexion));
        }
    } else {
        throw new Exception('Método no permitido');
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
exit();