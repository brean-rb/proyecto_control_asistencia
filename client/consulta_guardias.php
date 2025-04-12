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
    <title>Consulta de Guardias</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h2>Consulta de Guardias</h2>

    <table id="tablaGuardias" border="1">
        <thead>
            <tr>
                <th>Documento</th>
                <th>Fecha</th>
                <th>Hora</th>
                <th>Aula</th>
                <th>Motivo</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <a href="index.php">Volver</a>
    <script src="js/consulta_guardias.js"></script>
</body>
</html>
