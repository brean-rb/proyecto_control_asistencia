<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

$document = $_SESSION['usuario'];

try {
    $sql = "SELECT * FROM horarios WHERE documento = '$document' ORDER BY dia, hora";
    $resultado = $pdo->query($sql);
    $horario = $resultado->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($horario);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener el horario del profesor']);
}
