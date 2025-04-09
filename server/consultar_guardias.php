<?php
session_start();
require_once __DIR__ . '/config/config.php';

$response = ['success' => false, 'message' => '', 'guardias' => []];

try {
    $fecha = $_POST['fecha'];

    // Consulta para obtener las ausencias del día
    $sql = "SELECT h.grup, h.aula, h.contingut, h.sessio_orde, h.dia_setmana, 
                    h.hora_desde AS hora_inicio, h.hora_fins AS hora_fin, 
                    a.documento AS docente_ausente, 
                    CONCAT(d.nom, ' ', d.cognom1, ' ', d.cognom2) AS nombre_docente
            FROM horari_grup h
            LEFT JOIN ausencias a ON h.docent = a.documento 
                AND a.fecha_inicio = '$fecha'
            LEFT JOIN docent d ON h.docent = d.document
            WHERE a.documento IS NOT NULL
            AND NOT EXISTS (
                SELECT 1 
                FROM horari_grup h2
                WHERE h2.grup = h.grup 
                    AND h2.aula = h.aula 
                    AND h2.docent != a.documento
            )
            ORDER BY h.hora_desde ASC";

    $resultado = mysqli_query($conexion, $sql);

    if ($resultado) {
        $guardias = [];
        while ($row = mysqli_fetch_assoc($resultado)) {
            $guardias[] = [
                'grupo' => $row['grup'],
                'aula' => $row['aula'],
                'contenido' => $row['contingut'],
                'sesion_orden' => $row['sessio_orde'],
                'dia_semana' => $row['dia_setmana'],
                'hora_inicio' => $row['hora_inicio'],
                'hora_fin' => $row['hora_fin'],
                'docente_ausente' => $row['nombre_docente']
            ];
        }

        $response['success'] = true;
        $response['guardias'] = $guardias;
    } else {
        throw new Exception(mysqli_error($conexion));
    }
} catch (Exception $e) {
    $response['message'] = 'Error: ' . $e->getMessage();
}

header('Content-Type: application/json');
echo json_encode($response);
exit();