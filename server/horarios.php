<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Verificar autenticación
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: ../client/src/login.php?error=1');
    exit();
}

// Obtener DNI del profesor
$dni = mysqli_real_escape_string($conexion, $_SESSION['dni']);

// Consulta SQL para obtener el horario
$sql = "SELECT 
            ho.dia_setmana,
            ho.hora_desde,
            ho.hora_fins,
            ho.ocupacio AS asignatura,
            ho.ensenyament AS grupo,
            ho.plantilla AS aula
        FROM horari_ocupacions ho
        WHERE ho.docent = '$dni'
        ORDER BY FIELD(ho.dia_setmana, 'L', 'M', 'X', 'J', 'V'), ho.hora_desde";

$result = mysqli_query($conexion, $sql);

if (!$result) {
    die("Error en la consulta: " . mysqli_error($conexion));
}

// Preparar resultado
$horarios = [];
while ($row = mysqli_fetch_assoc($result)) {
    $horarios[] = $row;
}

// Devolver JSON
header('Content-Type: application/json');
echo json_encode($horarios);