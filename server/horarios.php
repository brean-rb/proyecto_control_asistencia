<?php
// Este archivo devuelve el horario del profesor que ha iniciado sesión.
// Se usa para mostrar el horario en la página principal del sistema.

// Desactivar la salida de errores de PHP
error_reporting(0);
ini_set('display_errors', 0);

// Asegurar que siempre se devuelva JSON
header('Content-Type: application/json');

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

$response = ['success' => false, 'message' => '', 'horario' => []];

try {
    // Verificar el token
    $tokenData = verificarToken();

    // Obtiene el DNI del profesor desde el token
    $dni = mysqli_real_escape_string($conexion, $tokenData['id']);

    // Consulta para obtener el horario del profesor
    $sql = "SELECT 
                hg.dia_setmana,
                hg.hora_desde,
                hg.hora_fins,
                c.nom_val AS asignatura,
                hg.grup AS grupo,         
                hg.aula AS aula
            FROM horari_grup hg
            LEFT JOIN continguts c ON c.codi = hg.contingut
            WHERE hg.docent = '$dni'
            ORDER BY FIELD(hg.dia_setmana, 'L', 'M', 'X', 'J', 'V'), hg.hora_desde";

    $result = mysqli_query($conexion, $sql);

    if (!$result) {
        throw new Exception('Error en la consulta: ' . mysqli_error($conexion));
    }

    // Recorre los resultados y los guarda en un array
    while ($row = mysqli_fetch_assoc($result)) {
        $response['horario'][] = $row;
    }

    $response['success'] = true;

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
} catch (Error $e) {
    $response['message'] = 'Error interno del servidor: ' . $e->getMessage();
}

// Devuelve la respuesta en formato JSON
echo json_encode($response);
exit();
