<?php
session_start();

// Si no hay sesión activa, redirigir al login
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}

$usuario = $_SESSION['usuario'];
$rol = $_SESSION['rol'];
?>

<!DOCTYPE html>
<html lang="es">
    <head>
        <meta charset="UTF-8">
        <title>Panel Principal</title>
        <link rel="stylesheet" href="css/styles.css">
    </head>
    <body>
        <h2>Bienvenido, <?php echo htmlspecialchars($usuario); ?></h2>
        <p>Rol: <?php echo htmlspecialchars($rol); ?></p>

        <button id="logoutBtn">Cerrar sesión</button>

        <script src="js/app.js"></script>
    </body>
</html>
