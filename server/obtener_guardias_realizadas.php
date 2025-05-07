<?php
// Este archivo devuelve la lista de guardias realizadas por el profesor que ha iniciado sesión.
// Permite filtrar por fecha y hora, y devuelve los resultados en formato JSON para mostrarlos en la tabla.

session_start();
require_once __DIR__ . '/config/config.php';

// Verificar que el usuario está autenticado
if (!isset($_SESSION['dni'])) {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'No hay sesión iniciada']);
    exit();
}

$response = ['success' => false, 'message' => '', 'guardias' => []];

try {
    // Recoge los filtros de fecha y hora enviados por GET
    $fecha = $_GET['fecha'] ?? date('Y-m-d');
    $hora = $_GET['hora'] ?? null;
    $dni_profesor = $_SESSION['dni'];

    // Consulta para obtener las guardias realizadas por el profesor
    $sql = "SELECT 
                g.id,
                g.fecha,
                TIME_FORMAT(g.hora_inicio, '%H:%i') as hora,
                TIME_FORMAT(g.hora_fin, '%H:%i') as hora_fin,
                g.docente_ausente,
                CONCAT(d1.nom, ' ', d1.cognom1, ' ', d1.cognom2) as nombre_ausente,
                g.docente_guardia,
                CONCAT(d2.nom, ' ', d2.cognom1, ' ', d2.cognom2) as nombre_guardia,
                g.grupo,
                g.aula,
                g.contenido as asignatura,
                g.horario_grupo,
                g.dia_semana
            FROM registro_guardias g
            LEFT JOIN docent d1 ON g.docente_ausente = d1.document
            LEFT JOIN docent d2 ON g.docente_guardia = d2.document
            WHERE g.docente_guardia = '$dni_profesor'";
    
    if ($fecha) {
        $sql .= " AND DATE(g.fecha) = '$fecha'";
    }
    
    if ($hora) {
        $sql .= " AND TIME(g.hora_inicio) = '$hora'";
    }

    // Ordena los resultados por fecha y hora
    $sql .= " ORDER BY g.fecha DESC, g.hora_inicio ASC";

    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        while ($row = mysqli_fetch_assoc($resultado)) {
            $response['guardias'][] = [
                'id_guardia' => $row['id'],
                'fecha' => $row['fecha'],
                'hora' => $row['hora'],
                'hora_fin' => $row['hora_fin'],
                'profesor_ausente' => $row['nombre_ausente'] ?: $row['docente_ausente'],
                'profesor_guardia' => $row['nombre_guardia'] ?: $row['docente_guardia'],
                'asignatura' => $row['asignatura'],
                'grupo' => $row['grupo'],
                'aula' => $row['aula'],
                'horario_grupo' => $row['horario_grupo'],
                'dia_semana' => $row['dia_semana']
            ];
        }
        $response['success'] = true;
    }

    // Debug: Agregar información adicional (puedes quitar esto en producción)
    $response['debug'] = [
        'dni_profesor' => $dni_profesor,
        'fecha_busqueda' => $fecha,
        'hora_busqueda' => $hora,
        'sql' => $sql,
        'num_resultados' => mysqli_num_rows($resultado)
    ];

} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

// Devuelve la respuesta en formato JSON
header('Content-Type: application/json');
echo json_encode($response);
exit(); 