<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

$data = json_decode(file_get_contents('php://input'), true);

// Validar campos requeridos
if (!isset($data['fecha']) || !isset($data['hora']) || !isset($data['aula']) || !isset($data['motivo'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan datos obligatorios']);
    exit;
}

$document = $_SESSION['usuario'];
$fecha = $data['fecha'];
$hora = $data['hora'];
$aula = $data['aula'];
$motivo = $data['motivo'];

try {
    $sql = "INSERT INTO guardias (documento, fecha, hora, aula, motivo) 
            VALUES ('$document', '$fecha', '$hora', '$aula', '$motivo')";
    $pdo->query($sql);

    echo json_encode(['mensaje' => 'Guardia registrada correctamente']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al registrar la guardia']);
}
