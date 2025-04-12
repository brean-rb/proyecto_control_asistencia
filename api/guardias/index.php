<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

try {
    $sql = "SELECT * FROM guardias ORDER BY fecha DESC, hora DESC";
    $resultado = $pdo->query($sql);
    $guardias = $resultado->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode($guardias);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al obtener las guardias']);
}
