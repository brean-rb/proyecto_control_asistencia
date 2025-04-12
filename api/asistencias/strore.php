<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!isset($data['fecha']) || !isset($data['hora']) || !isset($data['tipo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

$document = $_SESSION['usuario'];
$fecha = $data['fecha'];
$hora = $data['hora'];
$tipo = $data['tipo'];

try {
    $sql = "INSERT INTO asistencias (document, fecha, hora, tipo) 
            VALUES ('$document', '$fecha', '$hora', '$tipo')";
    $pdo->query($sql);

    echo json_encode(['mensaje' => 'Asistencia registrada correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al registrar asistencia']);
}
