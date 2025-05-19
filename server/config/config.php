<?php
// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'gestion_guardias_asistencias');

// Desactivar la salida de errores de PHP
error_reporting(0);
ini_set('display_errors', 0);

// Crear conexión
$conexion = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Verificar conexión
if (!$conexion) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'mensaje' => 'Error de conexión a la base de datos: ' . mysqli_connect_error()
    ]);
    exit();
}

// Establecer charset
mysqli_set_charset($conexion, "utf8");