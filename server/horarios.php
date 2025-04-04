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
            ho.dia_setmana,
            ho.hora_desde,
            ho.hora_fins,
            c.nom_val AS asignatura,
            ho.ensenyament AS grupo,
            a.aula AS aula
        FROM horari_ocupacions ho
        LEFT JOIN continguts c ON c.codi = ho.ocupacio
        LEFT JOIN aules a ON a.codi = ho.plantilla
        WHERE ho.docent = '$dni'
        ORDER BY FIELD(ho.dia_setmana, 'L', 'M', 'X', 'J', 'V'), ho.hora_desde";

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
