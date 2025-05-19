<?php
// Este archivo devuelve la lista de todos los docentes para los desplegables del sistema.
// Se usa para cargar los nombres de los profesores en los formularios.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/verify_token.php';

header('Content-Type: application/json');

$resultado = ['success' => false, 'docentes' => []];

try {
    // Verificar el token
    $tokenData = verificarToken();
    
    // Verificar que el usuario tenga rol de administrador
    if ($tokenData['rol'] !== 'admin') {
        throw new Exception('No tienes permisos para acceder a esta información');
    }

    // Consulta para obtener el documento y el nombre completo de cada docente
    $sql = "SELECT document, CONCAT(nom, ' ', cognom1, ' ', cognom2) AS nombre FROM docent ORDER BY nom, cognom1, cognom2";
    $res = mysqli_query($conexion, $sql);
    
    if ($res) {
        while ($row = mysqli_fetch_assoc($res)) {
            $resultado['docentes'][] = [
                'document' => $row['document'],
                'nombre' => $row['nombre']
            ];
        }
        $resultado['success'] = true;
    } else {
        throw new Exception('Error al obtener la lista de docentes');
    }
} catch (Exception $e) {
    $resultado['message'] = $e->getMessage();
}

// Devuelve la lista de docentes en formato JSON
echo json_encode($resultado);