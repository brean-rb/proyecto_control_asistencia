<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

try {
    $sql = "SELECT * FROM ausencias ORDER BY fecha_inicio DESC";
    $resultado = $pdo->query($sql);
    $ausencias = $resultado->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($ausencias);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener las ausencias']);
}
