<?php
session_start();
require_once __DIR__ . '/../config/DatabaseConnection.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    if (isset($data['action'])) {
        $action = $data['action'];
        $userId = $_SESSION['user_id'];

        try {
            $db = DatabaseConnection::getInstance();
            $conn = $db->getConnection();

            if ($action === 'start') {
                $stmt = $conn->prepare("INSERT INTO registro_jornada (user_id, inicio) VALUES (?, NOW())");
                $stmt->execute([$userId]);
                echo json_encode(['success' => true, 'message' => 'Jornada iniciada']);
            } elseif ($action === 'end') {
                $stmt = $conn->prepare("UPDATE registro_jornada SET fin = NOW() WHERE user_id = ? AND fin IS NULL");
                $stmt->execute([$userId]);
                echo json_encode(['success' => true, 'message' => 'Jornada finalizada']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Acción no válida']);
            }
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Faltan datos']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
}
?>