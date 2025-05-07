<?php
// Este archivo procesa el registro de una ausencia de un docente.
// Recibe los datos del formulario, los guarda en la base de datos y redirige según el resultado.

session_start();
require_once __DIR__ . '/config/config.php';

// Solo permite el acceso a usuarios autenticados y con rol de admin
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true || $_SESSION['rol'] !== 'admin') {
    header('Location: ../client/src/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $documento = mysqli_real_escape_string($conexion, $_POST['documento']);
    $tipo = $_POST['tipo'];
    $motivo = mysqli_real_escape_string($conexion, $_POST['motivo']);
    $registrado_por = $_SESSION['dni'];
    $justificada = isset($_POST['justificada']) ? 1 : 0;

    if ($tipo === 'dia') {
        // Si la ausencia es de un solo día, recoge las horas seleccionadas
        $fecha = $_POST['fecha'];
        $hora_inicio = $_POST['hora_inicio'];
        $hora_fin = $_POST['hora_fin'];

        $sql = "INSERT INTO ausencias (
                    documento, 
                    fecha_inicio, 
                    fecha_fin,
                    hora_inicio, 
                    hora_fin, 
                    motivo,
                    jornada_completa,
                    justificada,
                    registrado_por
                ) VALUES (
                    '$documento', 
                    '$fecha', 
                    '$fecha', 
                    '$hora_inicio', 
                    '$hora_fin', 
                    '$motivo', 
                        0,
                    $justificada,
                    '$registrado_por'
                )";

    } else {
        // Si la ausencia es de varios días, no se guardan horas concretas
        $fecha_inicio = $_POST['fecha_inicio'];
        $fecha_fin = $_POST['fecha_fin'];

        $sql = "INSERT INTO ausencias (
                    documento, 
                    fecha_inicio, 
                    fecha_fin,
                    motivo,
                    jornada_completa,
                    justificada,
                    registrado_por
                ) VALUES (
                    '$documento', 
                    '$fecha_inicio', 
                    '$fecha_fin', 
                    '$motivo', 
                    1,
                    $justificada,
                    '$registrado_por'
                )";
    }

    // Ejecuta la consulta y redirige según el resultado
    if (mysqli_query($conexion, $sql)) {
        header('Location: ../client/src/registro_ausencia.php?exito=1');
        exit();
    } else {
        header('Location: ../client/src/registro_ausencia.php?error=1');
        exit();
    }
}

// Si no es POST, redirige al formulario
header('Location: ../client/src/registro_ausencia.php');
exit();