<?php
require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/auth_middleware.php';

// Obtener todas las asistencias registradas
try {
    $stmt = $pdo->query("SELECT * FROM asistencias ORDER BY fecha DESC, hora DESC");
    $asistencias = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($asistencias);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Error al consultar asistencias']);
}
