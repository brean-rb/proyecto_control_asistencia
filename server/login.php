<?php
session_start();
require_once __DIR__ . '/config/config.php';

// Verificar si se enviaron los datos
if (!isset($_POST['dni']) || !isset($_POST['password'])) {
    header('Location: ../client/src/login.php?error=3');
    exit();
}

$dni = mysqli_real_escape_string($conexion, trim($_POST['dni']));
$password = mysqli_real_escape_string($conexion, trim($_POST['password']));

// Verificar conexión
if (!$conexion) {
    header('Location: ../client/src/login.php?error=4');
    exit();
}

// Consulta modificada para obtener  el nombre del docente
$sql = "SELECT u.*, d.nom, d.cognom1, d.cognom2 
        FROM usuarios u 
        LEFT JOIN docent d ON u.documento = d.document 
        WHERE u.documento = '$dni'";
$result = mysqli_query($conexion, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    // Verificar password
    if ($row && $row['password'] === $password) {
        $_SESSION['dni'] = trim($row['documento']);
        $_SESSION['rol'] = $row['rol'];
        // Guardar el nombre completo en la sesión
        $_SESSION['nombre_completo'] = trim($row['nom'] . ' ' . $row['cognom1'] . ' ' . $row['cognom2']);
        $_SESSION['autenticado'] = true;
        header('Location: ../client/src/index.php');
        exit();
    }
}

// Si llegamos aquí, hubo error de autenticación
header('Location: ../client/src/login.php?error=1');
exit();
