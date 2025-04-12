<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

// Comprobar si viene el documento del profesor ausente por GET
if (!isset($_GET['documento'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Falta el documento del profesor ausente']);
    exit;
}

$documento = $_GET['documento'];

try {
    $sql = "SELECT * FROM horarios WHERE documento = '$documento' ORDER BY dia, hora";
    $resultado = $pdo->query($sql);
    $horario = $resultado->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($horario);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener el horario del profesor ausente']);
}
