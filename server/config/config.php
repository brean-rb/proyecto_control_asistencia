<?php
// Configuración de la conexión a la base de datos
define('SERVER', 'localhost');
define('USER', 'root');
define('PASSWORD', '');
define('DATABASE', 'gestion_guardias_asistencias');

$conexion = @mysqli_connect(SERVER, USER, PASSWORD, DATABASE);

if (!$conexion) {
    die("Error de conexión: " . mysqli_connect_error());
} else {
    mysqli_set_charset($conexion, 'utf8');

}