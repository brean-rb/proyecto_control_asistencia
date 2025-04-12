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
    <title>Informe de Ausencias</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Informe de Ausencias</h2>

    <table id="tablaAusencias" border="1">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Fecha Inicio</th>
                <th>Fecha Fin</th>
                <th>Hora Inicio</th>
                <th>Hora Fin</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <a href="index.php">Volver</a>
    <script src="js/informe_ausencias.js"></script>
</body>
</html>
