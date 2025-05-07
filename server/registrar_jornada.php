<?php
// Este archivo gestiona el inicio y fin de la jornada laboral de un profesor.
// Permite registrar el inicio y fin de la jornada, tanto por petición normal como por AJAX (JSON).

session_start();
require_once __DIR__.'/config/config.php';

// Verificar que haya sesión
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    if ($_GET['format'] === 'json') {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'mensaje' => 'No autenticado.']);
    } else {
        header('Location: ../client/src/login.php?error=1');
    }
    exit();
}

$accion = $_GET['accion'] ?? '';
$dni    = mysqli_real_escape_string($conexion, $_SESSION['dni']);
$fecha  = date('Y-m-d');
$hora   = date('H:i:s');

// Si se solicita formato JSON, siempre devolver JSON
if ($_GET['format'] === 'json') {
    header('Content-Type: application/json');
    
    if ($accion === 'inicio') {
        // 1) Comprobar si ya hay un registro pendiente (sin hora de salida)
        $sqlCheck = "SELECT id FROM registro_jornada 
                        WHERE documento = '$dni' 
                        AND fecha = '$fecha' 
                        AND hora_salida = '00:00:00' 
                        LIMIT 1";
        
        $resCheck = mysqli_query($conexion, $sqlCheck);

        if (mysqli_num_rows($resCheck) > 0) {
            echo json_encode(['success' => false, 'mensaje' => 'Ya hay una jornada iniciada.']);
            exit();
        }

        // 2) Insertar nuevo inicio de jornada
        $sqlInsert = "INSERT INTO registro_jornada (documento, fecha, hora_entrada, hora_salida) 
                        VALUES ('$dni', '$fecha', '$hora', '00:00:00')";
        
        if (mysqli_query($conexion, $sqlInsert)) {
            echo json_encode(['success' => true, 'mensaje' => 'Jornada iniciada correctamente.']);
        } else {
            echo json_encode(['success' => false, 'mensaje' => 'Error al iniciar la jornada.']);
        }
        exit();

    } elseif ($accion === 'fin') {
        // Actualizar la hora de salida de la jornada abierta
        $sqlUpdate = "UPDATE registro_jornada 
                        SET hora_salida = '$hora' 
                        WHERE documento = '$dni' 
                        AND fecha = '$fecha' 
                        AND hora_salida = '00:00:00' 
                        ORDER BY id DESC 
                        LIMIT 1";
        
        $resultUpdate = mysqli_query($conexion, $sqlUpdate);

        if (mysqli_affected_rows($conexion) === 0) {
            echo json_encode(['success' => false, 'mensaje' => 'No hay ninguna jornada abierta para finalizar.']);
            exit();
        }

        echo json_encode(['success' => true, 'mensaje' => 'Jornada finalizada correctamente.']);
        exit();
    }

    echo json_encode(['success' => false, 'mensaje' => 'Acción inválida.']);
    exit();
}

// Si no es formato JSON, mantener el comportamiento original de redirección
if ($accion === 'inicio') {
    // 1) Comprobar si ya hay un registro pendiente (sin hora de salida)
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

    // 2) Insertar nuevo inicio de jornada
    $sqlInsert = "INSERT INTO registro_jornada (documento, fecha, hora_entrada, hora_salida) 
                    VALUES ('$dni', '$fecha', '$hora', '00:00:00')";
    
    mysqli_query($conexion, $sqlInsert);
    header('Location: ../client/src/index.php?mensaje=jornada-iniciada');
    exit();

} elseif ($accion === 'fin') {
    // Actualizar la hora de salida de la jornada abierta
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
