<?php
session_start();
if (isset($_SESSION['dni'])) {
    // Registrar en registro_sesion.txt que cierra sesión
    $log = date('Y-m-d H:i:s') . " - ".$_SESSION['dni']." cerró sesión\n";
    file_put_contents(__DIR__ . '/registro_sesion.txt', $log, FILE_APPEND);
}
// Destruir sesión
session_destroy();
// Redirigir al login
header('Location: ../client/src/login.php');
exit;
