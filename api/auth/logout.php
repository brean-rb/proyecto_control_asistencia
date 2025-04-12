<?php
require_once __DIR__ . '/../utils/config.php';

// Destruir sesión si existe
session_unset();
session_destroy();

echo json_encode(['mensaje' => 'Sesión cerrada correctamente']);
