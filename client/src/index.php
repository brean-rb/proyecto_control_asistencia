<?php
session_start();

// Verificar si el usuario está autenticado
if (!isset($_SESSION['autenticado']) || $_SESSION['autenticado'] !== true) {
    header('Location: ./login.php');  // Cambiado de login.html a login.php
    exit();
}

// Tomamos el rol de la sesión para decidir qué mostrar
$rolUsuario = $_SESSION['rol'] ?? 'profesor'; // por defecto 'profesor'
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Control de Asistencia y Guardias</title>
    <link rel="icon" type="image/png" href="./img/favi.png">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Bootstrap CSS (Local) -->
    <link rel="stylesheet" href="../vendor/bootstrap-5.0.2-dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- CSS -->
    <link rel="stylesheet" href="./css/styles.css">
</head>
<body class="bg-light">

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light border-bottom mb-4">
        <div class="container">
            <!-- Logo / Nombre del centro -->
            <a class="navbar-brand text-dark fw-bold flex-shrink-0" href="index.php">
                IES Joan Coromines
            </a>

            <!-- Botón hamburguesa -->
            <button 
                class="navbar-toggler ms-2" 
                type="button" 
                data-bs-toggle="collapse" 
                data-bs-target="#navbarMain" 
                aria-controls="navbarMain" 
                aria-expanded="false" 
                aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <!-- Menú colapsable (opciones + logout) -->
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav mx-auto align-items-center">
                    <?php if (
                        $rolUsuario === 'admin'
                    ): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="consulta_guardias.php">Guardias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="guardias_realizadas.php">Guardias realizadas</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="adminDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                Administración
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="adminDropdown">
                                <li><a class="dropdown-item" href="consulta_asistencia.php">Consulta Asistencia</a></li>
                                <li><a class="dropdown-item" href="registro_ausencia.php">Registro Ausencia</a></li>
                                <li><a class="dropdown-item" href="informe_ausencias.php">Informe Ausencias</a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link px-4" href="consulta_guardias.php">Guardias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link px-4" href="guardias_realizadas.php">Guardias realizadas</a>
                        </li>
                    <?php endif; ?>
                </ul>
                <form action="../../server/logout.php" method="POST" class="d-flex ms-lg-auto mt-3 mt-lg-0">
                    <button type="submit" class="btn btn-danger">
                        log out <i class="fas fa-sign-out-alt ms-2"></i>
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Contenido principal -->
    <div class="container py-4"> <!-- Añadido padding vertical -->
        <div class="row justify-content-center">
            <div class="col-lg-8"> <!-- Contenido más centrado -->
                <h2 class="text-center mb-4">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre_completo'] ?? $_SESSION['dni']); ?></h2>
                
                <!-- Botones para inicio/fin de jornada -->
                <div class="d-flex justify-content-center gap-4 mb-5">
                    <a href="../../server/registrar_jornada.php?accion=inicio" 
                        class="btn btn-inicio-jornada">
                        <i class="fas fa-play me-2"></i>Inicio de jornada
                    </a>
                    <a href="../../server/registrar_jornada.php?accion=fin" 
                        class="btn btn-fin-jornada">
                        <i class="fas fa-stop me-2"></i>Finalizar jornada
                    </a>
                </div>

                <!-- Título de horario -->
                <h3 class="text-center fw-bold mb-4">Su horario</h3>

                <!-- Después de tu h3 "Su horario" -->
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Día</th>
                                <th>Hora Inicio</th>
                                <th>Hora Fin</th>
                                <th>Asignatura</th>
                                <th>Grupo</th>
                                <th>Aula</th>
                            </tr>
                        </thead>
                        <tbody id="tablaHorario">
                            <!-- Aquí se cargarán los datos dinámicamente -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- JS de Bootstrap local -->
    <script src="../vendor/bootstrap-5.0.2-dist/js/bootstrap.bundle.min.js"></script>
    <!-- Tu JavaScript personalizado -->
    <script src="./js/app.js"></script>
</body>
</html>
