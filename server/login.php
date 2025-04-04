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
    if ($row['password'] === $password) {
        // Login correcto
        $_SESSION['dni'] = $dni;
        $_SESSION['rol'] = $row['rol'];
        $_SESSION['autenticado'] = true;

        // Registrar en archivo de texto
        $registro = date('Y-m-d H:i:s') . " - $dni inició sesión\n";
        file_put_contents(__DIR__ . '/registro_sesion.txt', $registro, FILE_APPEND);

        // Redirigir
        header('Location: ../client/src/index.php');
        exit();
    }
}

// Si llegamos aquí, hubo error de autenticación
header('Location: ../client/src/login.php?error=1');  // Cambiado de login.html a login.php
exit();
