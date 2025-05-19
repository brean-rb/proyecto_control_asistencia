<?php
// Este archivo gestiona el inicio de sesión de los usuarios (profesores y administradores).
// Verifica los datos, inicia la sesión y redirige según el resultado.

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/Authentication.php';

// Verificar si se enviaron los datos del formulario
if (!isset($_POST['dni']) || !isset($_POST['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Faltan datos de autenticación']);
    exit();
}

$dni = mysqli_real_escape_string($conexion, trim($_POST['dni']));
$password = mysqli_real_escape_string($conexion, trim($_POST['password']));

// Verificar conexión a la base de datos
if (!$conexion) {
    echo json_encode(['status' => 'error', 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Consulta para obtener los datos del usuario y su nombre completo
$sql = "SELECT u.*, d.nom, d.cognom1, d.cognom2 
        FROM usuarios u 
        LEFT JOIN docent d ON u.documento = d.document 
        WHERE u.documento = '$dni'";
$result = mysqli_query($conexion, $sql);

if ($row = mysqli_fetch_assoc($result)) {
    // Verificar que la contraseña sea correcta usando password_verify
    if ($row && password_verify($password, $row['password'])) {
        // Crear instancia de Authentication
        $auth = new Authentication();
        
        // Generar token JWT
        $token = $auth->generaToken($row['documento'], $row['rol']);
        
        // Registrar el inicio de sesión en un archivo de texto
        $log = date('Y-m-d H:i:s') . " - " . $row['documento'] . " inició sesión\n";
        file_put_contents(__DIR__ . '/registro_sesion.txt', $log, FILE_APPEND);

        // Devolver respuesta con el token y datos del usuario
        echo json_encode([
            'status' => 'success',
            'token' => $token,
            'user' => [
                'dni' => trim($row['documento']),
                'rol' => $row['rol'],
                'nombre_completo' => trim($row['nom'] . ' ' . $row['cognom1'] . ' ' . $row['cognom2'])
            ]
        ]);
        exit();
    }
}

// Si llegamos aquí, hubo error de autenticación
echo json_encode(['status' => 'error', 'message' => 'Credenciales inválidas']);
exit();
