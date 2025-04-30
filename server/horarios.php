<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Verificar autenticación
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Content-Type: application/json');
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

// Obtener DNI del profesor
$dni = mysqli_real_escape_string($conexion, $_SESSION['dni']);

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
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Error en la consulta: ' . mysqli_error($conexion)]);
    exit();
}

$horarios = [];
while ($row = mysqli_fetch_assoc($result)) {
    $horarios[] = $row;
}

header('Content-Type: application/json');
echo json_encode($horarios);
exit();
