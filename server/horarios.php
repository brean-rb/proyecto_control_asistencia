<?php
// Este archivo devuelve el horario del profesor que ha iniciado sesión.
// Se usa para mostrar el horario en la página principal del sistema.

session_start();
require_once __DIR__ . '/config/config.php';

// Verifica que el usuario esté autenticado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

// Obtiene el DNI del profesor desde la sesión
$dni = mysqli_real_escape_string($conexion, $_SESSION['dni']);

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
    // Si hay un error en la consulta, lo devuelve en formato JSON
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

$horarios = [];
// Recorre los resultados y los guarda en un array
while ($row = mysqli_fetch_assoc($result)) {
    $horarios[] = $row;
}

// Devuelve el horario en formato JSON
header('Content-Type: application/json');
echo json_encode($horarios);
exit();
