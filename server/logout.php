<?php
// Este archivo cierra la sesión del usuario y lo redirige al login.
// También registra el cierre de sesión en un archivo de texto.

session_start();
if (isset($_SESSION['dni'])) {
    // Registrar en registro_sesion.txt que el usuario cierra sesión
    $log = date('Y-m-d H:i:s') . " - ".$_SESSION['dni']." cerró sesión\n";
    file_put_contents(__DIR__ . '/registro_sesion.txt', $log, FILE_APPEND);
}
// Destruir la sesión y limpiar los datos
session_destroy();
// Redirigir al login
header('Location: ../client/src/login.php');
exit;
