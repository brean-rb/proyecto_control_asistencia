<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Ausencia</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Registrar Ausencia</h2>

    <form id="formAusencia">
        <label for="fecha_inicio">Fecha inicio:</label>
        <input type="date" id="fecha_inicio" name="fecha_inicio" required>

        <label for="fecha_fin">Fecha fin:</label>
        <input type="date" id="fecha_fin" name="fecha_fin" required>

        <label for="hora_inicio">Hora inicio:</label>
        <input type="time" id="hora_inicio" name="hora_inicio" required>

        <label for="hora_fin">Hora fin:</label>
        <input type="time" id="hora_fin" name="hora_fin" required>

        <label for="motivo">Motivo:</label>
        <input type="text" id="motivo" name="motivo" required>

        <button type="submit">Registrar</button>
    </form>

    <p id="mensaje"></p>

    <a href="index.php">Volver</a>
    <script src="js/registro_ausencia.js"></script>
</body>
</html>
