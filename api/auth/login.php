<?php
require_once __DIR__ . '/../utils/config.php';

// Leer los datos del JSON recibido
$data = json_decode(file_get_contents('php://input'), true);

// Comprobar si vienen document y password
if (!isset($data['document']) || !isset($data['password'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Faltan campos']);
    exit;
}

$document = $data['document'];
$password = $data['password'];

// Consulta a la base de datos
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE document = :document LIMIT 1");
$stmt->execute(['document' => $document]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Validar
if ($user && $user['password'] === $password) { 
    $_SESSION['usuario'] = $user['document'];
    $_SESSION['rol'] = $user['rol'];
    echo json_encode(['mensaje' => 'Login correcto', 'rol' => $user['rol']]);
} else {
    http_response_code(401);
    echo json_encode(['error' => 'Credenciales incorrectas']);
}
