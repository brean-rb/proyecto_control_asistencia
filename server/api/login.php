<?php
require_once __DIR__ . '/../controllers/LoginController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['documento']) && isset($data['password'])) {
        $response = LoginController::login($data['documento'], $data['password']);
        echo json_encode($response);
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    }
}
?>