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
    <title>Consulta de Asistencias</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Consulta de Asistencias</h2>

    <table id="tablaAsistencias" border="1">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Tipo</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <a href="index.php">Volver</a>
    <script src="js/consulta_asistencia.js"></script>
</body>
</html>
