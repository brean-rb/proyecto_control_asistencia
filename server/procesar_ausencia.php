<?php
session_start();
require_once __DIR__ . '/config/config.php';

if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: ../client/src/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = mysqli_real_escape_string($conexion, $_POST['documento']);
    $fecha_inicio = $_POST['fecha_inicio'];
    $tipo = $_POST['tipo'];
    
    if ($tipo === 'dia') {
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];
        $jornada_completa = 0;
    } else {
        $hora_inicio = '00:00:00';
        $hora_fin = '23:59:59';
        $jornada_completa = 1;
    }

    $sql = "INSERT INTO ausencias (documento, fecha_inicio, hora_inicio, hora_fin, jornada_completa) 
            VALUES (?, ?, ?, ?, ?)";
            
    $stmt = mysqli_prepare($conexion, $sql);
    mysqli_stmt_bind_param($stmt, "ssssi", $documento, $fecha_inicio, $hora_inicio, $hora_fin, $jornada_completa);
    
    if (mysqli_stmt_execute($stmt)) {
        header('Location: ../client/src/index.php?mensaje=ausencia-registrada');
    } else {
        header('Location: ../client/src/registro_ausencia.php?error=1');
    }
    exit();
}

header('Location: ../client/src/registro_ausencia.php');
exit();