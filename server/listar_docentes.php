<?php
require_once __DIR__ . '/config/config.php';
header('Content-Type: application/json');

$resultado = ['success' => false, 'docentes' => []];
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
}
echo json_encode($resultado); 