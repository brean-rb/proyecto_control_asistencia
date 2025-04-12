<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validar campos requeridos
if (!isset($data['fecha_inicio']) || !isset($data['fecha_fin']) || !isset($data['hora_inicio']) || !isset($data['hora_fin']) || !isset($data['motivo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

$document = $_SESSION['usuario'];
$fecha_inicio = $data['fecha_inicio'];
$fecha_fin    = $data['fecha_fin'];
$hora_inicio  = $data['hora_inicio'];
$hora_fin     = $data['hora_fin'];
$motivo       = $data['motivo'];

try {
    $sql = "INSERT INTO ausencias (documento, fecha_inicio, fecha_fin, hora_inicio, hora_fin, motivo) 
            VALUES ('$document', '$fecha_inicio', '$fecha_fin', '$hora_inicio', '$hora_fin', '$motivo')";
    $pdo->query($sql);

    echo json_encode(['mensaje' => 'Ausencia registrada correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al registrar la ausencia']);
}
