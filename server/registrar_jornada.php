<?php
session_start();
require_once __DIR__.'/config/config.php';

// Verificar que haya sesión
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: ../client/src/login.php?error=1');  // Cambiado de login.html a login.php
    exit();
}

$accion = $_GET['accion'] ?? '';
$dni    = mysqli_real_escape_string($conexion, $_SESSION['dni']);
$fecha  = date('Y-m-d');
$hora   = date('H:i:s');

if ($accion === 'inicio') {
    // 1) Comprobar si ya hay un registro pendiente
    $sqlCheck = "SELECT id FROM registro_jornada 
                    WHERE documento = '$dni' 
                    AND fecha = '$fecha' 
                    AND hora_salida = '00:00:00' 
                    LIMIT 1";
    
    $resCheck = mysqli_query($conexion, $sqlCheck);

    if (mysqli_num_rows($resCheck) > 0) {
        header('Location: ../client/src/index.php?error=jornada-ya-iniciada');
        exit();
    }

    // 2) Insertar nuevo inicio
    $sqlInsert = "INSERT INTO registro_jornada (documento, fecha, hora_entrada, hora_salida) 
                    VALUES ('$dni', '$fecha', '$hora', '00:00:00')";
    
    mysqli_query($conexion, $sqlInsert);
    header('Location: ../client/src/index.php?mensaje=jornada-iniciada');
    exit();

} elseif ($accion === 'fin') {
    // Actualizar hora_salida
    $sqlUpdate = "UPDATE registro_jornada 
                    SET hora_salida = '$hora' 
                    WHERE documento = '$dni' 
                    AND fecha = '$fecha' 
                    AND hora_salida = '00:00:00' 
                    ORDER BY id DESC 
                    LIMIT 1";
    
    $resultUpdate = mysqli_query($conexion, $sqlUpdate);

    if (mysqli_affected_rows($conexion) === 0) {
        header('Location: ../client/src/index.php?error=no-jornada-abierta');
        exit();
    }

    header('Location: ../client/src/index.php?mensaje=jornada-finalizada');
    exit();
}

// Si llega aquí, acción inválida
header('Location: ../client/src/index.php');
exit();
