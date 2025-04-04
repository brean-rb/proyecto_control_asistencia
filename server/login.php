<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Verificar si se enviaron los datos
if (!isset($_POST['dni']) || !isset($_POST['password'])) {
    header('Location: ../client/src/login.phpl?error=3');
    exit();
}

$dni = mysqli_real_escape_string($conexion, trim($_POST['dni']));
$password = mysqli_real_escape_string($conexion, trim($_POST['password']));

// Verificar conexión
if (!$conexion) {
    header('Location: ../client/src/login.php?error=4');
    exit();
}

// Consulta en la tabla usuarios
$sql = "SELECT * FROM usuarios WHERE documento = '$dni'";
$result = mysqli_query($conexion, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    // Verificar password
    if ($row && $row['password'] === $password) {
        $_SESSION['dni'] = trim($row['documento']); // Asegúrate de que no hay espacios
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['autenticado'] = true;
        header('Location: ../client/src/index.php');
        exit();
    }
}

// Si llegamos aquí, hubo error de autenticación
header('Location: ../client/src/login.php?error=1');  // Cambiado de login.html a login.php
exit();
